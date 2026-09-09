<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\City;
use App\Models\Cnae;
use App\Models\Gender;
use App\Models\Neighborhood;
use App\Models\Person;
use App\Models\PersonGroup;
use App\Models\State;
use Database\Seeders\DummyPeopleSeeder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DeveloperController extends Controller
{
    /**
     * Retorna estatísticas em tempo real do banco de dados para a página de desenvolvedor.
     */
    public function stats(): JsonResponse
    {
        $totalPeople = Person::count();
        $pfCount = Person::where('person_type', 'individual')->count();
        $pjCount = Person::where('person_type', 'legal')->count();
        $activeCount = Person::where('status', 'active')->count();
        $inactiveCount = Person::where('status', 'inactive')->count();

        $account = Account::first();

        return response()->json([
            'data' => [
                'total_people' => $totalPeople,
                'individual_people' => $pfCount,
                'legal_people' => $pjCount,
                'active_people' => $activeCount,
                'inactive_people' => $inactiveCount,
                'total_groups' => PersonGroup::count(),
                'total_cities' => City::count(),
                'total_states' => State::count(),
                'total_cnaes' => Cnae::count(),
                'total_neighborhoods' => Neighborhood::count(),
                'account' => $account ? [
                    'id' => $account->id,
                    'name' => $account->name,
                    'document' => $account->document,
                    'email' => $account->email,
                ] : null,
                'server_time' => Carbon::now()->format('d/m/Y H:i:s'),
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
            ]
        ]);
    }

    /**
     * Reseta o banco de dados para o estado inicial padrão sem cadastros de pessoas.
     */
    public function reset(): JsonResponse
    {
        try {
            \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
            DB::table('people')->truncate();
            \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

            // Garantir que a Conta Matriz existe
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

            // Garantir que os grupos canônicos existem
            if (PersonGroup::where('account_id', $account->id)->count() === 0) {
                $defaultGroups = [
                    ['name' => 'Clientes VIP', 'description' => 'Clientes de alto valor com atendimento prioritario SLA 4h', 'color' => '#FC6714'],
                    ['name' => 'Prestadores Fibra Óptica', 'description' => 'Técnicos e equipes terceirizadas de infraestrutura de campo', 'color' => '#06064D'],
                    ['name' => 'Fornecedores Homologados', 'description' => 'Distribuidores e parceiros de atacado de equipamentos e links', 'color' => '#10B981'],
                    ['name' => 'Geral', 'description' => 'Grupo padrão de cadastros', 'color' => '#64748B'],
                ];

                foreach ($defaultGroups as $grp) {
                    PersonGroup::create(array_merge($grp, [
                        'account_id' => $account->id,
                        'is_active' => true,
                    ]));
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Sistema resetado com sucesso! Todos os cadastros de pessoas foram removidos e o sistema retornou ao padrão limpo.',
                'stats' => [
                    'total_people' => 0,
                    'account_name' => $account->name,
                ]
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao resetar o sistema: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Popula pessoas fictícias no sistema.
     */
    public function populatePeople(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'count' => 'nullable|integer|min:1|max:5000',
        ]);

        $count = (int) ($validated['count'] ?? 1000);

        try {
            $seeder = new DummyPeopleSeeder();
            $seeder->run($count);

            $totalNow = Person::count();

            return response()->json([
                'success' => true,
                'message' => "População concluída com sucesso! {$count} pessoas fictícias foram adicionadas.",
                'total_people' => $totalNow,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao popular pessoas: ' . $e->getMessage(),
            ], 500);
        }
    }
}
