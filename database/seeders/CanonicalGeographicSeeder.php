<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\State;
use App\Models\City;

class CanonicalGeographicSeeder extends Seeder
{
    public function run(): void
    {
        $states = [
            ['code' => 'AC', 'name' => 'Acre'],
            ['code' => 'AL', 'name' => 'Alagoas'],
            ['code' => 'AP', 'name' => 'Amapá'],
            ['code' => 'AM', 'name' => 'Amazonas'],
            ['code' => 'BA', 'name' => 'Bahia'],
            ['code' => 'CE', 'name' => 'Ceará'],
            ['code' => 'DF', 'name' => 'Distrito Federal'],
            ['code' => 'ES', 'name' => 'Espírito Santo'],
            ['code' => 'GO', 'name' => 'Goiás'],
            ['code' => 'MA', 'name' => 'Maranhão'],
            ['code' => 'MT', 'name' => 'Mato Grosso'],
            ['code' => 'MS', 'name' => 'Mato Grosso do Sul'],
            ['code' => 'MG', 'name' => 'Minas Gerais'],
            ['code' => 'PA', 'name' => 'Pará'],
            ['code' => 'PB', 'name' => 'Paraíba'],
            ['code' => 'PR', 'name' => 'Paraná'],
            ['code' => 'PE', 'name' => 'Pernambuco'],
            ['code' => 'PI', 'name' => 'Piauí'],
            ['code' => 'RJ', 'name' => 'Rio de Janeiro'],
            ['code' => 'RN', 'name' => 'Rio Grande do Norte'],
            ['code' => 'RS', 'name' => 'Rio Grande do Sul'],
            ['code' => 'RO', 'name' => 'Rondônia'],
            ['code' => 'RR', 'name' => 'Roraima'],
            ['code' => 'SC', 'name' => 'Santa Catarina'],
            ['code' => 'SP', 'name' => 'São Paulo'],
            ['code' => 'SE', 'name' => 'Sergipe'],
            ['code' => 'TO', 'name' => 'Tocantins'],
        ];

        $stateModels = [];
        foreach ($states as $s) {
            $stateModels[$s['code']] = State::firstOrCreate(['code' => $s['code']], $s);
        }

        // Cidades Canônicas Estratégicas com códigos IBGE reais
        $cities = [
            // SP
            ['state' => 'SP', 'ibge_code' => '3550308', 'name' => 'São Paulo'],
            ['state' => 'SP', 'ibge_code' => '3509502', 'name' => 'Campinas'],
            ['state' => 'SP', 'ibge_code' => '3549904', 'name' => 'São José dos Campos'],
            ['state' => 'SP', 'ibge_code' => '3543402', 'name' => 'Ribeirão Preto'],
            ['state' => 'SP', 'ibge_code' => '3548708', 'name' => 'São Bernardo do Campo'],
            ['state' => 'SP', 'ibge_code' => '3547809', 'name' => 'Santo André'],
            ['state' => 'SP', 'ibge_code' => '3534401', 'name' => 'Osasco'],
            ['state' => 'SP', 'ibge_code' => '3552205', 'name' => 'Sorocaba'],
            // MG
            ['state' => 'MG', 'ibge_code' => '3106200', 'name' => 'Belo Horizonte'],
            ['state' => 'MG', 'ibge_code' => '3170206', 'name' => 'Uberlândia'],
            ['state' => 'MG', 'ibge_code' => '3118601', 'name' => 'Contagem'],
            ['state' => 'MG', 'ibge_code' => '3136702', 'name' => 'Juiz de Fora'],
            ['state' => 'MG', 'ibge_code' => '3106705', 'name' => 'Betim'],
            ['state' => 'MG', 'ibge_code' => '3143302', 'name' => 'Montes Claros'],
            // RJ
            ['state' => 'RJ', 'ibge_code' => '3304557', 'name' => 'Rio de Janeiro'],
            ['state' => 'RJ', 'ibge_code' => '3304904', 'name' => 'São Gonçalo'],
            ['state' => 'RJ', 'ibge_code' => '3301702', 'name' => 'Duque de Caxias'],
            ['state' => 'RJ', 'ibge_code' => '3303302', 'name' => 'Niterói'],
            // PR
            ['state' => 'PR', 'ibge_code' => '4106902', 'name' => 'Curitiba'],
            ['state' => 'PR', 'ibge_code' => '4113700', 'name' => 'Londrina'],
            ['state' => 'PR', 'ibge_code' => '4115200', 'name' => 'Maringá'],
            // RS
            ['state' => 'RS', 'ibge_code' => '4314902', 'name' => 'Porto Alegre'],
            ['state' => 'RS', 'ibge_code' => '4305108', 'name' => 'Caxias do Sul'],
            // SC
            ['state' => 'SC', 'ibge_code' => '4205407', 'name' => 'Florianópolis'],
            ['state' => 'SC', 'ibge_code' => '4209102', 'name' => 'Joinville'],
            // BA
            ['state' => 'BA', 'ibge_code' => '2927408', 'name' => 'Salvador'],
            ['state' => 'BA', 'ibge_code' => '2910800', 'name' => 'Feira de Santana'],
            // PE
            ['state' => 'PE', 'ibge_code' => '2611606', 'name' => 'Recife'],
            // CE
            ['state' => 'CE', 'ibge_code' => '2304400', 'name' => 'Fortaleza'],
            // GO
            ['state' => 'GO', 'ibge_code' => '5208707', 'name' => 'Goiânia'],
            // DF
            ['state' => 'DF', 'ibge_code' => '5300108', 'name' => 'Brasília'],
        ];

        foreach ($cities as $c) {
            City::firstOrCreate(
                ['ibge_code' => $c['ibge_code']],
                [
                    'state_id' => $stateModels[$c['state']]->id,
                    'ibge_code' => $c['ibge_code'],
                    'name' => $c['name'],
                ]
            );
        }
    }
}
