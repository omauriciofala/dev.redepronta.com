<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\City;
use App\Models\Cnae;
use App\Models\Gender;
use App\Models\Person;
use App\Models\PersonGroup;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DummyPeopleSeeder extends Seeder
{
    /**
     * Gera 1000 cadastros fictícios entre Pessoa Física (PF) e Pessoa Jurídica (PJ)
     * com dados ricos, documentos válidos e papéis aleatórios.
     */
    public function run(int $total = 1000): void
    {
        $account = Account::first();
        if (!$account) {
            $account = Account::create([
                'name' => 'Rede Pronta Telecom Matriz',
                'subdomain' => 'matriz',
                'document' => '12345678000190',
                'email' => 'admin@redepronta.com',
                'phone' => '(11) 3000-0000',
                'status' => 'active',
            ]);
        }

        // Carregar cidades com estados associados para filtros ricos
        $cities = City::with('state')->inRandomOrder()->limit(300)->get();
        if ($cities->isEmpty()) {
            (new CanonicalGeographicSeeder())->run();
            $cities = City::with('state')->inRandomOrder()->limit(300)->get();
        }

        if ($cities->isEmpty()) {
            $fallbackState = \App\Models\State::firstOrCreate(['code' => 'SP'], ['code' => 'SP', 'name' => 'São Paulo']);
            $fallbackCity = City::firstOrCreate(
                ['ibge_code' => '3550308'],
                ['state_id' => $fallbackState->id, 'name' => 'São Paulo', 'ibge_code' => '3550308']
            );
            $cities = collect([$fallbackCity->load('state')]);
        }

        // Carregar grupos e gêneros
        $groups = PersonGroup::where('account_id', $account->id)->get();
        $genders = Gender::all();
        if ($genders->isEmpty()) {
            (new GenderSeeder())->run();
            $genders = Gender::all();
        }
        $maleGender = $genders->firstWhere('code', 'M') ?? $genders->first();
        $femaleGender = $genders->firstWhere('code', 'F') ?? $genders->last();

        $cnaes = Cnae::pluck('code')->toArray();
        if (empty($cnaes)) {
            $cnaes = [
                '6110-8/03', '6110-8/01', '6190-6/01', '6209-1/00',
                '4751-2/01', '4321-5/00', '4221-9/05', '7739-0/99',
            ];
        }

        $maleFirstNames = [
            'Alexandre', 'André', 'Bruno', 'Carlos', 'Daniel', 'Eduardo', 'Felipe', 'Gabriel',
            'Guilherme', 'Gustavo', 'Henrique', 'Igor', 'João', 'Lucas', 'Leonardo', 'Marcelo',
            'Matheus', 'Paulo', 'Pedro', 'Rafael', 'Rodrigo', 'Thiago', 'Vinicius', 'Vitor',
            'Fernando', 'Fabio', 'Marcos', 'Renato', 'Roberto', 'Ricardo', 'Sergio', 'Caio',
            'Diego', 'Leandro', 'Samuel', 'Ramon', 'Murilo', 'Otávio', 'Renan', 'Luciano',
            'Danilo', 'Maurício', 'Hugo', 'Arthur', 'Bernardo', 'Davi', 'Lorenzo', 'Enzo',
        ];

        $femaleFirstNames = [
            'Alice', 'Aline', 'Amanda', 'Beatriz', 'Camila', 'Carolina', 'Débora', 'Fernanda',
            'Gabriela', 'Isabela', 'Jéssica', 'Juliana', 'Larissa', 'Letícia', 'Luana', 'Mariana',
            'Natália', 'Patrícia', 'Paula', 'Rafaela', 'Renata', 'Sabrina', 'Vanessa', 'Vitória',
            'Bruna', 'Bianca', 'Clara', 'Daniela', 'Eduarda', 'Helena', 'Ingrid', 'Jaqueline',
            'Lívia', 'Manuela', 'Mirella', 'Monique', 'Nayara', 'Priscila', 'Raquel', 'Sara',
            'Simone', 'Tainá', 'Talita', 'Thaís', 'Viviane', 'Yasmin', 'Giovanna', 'Sofia',
        ];

        $lastNames = [
            'Silva', 'Santos', 'Oliveira', 'Souza', 'Pereira', 'Lima', 'Carvalho', 'Ferreira',
            'Ribeiro', 'Rodrigues', 'Almeida', 'Nascimento', 'Alves', 'Araújo', 'Costa', 'Rocha',
            'Dias', 'Castro', 'Martins', 'Barbosa', 'Moreira', 'Cardoso', 'Ramos', 'Teixeira',
            'Moura', 'Cavalcanti', 'Nunes', 'Mendes', 'Vieira', 'Monteiro', 'Correia', 'Cardoso',
            'Freitas', 'Barros', 'Pinto', 'Batista', 'Coelho', 'Fonseca', 'Guimarães', 'Borges',
            'Campos', 'Macedo', 'Vargas', 'Farias', 'Miranda', 'Assis', 'Duarte', 'Peixoto',
        ];

        $companyPrefixes = [
            'Conecte', 'Vanguarda', 'Alpha', 'Beta', 'Omega', 'Delta', 'Sigma', 'Prime', 'Ultra',
            'Max', 'Mega', 'Smart', 'Global', 'Inova', 'Brasil', 'Nacional', 'Horizonte', 'Ponto',
            'Tecno', 'Master', 'Top', 'Lider', 'Express', 'Rápido', 'Sul', 'Norte', 'Centro',
            'Infra', 'Rede', 'Fibra', 'Digital', 'Byte', 'Web', 'Net', 'Cyber', 'Power',
        ];

        $companySegments = [
            'Telecomunicações', 'Cabos e Conectores', 'Infraestrutura de Rede', 'Internet Banda Larga',
            'Soluções em Fibra Óptica', 'Logística e Cargas Expressas', 'Transportes Rápidos',
            'Serviços e Instalações', 'Equipamentos Eletrônicos', 'Engenharia de Telecom',
            'Consultoria e Sistemas', 'Data Center e Nuvem', 'Segurança Eletrônica', 'Serviços de Campo',
            'Distribuidora de Materiais', 'Comércio e Representações', 'Torres e Transmissão',
        ];

        $companySuffixes = ['Ltda', 'S.A.', 'Eireli', 'ME', 'EPP'];

        $streetTypes = ['Rua', 'Avenida', 'Alameda', 'Travessa', 'Praça', 'Rodovia'];
        $streetNames = [
            'Paulista', 'das Flores', 'Brasil', 'XV de Novembro', 'Rio Branco', 'Sete de Setembro',
            'Amazonas', 'Marechal Deodoro', 'Ipiranga', 'Barão do Rio Branco', 'Getúlio Vargas',
            'São João', 'Atlântica', 'Duque de Caxias', 'Tiradentes', 'Independência', 'Santos Dumont',
            'Presidente Vargas', 'Bela Vista', 'dos Bandeirantes', 'Juscelino Kubitschek', 'Rui Barbosa',
        ];

        $neighborhoods = [
            'Centro', 'Jardim América', 'Bela Vista', 'Vila Mariana', 'Pinheiros', 'Santana',
            'Moema', 'Boa Viagem', 'Savassi', 'Batel', 'Moinhos de Vento', 'Asa Norte', 'Asa Sul',
            'Meireles', 'Barra da Tijuca', 'Itaim Bibi', 'Higienópolis', 'Botafogo', 'Cambuí',
            'Brooklin', 'Gonzaga', 'Ponta Verde', 'Setor Bueno', 'Vila Nova', 'Floresta',
        ];

        $issuers = ['SSP/SP', 'SSP/RJ', 'SSP/MG', 'SSP/PR', 'DETRAN/RJ', 'SSP/BA', 'SSP/RS', 'SSP/SC'];

        $usedDocuments = [];
        $batch = [];
        $batchSize = 100;

        $now = Carbon::now();

        for ($i = 1; $i <= $total; $i++) {
            // Alternar 50% PF e 50% PJ
            $isPf = ($i % 2 === 1);
            $personType = $isPf ? 'individual' : 'legal';

            // Documento único e válido
            do {
                $doc = $isPf ? $this->generateCpf() : $this->generateCnpj();
            } while (isset($usedDocuments[$doc]));
            $usedDocuments[$doc] = true;

            // Cidade e Estado
            /** @var City $city */
            $city = $cities->random();
            $stateCode = $city->state?->code ?? 'SP';

            // Grupo aleatório (~90% com grupo, ~10% sem grupo)
            $group = (mt_rand(1, 100) <= 90 && $groups->isNotEmpty()) ? $groups->random() : null;

            // Status (~90% ativo, ~10% inativo)
            $status = (mt_rand(1, 100) <= 90) ? 'active' : 'inactive';

            // Data de cadastro distribuída nos últimos 24 meses
            $registrationDate = Carbon::now()->subDays(mt_rand(1, 730))->format('Y-m-d');
            $createdAt = Carbon::parse($registrationDate)->addHours(mt_rand(8, 18))->addMinutes(mt_rand(0, 59));

            // Endereço
            $street = $streetTypes[array_rand($streetTypes)] . ' ' . $streetNames[array_rand($streetNames)];
            $number = (string) mt_rand(10, 3500);
            $complement = mt_rand(1, 100) <= 40 ? ('Sala ' . mt_rand(101, 808) . ' Bloco ' . chr(mt_rand(65, 68))) : null;
            $neighborhood = $neighborhoods[array_rand($neighborhoods)];
            $cep = sprintf('%05d-%03d', mt_rand(1000, 99999), mt_rand(10, 999));

            // Coordenadas plausíveis para o território brasileiro
            $lat = -1 * (mt_rand(30000, 300000) / 10000);
            $lng = -1 * (mt_rand(35000, 600000) / 10000);

            // Papéis (Roles)
            $roles = [
                'is_client' => false,
                'is_supplier' => false,
                'is_employee' => false,
                'is_outsourced' => false,
                'is_seller' => false,
                'is_driver' => false,
                'is_carrier' => false,
                'is_requester' => false,
            ];

            if ($isPf) {
                // Distribuição de papéis PF
                $randRole = mt_rand(1, 100);
                if ($randRole <= 40) {
                    $roles['is_client'] = true;
                } elseif ($randRole <= 60) {
                    $roles['is_employee'] = true;
                    $roles['is_requester'] = (mt_rand(1, 100) <= 50);
                } elseif ($randRole <= 75) {
                    $roles['is_seller'] = true;
                } elseif ($randRole <= 85) {
                    $roles['is_driver'] = true;
                } elseif ($randRole <= 95) {
                    $roles['is_outsourced'] = true;
                } else {
                    $roles['is_client'] = true;
                    $roles['is_seller'] = true;
                }

                // Nome PF
                $isMale = (mt_rand(1, 100) <= 50);
                $firstName = $isMale
                    ? $maleFirstNames[array_rand($maleFirstNames)]
                    : $femaleFirstNames[array_rand($femaleFirstNames)];
                $sur1 = $lastNames[array_rand($lastNames)];
                $sur2 = $lastNames[array_rand($lastNames)];
                $fullName = "{$firstName} {$sur1} {$sur2}";

                $genderId = $isMale ? ($maleGender?->id) : ($femaleGender?->id);

                $birthDate = Carbon::now()->subYears(mt_rand(20, 65))->subDays(mt_rand(0, 365))->format('Y-m-d');
                $rg = sprintf('%02d.%03d.%03d-%01d', mt_rand(10, 99), mt_rand(100, 999), mt_rand(100, 999), mt_rand(0, 9));
                $rgIssuer = $issuers[array_rand($issuers)];
                $rgDate = Carbon::now()->subYears(mt_rand(2, 15))->format('Y-m-d');

                $motherName = $femaleFirstNames[array_rand($femaleFirstNames)] . ' ' . $sur2 . ' ' . $lastNames[array_rand($lastNames)];
                $fatherName = mt_rand(1, 100) <= 85 ? ($maleFirstNames[array_rand($maleFirstNames)] . ' ' . $sur1 . ' ' . $lastNames[array_rand($lastNames)]) : null;

                $email = strtolower($this->sanitizeString($firstName) . '.' . $this->sanitizeString($sur1) . $i . '@email.com');
                $phone = sprintf('(%02d) 3%03d-%04d', mt_rand(11, 99), mt_rand(100, 999), mt_rand(1000, 9999));
                $whatsapp = sprintf('(%02d) 9%04d-%04d', mt_rand(11, 99), mt_rand(1000, 9999), mt_rand(1000, 9999));

                $record = [
                    'account_id' => $account->id,
                    'city_id' => $city->id,
                    'gender_id' => $genderId,
                    'group_id' => $group?->id,
                    'group_name' => $group?->name,
                    'person_type' => 'individual',
                    'name' => $fullName,
                    'trade_name' => null,
                    'document_number' => $doc,
                    'rg_ie' => $rg,
                    'rg_issuer' => $rgIssuer,
                    'rg_issue_date' => $rgDate,
                    'birth_date' => $birthDate,
                    'birth_or_foundation_date' => $birthDate,
                    'registration_date' => $registrationDate,
                    'mother_name' => $motherName,
                    'father_name' => $fatherName,
                    'birth_place' => "{$city->name}/{$stateCode}",
                    'share_capital' => null,
                    'state_registration' => null,
                    'municipal_registration' => null,
                    'cnae' => null,
                    'suframa_registration' => null,

                    // Papéis
                    'is_client' => $roles['is_client'],
                    'is_supplier' => $roles['is_supplier'],
                    'is_employee' => $roles['is_employee'],
                    'is_outsourced' => $roles['is_outsourced'],
                    'is_seller' => $roles['is_seller'],
                    'is_driver' => $roles['is_driver'],
                    'is_carrier' => $roles['is_carrier'],
                    'is_requester' => $roles['is_requester'],

                    // Contato
                    'email' => $email,
                    'phone' => $phone,
                    'whatsapp' => $whatsapp,

                    // Endereço Residencial
                    'postal_code' => $cep,
                    'street' => $street,
                    'number' => $number,
                    'complement' => $complement,
                    'neighborhood' => $neighborhood,
                    'reference' => 'Próximo à praça central',
                    'latitude' => $lat,
                    'longitude' => $lng,

                    // Comercial igual
                    'commercial_same_as_residential' => true,
                    'commercial_postal_code' => null,
                    'commercial_street' => null,
                    'commercial_number' => null,
                    'commercial_complement' => null,
                    'commercial_neighborhood' => null,
                    'commercial_reference' => null,
                    'commercial_latitude' => null,
                    'commercial_longitude' => null,
                    'commercial_city_id' => null,

                    'status' => $status,
                    'notes' => 'Cadastro fictício de Pessoa Física para validação do sistema.',
                    'created_at' => $createdAt,
                    'updated_at' => $now,
                ];
            } else {
                // Distribuição de papéis PJ
                $randRole = mt_rand(1, 100);
                if ($randRole <= 45) {
                    $roles['is_supplier'] = true;
                } elseif ($randRole <= 70) {
                    $roles['is_client'] = true;
                } elseif ($randRole <= 85) {
                    $roles['is_carrier'] = true;
                } elseif ($randRole <= 95) {
                    $roles['is_outsourced'] = true;
                } else {
                    $roles['is_supplier'] = true;
                    $roles['is_carrier'] = true;
                }

                // Nome PJ
                $prefix = $companyPrefixes[array_rand($companyPrefixes)];
                $segment = $companySegments[array_rand($companySegments)];
                $suffix = $companySuffixes[array_rand($companySuffixes)];
                $legalName = "{$prefix} {$segment} {$suffix}";
                $tradeName = "{$prefix} " . explode(' ', $segment)[0];

                $foundationDate = Carbon::now()->subYears(mt_rand(2, 28))->subDays(mt_rand(0, 365))->format('Y-m-d');
                $ie = sprintf('%03d.%03d.%03d.%03d', mt_rand(100, 999), mt_rand(100, 999), mt_rand(100, 999), mt_rand(100, 999));
                $im = (string) mt_rand(100000, 999999);
                $cnae = $cnaes[array_rand($cnaes)];
                $shareCapital = (float) mt_rand(10, 1500) * 1000.00;

                $slug = strtolower($this->sanitizeString($prefix) . '.' . $this->sanitizeString(explode(' ', $segment)[0]));
                $email = "contato@{$slug}{$i}.com.br";
                $phone = sprintf('(%02d) 3%03d-%04d', mt_rand(11, 99), mt_rand(100, 999), mt_rand(1000, 9999));
                $whatsapp = sprintf('(%02d) 9%04d-%04d', mt_rand(11, 99), mt_rand(1000, 9999), mt_rand(1000, 9999));

                // 30% das PJs possuem endereço comercial destacado
                $hasSeparateCommercial = (mt_rand(1, 100) <= 30);
                /** @var City $commercialCity */
                $commercialCity = $hasSeparateCommercial ? $cities->random() : $city;

                $record = [
                    'account_id' => $account->id,
                    'city_id' => $city->id,
                    'gender_id' => null,
                    'group_id' => $group?->id,
                    'group_name' => $group?->name,
                    'person_type' => 'legal',
                    'name' => $legalName,
                    'trade_name' => $tradeName,
                    'document_number' => $doc,
                    'rg_ie' => $ie,
                    'rg_issuer' => null,
                    'rg_issue_date' => null,
                    'birth_date' => $foundationDate,
                    'birth_or_foundation_date' => $foundationDate,
                    'registration_date' => $registrationDate,
                    'mother_name' => null,
                    'father_name' => null,
                    'birth_place' => null,
                    'share_capital' => $shareCapital,
                    'state_registration' => $ie,
                    'municipal_registration' => $im,
                    'cnae' => $cnae,
                    'suframa_registration' => mt_rand(1, 100) <= 20 ? (string) mt_rand(10000000, 99999999) : null,

                    // Papéis
                    'is_client' => $roles['is_client'],
                    'is_supplier' => $roles['is_supplier'],
                    'is_employee' => false,
                    'is_outsourced' => $roles['is_outsourced'],
                    'is_seller' => false,
                    'is_driver' => false,
                    'is_carrier' => $roles['is_carrier'],
                    'is_requester' => false,

                    // Contato
                    'email' => $email,
                    'phone' => $phone,
                    'whatsapp' => $whatsapp,

                    // Endereço Principal / Sede
                    'postal_code' => $cep,
                    'street' => $street,
                    'number' => $number,
                    'complement' => $complement,
                    'neighborhood' => $neighborhood,
                    'reference' => 'Edifício Corporativo',
                    'latitude' => $lat,
                    'longitude' => $lng,

                    // Endereço Comercial
                    'commercial_same_as_residential' => !$hasSeparateCommercial,
                    'commercial_postal_code' => $hasSeparateCommercial ? sprintf('%05d-%03d', mt_rand(10000, 99999), mt_rand(10, 999)) : null,
                    'commercial_street' => $hasSeparateCommercial ? 'Av. Industrial ' . mt_rand(100, 999) : null,
                    'commercial_number' => $hasSeparateCommercial ? (string) mt_rand(10, 999) : null,
                    'commercial_complement' => $hasSeparateCommercial ? 'Galpão ' . mt_rand(1, 15) : null,
                    'commercial_neighborhood' => $hasSeparateCommercial ? 'Distrito Industrial' : null,
                    'commercial_reference' => $hasSeparateCommercial ? 'Próximo ao anel viário' : null,
                    'commercial_latitude' => $hasSeparateCommercial ? $lat + 0.015 : null,
                    'commercial_longitude' => $hasSeparateCommercial ? $lng + 0.015 : null,
                    'commercial_city_id' => $hasSeparateCommercial ? $commercialCity->id : null,

                    'status' => $status,
                    'notes' => 'Cadastro fictício de Pessoa Jurídica com papéis comerciais.',
                    'created_at' => $createdAt,
                    'updated_at' => $now,
                ];
            }

            $batch[] = $record;

            if (count($batch) >= $batchSize) {
                DB::table('people')->insert($batch);
                $batch = [];
            }
        }

        if (!empty($batch)) {
            DB::table('people')->insert($batch);
        }

        $this->command?->info("{$total} cadastros de pessoas fictícias gerados com sucesso!");
    }

    /**
     * Gera CPF matematicamente válido (11 dígitos numéricos).
     */
    private function generateCpf(): string
    {
        $n = [];
        for ($i = 0; $i < 9; $i++) {
            $n[] = mt_rand(0, 9);
        }

        $d1 = 0;
        for ($i = 0; $i < 9; $i++) {
            $d1 += $n[$i] * (10 - $i);
        }
        $d1 = 11 - ($d1 % 11);
        if ($d1 >= 10) $d1 = 0;
        $n[] = $d1;

        $d2 = 0;
        for ($i = 0; $i < 10; $i++) {
            $d2 += $n[$i] * (11 - $i);
        }
        $d2 = 11 - ($d2 % 11);
        if ($d2 >= 10) $d2 = 0;
        $n[] = $d2;

        return implode('', $n);
    }

    /**
     * Gera CNPJ matematicamente válido (14 dígitos numéricos).
     */
    private function generateCnpj(): string
    {
        $n = [];
        for ($i = 0; $i < 8; $i++) {
            $n[] = mt_rand(0, 9);
        }
        $n = array_merge($n, [0, 0, 0, 1]); // Filial matriz padrão 0001

        $w1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $d1 = 0;
        for ($i = 0; $i < 12; $i++) {
            $d1 += $n[$i] * $w1[$i];
        }
        $d1 = 11 - ($d1 % 11);
        if ($d1 >= 10) $d1 = 0;
        $n[] = $d1;

        $w2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $d2 = 0;
        for ($i = 0; $i < 13; $i++) {
            $d2 += $n[$i] * $w2[$i];
        }
        $d2 = 11 - ($d2 % 11);
        if ($d2 >= 10) $d2 = 0;
        $n[] = $d2;

        return implode('', $n);
    }

    /**
     * Remove caracteres acentuados para criação de emails.
     */
    private function sanitizeString(string $str): string
    {
        $unaccented = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $str);
        return preg_replace('/[^a-z0-9]/i', '', strtolower($unaccented ?: $str));
    }
}
