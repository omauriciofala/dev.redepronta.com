<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;
use App\Models\Department;
use App\Models\TicketCategory;
use App\Models\TicketReason;

class OperationsCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = Account::all();

        foreach ($accounts as $account) {
            $this->seedForAccount($account);
        }
    }

    public function seedForAccount(Account $account): void
    {
        $structure = [
            [
                'name' => 'NOC & Monitoramento de Redes',
                'code' => 'NOC',
                'description' => 'Operação central de monitoramento de infraestrutura, links dedicados e falhas massivas.',
                'categories' => [
                    [
                        'name' => 'Falha Massiva / Rompimento',
                        'description' => 'Incidentes de rompimento de cabo óptico troncal ou indisponibilidade de POP/OLT.',
                        'reasons' => [
                            ['name' => 'Rompimento de Cabo Troncal', 'priority' => 'CRITICAL', 'sla' => 4],
                            ['name' => 'Queda de Energia em POP / Baterias Baixas', 'priority' => 'CRITICAL', 'sla' => 2],
                            ['name' => 'Degradação Óptica Generalizada', 'priority' => 'HIGH', 'sla' => 6],
                        ],
                    ],
                    [
                        'name' => 'Link Dedicado Corporativo',
                        'description' => 'Eventos afetando clientes corporativos com SLA estrito de disponibilidade.',
                        'reasons' => [
                            ['name' => 'Link BGP Fora do Ar', 'priority' => 'CRITICAL', 'sla' => 4],
                            ['name' => 'Perda de Pacotes / Latência Excessiva', 'priority' => 'HIGH', 'sla' => 6],
                            ['name' => 'Alteração de Roteamento / Prefixos', 'priority' => 'MEDIUM', 'sla' => 12],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Suporte Técnico ao Assinante (N1/N2)',
                'code' => 'SUPORTE',
                'description' => 'Atendimento e triagem de chamados residenciais e PME.',
                'categories' => [
                    [
                        'name' => 'Conectividade Residencial',
                        'description' => 'Problemas de sinal, lentidão e Wi-Fi no cliente.',
                        'reasons' => [
                            ['name' => 'Sem Conexão / Sinal Vermelho (LOS)', 'priority' => 'HIGH', 'sla' => 8],
                            ['name' => 'Lentidão Acentuada / Quedas Frequentes', 'priority' => 'MEDIUM', 'sla' => 16],
                            ['name' => 'Configuração de Wi-Fi / Troca de Senha', 'priority' => 'LOW', 'sla' => 24],
                        ],
                    ],
                    [
                        'name' => 'Equipamento de Assinante (CPE / ONU)',
                        'description' => 'Problemas físicos em ONU ou roteadores cedidos em comodato.',
                        'reasons' => [
                            ['name' => 'ONU Travada / Queimada por Descarga', 'priority' => 'HIGH', 'sla' => 12],
                            ['name' => 'Fonte de Alimentação Avariada', 'priority' => 'MEDIUM', 'sla' => 12],
                            ['name' => 'Upgrade de Roteador Wi-Fi 6', 'priority' => 'LOW', 'sla' => 48],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Operações de Campo & FSM',
                'code' => 'CAMPO',
                'description' => 'Execução presencial: ativações, vistorias, manutenções e reparos ópticos.',
                'categories' => [
                    [
                        'name' => 'Ativação & Instalação Nova',
                        'description' => 'Lançamento de drop óptico, conectorização e ativação no assinante.',
                        'reasons' => [
                            ['name' => 'Instalação Padrão FTTH Residencial', 'priority' => 'MEDIUM', 'sla' => 24],
                            ['name' => 'Instalação Corporativa com Dupla Abordagem', 'priority' => 'HIGH', 'sla' => 12],
                            ['name' => 'Mudança de Endereço do Assinante', 'priority' => 'MEDIUM', 'sla' => 24],
                        ],
                    ],
                    [
                        'name' => 'Manutenção Corretiva Externa',
                        'description' => 'Reparo em caixas de atendimento (CTO), fusão e substituição de drop.',
                        'reasons' => [
                            ['name' => 'Drop Óptico Rompido na Fachada/Poste', 'priority' => 'HIGH', 'sla' => 8],
                            ['name' => 'Porta CTO Atenuada / Troca de Porta', 'priority' => 'MEDIUM', 'sla' => 12],
                            ['name' => 'Substituição de Conector de Campo (Fast)', 'priority' => 'MEDIUM', 'sla' => 12],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Engenharia & Expansão de Rede',
                'code' => 'ENG',
                'description' => 'Projetos de implantação de rede FTTH, adequação de postes e novas CTOs.',
                'categories' => [
                    [
                        'name' => 'Auditoria e Vistoria de Infraestrutura',
                        'description' => 'Fiscalização de conformidade técnica e padronização.',
                        'reasons' => [
                            ['name' => 'Vistoria Preventiva de Rota Óptica', 'priority' => 'LOW', 'sla' => 72],
                            ['name' => 'Auditoria de CTO / Retirada de Drop Clandestino', 'priority' => 'LOW', 'sla' => 48],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($structure as $deptData) {
            $department = Department::firstOrCreate(
                [
                    'account_id' => $account->id,
                    'name' => $deptData['name'],
                ],
                [
                    'code' => $deptData['code'],
                    'description' => $deptData['description'],
                    'is_active' => true,
                ]
            );

            foreach ($deptData['categories'] as $catData) {
                $category = TicketCategory::firstOrCreate(
                    [
                        'account_id' => $account->id,
                        'department_id' => $department->id,
                        'name' => $catData['name'],
                    ],
                    [
                        'description' => $catData['description'],
                        'is_active' => true,
                    ]
                );

                foreach ($catData['reasons'] as $rsnData) {
                    TicketReason::firstOrCreate(
                        [
                            'account_id' => $account->id,
                            'category_id' => $category->id,
                            'name' => $rsnData['name'],
                        ],
                        [
                            'default_priority' => $rsnData['priority'],
                            'default_sla_hours' => $rsnData['sla'],
                            'is_active' => true,
                        ]
                    );
                }
            }
        }
    }
}
