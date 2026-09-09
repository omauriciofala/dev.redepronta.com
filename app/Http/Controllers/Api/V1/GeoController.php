<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\State;
use App\Models\Gender;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GeoController extends Controller
{
    public function states(): JsonResponse
    {
        $states = State::orderBy('name')->get(['id', 'code', 'name']);
        return response()->json(['data' => $states]);
    }

    public function cities(Request $request): JsonResponse
    {
        $query = City::with('state:id,code,name');

        // Busca direta por ID específico
        if ($request->filled('id')) {
            $city = $query->find($request->input('id'));
            return response()->json(['data' => $city ? [$city] : []]);
        }

        // Busca por termo (nome ou código IBGE)
        if ($request->filled('q')) {
            $term = trim($request->input('q'));
            if (mb_strlen($term) >= 3) {
                $query->where(function ($q) use ($term) {
                    $q->where('name', 'like', "{$term}%")
                      ->orWhere('name', 'like', "%{$term}%")
                      ->orWhere('ibge_code', 'like', "{$term}%");
                });
            } else {
                return response()->json(['data' => []]);
            }
        }

        if ($request->filled('state_id')) {
            $query->where('state_id', $request->input('state_id'));
        }

        // Limite ergonômico de resultados para navegação ágil
        $cities = $query->orderBy('name')->limit(30)->get(['id', 'state_id', 'ibge_code', 'name']);

        return response()->json(['data' => $cities]);
    }

    public function genders(): JsonResponse
    {
        $genders = Gender::orderBy('name')->get(['id', 'code', 'name']);
        return response()->json(['data' => $genders]);
    }
    public function cep(string $postalCode): JsonResponse
    {
        $cleanCep = preg_replace('/\D/', '', $postalCode);

        if (strlen($cleanCep) !== 8) {
            return response()->json([
                'success' => false,
                'message' => 'CEP inválido. Forneça 8 dígitos numéricos.',
            ], 422);
        }

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(5)->get("https://viacep.com.br/ws/{$cleanCep}/json/");

            if (!$response->successful() || isset($response->json()['erro'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'CEP não encontrado na base do ViaCEP.',
                ], 404);
            }

            $data = $response->json();

            // Localiza a cidade canônica pelo código IBGE oficial
            $city = null;
            if (!empty($data['ibge'])) {
                $city = City::with('state:id,code,name')->where('ibge_code', $data['ibge'])->first();
            }

            // Fallback por nome da localidade e UF
            if (!$city && !empty($data['localidade']) && !empty($data['uf'])) {
                $city = City::with('state:id,code,name')
                    ->where('name', 'like', $data['localidade'])
                    ->whereHas('state', fn ($q) => $q->where('code', $data['uf']))
                    ->first();
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'postal_code' => $data['cep'] ?? $postalCode,
                    'street' => $data['logradouro'] ?? '',
                    'complement' => $data['complemento'] ?? '',
                    'neighborhood' => $data['bairro'] ?? '',
                    'city_id' => $city?->id,
                    'city_name' => $city?->name ?? $data['localidade'] ?? '',
                    'state_code' => $city?->state?->code ?? $data['uf'] ?? '',
                    'ibge_code' => $city?->ibge_code ?? $data['ibge'] ?? '',
                    'city' => $city,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Falha de comunicação com o serviço ViaCEP: ' . $e->getMessage(),
            ], 502);
        }
    }
}
