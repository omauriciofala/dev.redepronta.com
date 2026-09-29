<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccessControlSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Módulo Pessoas
            [
                'module' => 'people',
                'name' => 'Visualizar Pessoas',
                'slug' => 'people.view',
                'description' => 'Permite visualizar o catálogo, listagem e detalhes das pessoas cadastradas.'
            ],
            [
                'module' => 'people',
                'name' => 'Cadastrar Pessoas',
                'slug' => 'people.create',
                'description' => 'Permite criar novos registros de pessoas com múltiplos papéis.'
            ],
            [
                'module' => 'people',
                'name' => 'Editar Pessoas',
                'slug' => 'people.edit',
                'description' => 'Permite atualizar dados cadastrais, endereços e documentos de pessoas.'
            ],
            [
                'module' => 'people',
                'name' => 'Excluir Pessoas',
                'slug' => 'people.delete',
                'description' => 'Permite inativar ou excluir registros de pessoas que não possuam vínculos impeditivos.'
            ],
            [
                'module' => 'people',
                'name' => 'Exportar Dados de Pessoas',
                'slug' => 'people.export',
                'description' => 'Permite exportar relatórios e planilhas com dados de pessoas.'
            ],

            // Módulo Suprimentos & WMS
            [
                'module' => 'supplies',
                'name' => 'Visualizar Suprimentos & Estoque',
                'slug' => 'supplies.view',
                'description' => 'Permite visualizar saldo, posições regionais e catálogo de materiais.'
            ],
            [
                'module' => 'supplies',
                'name' => 'Gerenciar Materiais & Unidades',
                'slug' => 'supplies.materials.manage',
                'description' => 'Permite criar, editar e importar cadastro de materiais e unidades de medida.'
            ],
            [
                'module' => 'supplies',
                'name' => 'Gerenciar Depósitos Físicos',
                'slug' => 'supplies.depots.manage',
                'description' => 'Permite criar e gerenciar depósitos, tipos de depósitos e responsáveis.'
            ],
            [
                'module' => 'supplies',
                'name' => 'Gerenciar Posições Regionais (Clusters)',
                'slug' => 'supplies.clusters.manage',
                'description' => 'Permite agrupar depósitos em clusters regionais e definir proprietários.'
            ],
            [
                'module' => 'supplies',
                'name' => 'Gerenciar Proprietários de Materiais',
                'slug' => 'supplies.owners.manage',
                'description' => 'Permite gerenciar empresas ou clientes proprietários de materiais de telecom.'
            ],
            [
                'module' => 'supplies',
                'name' => 'Gerenciar & Rastrear Seriais',
                'slug' => 'supplies.serials.manage',
                'description' => 'Permite controle individual de números de série, estados e históricos.'
            ],
            [
                'module' => 'supplies',
                'name' => 'Registrar Entradas & Movimentações',
                'slug' => 'supplies.movements.create',
                'description' => 'Permite emitir entradas por compra/doação, saídas de consumo e perdas.'
            ],
            [
                'module' => 'supplies',
                'name' => 'Transferências Entre Depósitos',
                'slug' => 'supplies.transfers.create',
                'description' => 'Permite transferir materiais e seriais entre depósitos e clusters.'
            ],

            // Módulo Usuários & Permissões
            [
                'module' => 'access',
                'name' => 'Visualizar Usuários',
                'slug' => 'users.view',
                'description' => 'Permite visualizar os usuários e operadores do sistema.'
            ],
            [
                'module' => 'access',
                'name' => 'Cadastrar Usuários',
                'slug' => 'users.create',
                'description' => 'Permite criar novos usuários de acesso ao ERP.'
            ],
            [
                'module' => 'access',
                'name' => 'Editar Usuários & Senhas',
                'slug' => 'users.edit',
                'description' => 'Permite editar permissões, papéis e redefinir credenciais de usuários.'
            ],
            [
                'module' => 'access',
                'name' => 'Inativar / Excluir Usuários',
                'slug' => 'users.delete',
                'description' => 'Permite desativar ou remover acessos de usuários ao sistema.'
            ],
            [
                'module' => 'access',
                'name' => 'Gerenciar Papéis de Usuários (Roles)',
                'slug' => 'roles.manage',
                'description' => 'Permite criar perfis de acesso e configurar a matriz de permissões.'
            ],

            // Módulo Cadastros Básicos
            [
                'module' => 'basics',
                'name' => 'Gerenciar Grupos de Pessoas',
                'slug' => 'basics.groups',
                'description' => 'Permite gerenciar grupos de classificação de pessoas.'
            ],
            [
                'module' => 'basics',
                'name' => 'Gerenciar Geografia & Bairros',
                'slug' => 'basics.geo',
                'description' => 'Permite gerenciar estados, cidades e bairros.'
            ],
            [
                'module' => 'basics',
                'name' => 'Gerenciar Atividades CNAE',
                'slug' => 'basics.cnae',
                'description' => 'Permite cadastrar e vincular atividades econômicas CNAE.'
            ],

            // Módulo Integrações & APIs
            [
                'module' => 'integrations',
                'name' => 'Visualizar Integrações',
                'slug' => 'integrations.view',
                'description' => 'Permite monitorar integradores e histórico de consumo de APIs externas.'
            ],
            [
                'module' => 'integrations',
                'name' => 'Executar Consultas em APIs',
                'slug' => 'integrations.execute',
                'description' => 'Permite disparar buscas de CNPJá, ViaCEP e serviços governamentais.'
            ],

            // Módulo Governança & Desenvolvedor
            [
                'module' => 'developer',
                'name' => 'Acesso ao Console Desenvolvedor',
                'slug' => 'developer.access',
                'description' => 'Permite inspecionar logs e métricas de sandbox.'
            ],
            [
                'module' => 'developer',
                'name' => 'Reset e Auditoria de Dados',
                'slug' => 'developer.reset',
                'description' => 'Permite executar ferramentas de população e limpeza de testes.'
            ],
            [
                'module' => 'developer',
                'name' => 'Visualizar Histórico de Mudanças (Change-log)',
                'slug' => 'changelog.view',
                'description' => 'Permite consultar o change-log vivo e commits recentes.'
            ],
        ];

        foreach ($permissions as $permData) {
            Permission::updateOrCreate(
                ['slug' => $permData['slug']],
                $permData
            );
        }

        $allPermissionIds = Permission::pluck('id')->all();

        // Tenants existentes
        $accounts = Account::all();
        if ($accounts->isEmpty()) {
            $accounts = [Account::create([
                'id' => 1,
                'name' => 'Rede Pronta Matriz',
                'subdomain' => 'matriz',
                'status' => 'active',
            ])];
        }

        foreach ($accounts as $account) {
            // 1. Administrador Geral
            $adminRole = Role::updateOrCreate(
                ['account_id' => $account->id, 'slug' => 'admin'],
                [
                    'name' => 'Administrador Geral',
                    'description' => 'Acesso administrativo completo aos módulos do tenant.',
                    'is_system' => true,
                ]
            );
            $adminRole->permissions()->sync($allPermissionIds);

            // 2. Gestor de Suprimentos & WMS
            $stockManagerRole = Role::updateOrCreate(
                ['account_id' => $account->id, 'slug' => 'stock_manager'],
                [
                    'name' => 'Gestor de Suprimentos & WMS',
                    'description' => 'Gestão avançada de depósitos, inventário, materiais, seriais e movimentações.',
                    'is_system' => true,
                ]
            );
            $stockManagerPerms = Permission::where(function ($q) {
                $q->where('module', 'supplies')
                  ->orWhereIn('slug', ['people.view', 'basics.groups', 'basics.geo', 'changelog.view']);
            })->pluck('id')->all();
            $stockManagerRole->permissions()->sync($stockManagerPerms);

            // 3. Operador de Estoque
            $stockOpRole = Role::updateOrCreate(
                ['account_id' => $account->id, 'slug' => 'stock_operator'],
                [
                    'name' => 'Operador de Estoque',
                    'description' => 'Operações diárias de entrada, baixa e conferência de materiais e seriais.',
                    'is_system' => true,
                ]
            );
            $stockOpPerms = Permission::whereIn('slug', [
                'supplies.view',
                'supplies.movements.create',
                'supplies.serials.manage',
                'people.view',
            ])->pluck('id')->all();
            $stockOpRole->permissions()->sync($stockOpPerms);

            // 4. Atendimento & Cadastros
            $frontdeskRole = Role::updateOrCreate(
                ['account_id' => $account->id, 'slug' => 'frontdesk'],
                [
                    'name' => 'Atendimento & Cadastros',
                    'description' => 'Gestão e atendimento cadastral de clientes, fornecedores e parceiros.',
                    'is_system' => true,
                ]
            );
            $frontdeskPerms = Permission::where(function ($q) {
                $q->where('module', 'people')
                  ->orWhere('module', 'basics')
                  ->orWhereIn('slug', ['integrations.view', 'integrations.execute']);
            })->pluck('id')->all();
            $frontdeskRole->permissions()->sync($frontdeskPerms);

            // 5. Consulta Geral
            $viewerRole = Role::updateOrCreate(
                ['account_id' => $account->id, 'slug' => 'viewer'],
                [
                    'name' => 'Consulta Geral (Leitura)',
                    'description' => 'Acesso restrito para consulta e relatórios sem permissão de gravação.',
                    'is_system' => true,
                ]
            );
            $viewerPerms = Permission::whereIn('slug', [
                'people.view',
                'supplies.view',
                'changelog.view',
            ])->pluck('id')->all();
            $viewerRole->permissions()->sync($viewerPerms);

            // Atualiza usuário administrador padrão existente para Super Admin
            $firstUser = User::where('account_id', $account->id)->first();
            if ($firstUser) {
                $firstUser->update([
                    'is_super_admin' => true,
                    'status' => 'active',
                    'role_id' => $adminRole->id,
                ]);
            }
        }
    }
}
