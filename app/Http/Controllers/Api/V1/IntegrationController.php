<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Rules\CpfCnpjRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IntegrationController extends Controller
{
    /**
     * Consulta dados cadastrais de Pessoa Jurídica via API pública da CNPJá.
     */
    public function cnpj(string $taxId): JsonResponse
    {
        $cleanCnpj = preg_replace('/\D/', '', $taxId);

        if (strlen($cleanCnpj) !== 14) {
            return response()->json([
                'success' => false,
                'message' => 'O CNPJ informado deve conter exatamente 14 dígitos numéricos.',
            ], 422);
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => 'RedeProntaERP/1.0 (dev.redepronta.com)',
                    'Accept' => 'application/json',
                ])
                ->get("https://open.cnpja.com/office/{$cleanCnpj}");

            if ($response->status() === 404) {
                return response()->json([
                    'success' => false,
                    'message' => 'CNPJ não encontrado na base pública da Receita Federal.',
                ], 404);
            }

            if (! $response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Serviço CNPJá temporariamente indisponível (HTTP ' . $response->status() . ').',
                ], 502);
            }

            $data = $response->json();

            // Extração de Cidade vinculada via código IBGE
            $cityId = null;
            $cityName = $data['address']['city'] ?? null;
            $stateCode = $data['address']['state'] ?? null;
            $ibgeCode = isset($data['address']['municipality']) ? (string) $data['address']['municipality'] : null;

            if ($ibgeCode) {
                $city = City::with('state')->where('ibge_code', $ibgeCode)->first();
                if ($city) {
                    $cityId = $city->id;
                    $cityName = $city->name;
                    $stateCode = $city->state?->code ?? $stateCode;
                }
            }

            if (! $cityId && $cityName && $stateCode) {
                $city = City::with('state')
                    ->where('name', 'like', $cityName)
                    ->whereHas('state', fn ($q) => $q->where('code', strtoupper($stateCode)))
                    ->first();
                if ($city) {
                    $cityId = $city->id;
                }
            }

            // Telefones
            $phone = null;
            if (! empty($data['phones']) && is_array($data['phones'])) {
                $firstPhone = $data['phones'][0];
                $phone = sprintf('(%s) %s', $firstPhone['area'] ?? '', $firstPhone['number'] ?? '');
            }

            // Emails
            $email = null;
            if (! empty($data['emails']) && is_array($data['emails'])) {
                $email = $data['emails'][0]['address'] ?? null;
            }

            // CEP Formatado
            $zip = $data['address']['zip'] ?? '';
            $cleanZip = preg_replace('/\D/', '', $zip);
            $formattedZip = strlen($cleanZip) === 8
                ? substr($cleanZip, 0, 5) . '-' . substr($cleanZip, 5, 3)
                : $zip;

            return response()->json([
                'success' => true,
                'source' => 'cnpja_open_api',
                'data' => [
                    'tax_id' => $cleanCnpj,
                    'name' => $data['company']['name'] ?? $data['name'] ?? '',
                    'trade_name' => $data['alias'] ?? '',
                    'status' => $data['status']['text'] ?? 'Ativa',
                    'status_date' => $data['statusDate'] ?? null,
                    'founded' => $data['founded'] ?? null,
                    'legal_nature' => $data['company']['nature']['text'] ?? null,
                    'company_size' => $data['company']['size']['text'] ?? null,
                    'main_activity' => $data['mainActivity']['text'] ?? null,
                    'cnae' => isset($data['mainActivity']) ? sprintf('%s - %s', $data['mainActivity']['id'] ?? '', $data['mainActivity']['text'] ?? '') : null,
                    'share_capital' => isset($data['company']['equity']) ? (float) $data['company']['equity'] : null,
                    'suframa' => !empty($data['suframa']) && is_array($data['suframa']) ? ($data['suframa'][0]['id'] ?? null) : null,
                    'phone' => $phone,
                    'email' => $email,
                    'address' => [
                        'postal_code' => $formattedZip,
                        'street' => $data['address']['street'] ?? '',
                        'number' => $data['address']['number'] ?? '',
                        'complement' => $data['address']['details'] ?? '',
                        'neighborhood' => $data['address']['district'] ?? '',
                        'city_id' => $cityId,
                        'city_name' => $cityName,
                        'state_code' => $stateCode,
                        'ibge_code' => $ibgeCode,
                    ],
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Erro ao consultar API CNPJá: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Falha na comunicação com o serviço CNPJá: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Retorna a lista de APIs cadastradas e seu status operacional no sistema.
     */
    public function list(): JsonResponse
    {
        $integrations = [
            [
                'id' => 'viacep',
                'name' => 'ViaCEP API',
                'category' => 'Geolocalização & Endereços',
                'provider' => 'ViaCEP WebServices',
                'url' => 'https://viacep.com.br/',
                'type' => 'REST / JSON Pública',
                'status' => 'operational',
                'status_label' => 'Operacional',
                'description' => 'Consulta de endereços por CEP nacional, bairros, logradouros e associação canônica por código IBGE.',
                'features' => [
                    'Autopreenchimento no cadastro de pessoas',
                    'Busca de endereços residenciais e comerciais',
                    'Vinculação instantânea com municípios IBGE',
                ],
                'endpoint' => '/api/v1/cep/{postal_code}',
            ],
            [
                'id' => 'cnpja',
                'name' => 'CNPJá Open API',
                'category' => 'Receita Federal & Cadastros Fiscais',
                'provider' => 'CNPJá',
                'url' => 'https://cnpja.com/api/open',
                'type' => 'REST / JSON Aberta & Corporativa',
                'status' => 'operational',
                'status_label' => 'Operacional',
                'description' => 'Consulta pública de CNPJs na base oficial da Receita Federal com extração de Razão Social, Fantasia, Situação, QSA e Endereço.',
                'features' => [
                    'Puxada automática ao digitar CNPJ de 14 dígitos',
                    'Preenchimento de Razão Social, Nome Fantasia e CNAE',
                    'Preenchimento automático do endereço e cidade IBGE',
                ],
                'endpoint' => '/api/v1/integrations/cnpj/{tax_id}',
            ],
            [
                'id' => 'ibge',
                'name' => 'IBGE Cidades Canônicas',
                'category' => 'Base Territorial Canônica',
                'provider' => 'Instituto Brasileiro de Geografia e Estatística',
                'url' => 'https://www.ibge.gov.br/',
                'type' => 'Banco de Dados Relacional Local',
                'status' => 'operational',
                'status_label' => '5.570 Cidades Sincronizadas',
                'description' => 'Base oficial com 100% dos 5.570 municípios do Brasil e 27 Unidades da Federação garantindo integridade estrita (RESTRICT).',
                'features' => [
                    'Componente CitySearchSelect com busca sob demanda (3 caracteres)',
                    'Ícone de lupa na extremidade direita do campo',
                    'Prevenção total de cidades órfãs ou grafias incorretas',
                ],
                'endpoint' => '/api/v1/cities',
            ],
            [
                'id' => 'geolocation',
                'name' => 'GPS & Georreferenciamento W3C',
                'category' => 'Field Service & Logística',
                'provider' => 'W3C Geolocation API / OpenStreetMap',
                'url' => 'https://www.openstreetmap.org/',
                'type' => 'HTML5 Hardware GPS & Coordenadas',
                'status' => 'operational',
                'status_label' => 'Disponível no Navegador',
                'description' => 'Captura de latitude e longitude via satélite/dispositivo para localização precisa de clientes, bases e técnicos.',
                'features' => [
                    'Captura instantânea de coordenadas via GPS com 1 clique',
                    'Espelhamento residencial e comercial',
                    'Precisão decimal de alta escala (DECIMAL 10,8 e 11,8)',
                ],
                'endpoint' => 'Nativo no Navegador / navigator.geolocation',
            ],
            [
                'id' => 'payments',
                'name' => 'Gateways de Pagamento (PIX / Boleto / Cartão)',
                'category' => 'Financeiro & Faturamento',
                'provider' => 'Asaas / Efí / Mercado Pago',
                'url' => '#',
                'type' => 'Webhooks & Cobranças',
                'status' => 'roadmap',
                'status_label' => 'Planejado (Sprint 4)',
                'description' => 'Geração automatizada de PIX dinâmico com QR Code, boletos bancários registrados e recorrência.',
                'features' => [
                    'Geração automática de faturas e QR Code PIX',
                    'Retorno automático via webhook',
                ],
                'endpoint' => '/api/v1/financial/webhooks',
            ],
            [
                'id' => 'messaging',
                'name' => 'Gateway de Mensagens (WhatsApp & SMS)',
                'category' => 'Comunicação & Notificações',
                'provider' => 'Z-API / Evolution / Twilio',
                'url' => '#',
                'type' => 'Notificações Omnichannel',
                'status' => 'roadmap',
                'status_label' => 'Planejado (Sprint 5)',
                'description' => 'Notificações automatizadas de faturas, avisos de visita técnica e confirmação de acionamento.',
                'features' => [
                    'Disparo automático de faturas e lembretes',
                    'Avisos de técnico a caminho',
                ],
                'endpoint' => '/api/v1/notifications/send',
            ],
        ];

        return response()->json([
            'success' => true,
            'total' => count($integrations),
            'data' => $integrations,
        ]);
    }
}
