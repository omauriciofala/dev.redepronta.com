<?php

namespace App\Services\Person;

use App\Models\City;
use App\Models\Person;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use DomainException;
use InvalidArgumentException;

class PersonService
{
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Person::with(['city.state', 'gender', 'group']);

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['status'])) {
            $query->where('people.status', $filters['status']);
        }

        if (!empty($filters['person_type'])) {
            $query->where('people.person_type', $filters['person_type']);
        }

        if (!empty($filters['city_id'])) {
            $query->where('people.city_id', $filters['city_id']);
        }

        if (!empty($filters['group_id'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('people.group_id', $filters['group_id'])
                  ->orWhere('people.group_name', function ($sub) use ($filters) {
                      $sub->select('name')->from('person_groups')->where('id', $filters['group_id'])->limit(1);
                  });
            });
        }

        if (!empty($filters['group_name'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('people.group_name', $filters['group_name'])
                  ->orWhereHas('group', function ($gq) use ($filters) {
                      $gq->where('name', $filters['group_name']);
                  });
            });
        }

        if (!empty($filters['group'])) {
            $grp = $filters['group'];
            $query->where(function ($q) use ($grp) {
                $q->where('people.group_name', $grp)
                  ->orWhere('people.group_id', $grp)
                  ->orWhereHas('group', function ($gq) use ($grp) {
                      $gq->where('name', $grp)->orWhere('id', $grp);
                  });
            });
        }

        if (!empty($filters['state_id'])) {
            $query->whereHas('city', function ($q) use ($filters) {
                $q->where('state_id', $filters['state_id']);
            });
        } elseif (!empty($filters['state_code'])) {
            $query->whereHas('city.state', function ($q) use ($filters) {
                $q->where('code', strtoupper($filters['state_code']));
            });
        }

        $roleFilter = $filters['role'] ?? $filters['persona'] ?? null;
        if (!empty($roleFilter)) {
            match ($roleFilter) {
                'client' => $query->where('is_client', true),
                'supplier' => $query->where('is_supplier', true),
                'employee' => $query->where('is_employee', true),
                'outsourced' => $query->where('is_outsourced', true),
                'seller' => $query->where('is_seller', true),
                'driver' => $query->where('is_driver', true),
                'carrier' => $query->where('is_carrier', true),
                'requester' => $query->where('is_requester', true),
                default => null,
            };
        }

        // Ordenação segura (Sorting)
        $sortBy = $filters['sort_by'] ?? 'name';
        $sortDirection = strtolower($filters['sort_direction'] ?? 'asc') === 'desc' ? 'desc' : 'asc';

        match ($sortBy) {
            'city' => $query->orderBy(
                City::select('name')->whereColumn('cities.id', 'people.city_id'),
                $sortDirection
            )->orderBy('people.name', 'asc'),
            'date', 'created_at', 'registration_date' => $query->orderBy(
                DB::raw('COALESCE(people.registration_date, people.created_at)'),
                $sortDirection
            )->orderBy('people.name', 'asc'),
            default => $query->orderBy('people.name', $sortDirection),
        };

        return $query->paginate($perPage);
    }

    public function getCounts(?int $accountId = null): array
    {
        $accountId = $accountId ?? session('active_account_id') ?? \App\Models\Account::first()?->id ?? 1;

        $row = DB::table('people')
            ->where('account_id', $accountId)
            ->whereNull('deleted_at')
            ->selectRaw("
                COUNT(*) as total,
                COALESCE(SUM(CASE WHEN is_client = 1 THEN 1 ELSE 0 END), 0) as client,
                COALESCE(SUM(CASE WHEN is_supplier = 1 THEN 1 ELSE 0 END), 0) as supplier,
                COALESCE(SUM(CASE WHEN is_employee = 1 THEN 1 ELSE 0 END), 0) as employee,
                COALESCE(SUM(CASE WHEN is_outsourced = 1 THEN 1 ELSE 0 END), 0) as outsourced,
                COALESCE(SUM(CASE WHEN is_seller = 1 THEN 1 ELSE 0 END), 0) as seller,
                COALESCE(SUM(CASE WHEN is_driver = 1 THEN 1 ELSE 0 END), 0) as driver,
                COALESCE(SUM(CASE WHEN is_carrier = 1 THEN 1 ELSE 0 END), 0) as carrier,
                COALESCE(SUM(CASE WHEN is_requester = 1 THEN 1 ELSE 0 END), 0) as requester
            ")->first();

        return [
            'total' => (int) ($row->total ?? 0),
            'client' => (int) ($row->client ?? 0),
            'supplier' => (int) ($row->supplier ?? 0),
            'employee' => (int) ($row->employee ?? 0),
            'outsourced' => (int) ($row->outsourced ?? 0),
            'seller' => (int) ($row->seller ?? 0),
            'driver' => (int) ($row->driver ?? 0),
            'carrier' => (int) ($row->carrier ?? 0),
            'requester' => (int) ($row->requester ?? 0),
        ];
    }

    public function create(array $data): Person
    {
        return DB::transaction(function () use ($data) {
            if (empty($data['account_id'])) {
                $data['account_id'] = session('active_account_id') ?? \App\Models\Account::first()?->id ?? 1;
            }
            if (!empty($data['group_id'])) {
                $grp = \App\Models\PersonGroup::find($data['group_id']);
                if ($grp) {
                    $data['group_name'] = $grp->name;
                }
            }
            return Person::create($data);
        });
    }

    public function update(Person $person, array $data): Person
    {
        return DB::transaction(function () use ($person, $data) {
            if (!empty($data['group_id'])) {
                $grp = \App\Models\PersonGroup::find($data['group_id']);
                if ($grp) {
                    $data['group_name'] = $grp->name;
                }
            } elseif (array_key_exists('group_id', $data) && is_null($data['group_id'])) {
                $data['group_name'] = null;
            }

            $person->update($data);
            return $person->fresh(['city.state', 'gender', 'group']);
        });
    }

    /**
     * Verifica se a pessoa possui vínculos ou dependências em qualquer parte do sistema.
     */
    public function hasSystemDependencies(Person $person): bool
    {
        // 1. Vínculo com Usuários do Sistema (autenticação/operadores)
        if (Schema::hasColumn('users', 'person_id')) {
            if (DB::table('users')->where('person_id', $person->id)->exists()) {
                return true;
            }
        }
        if (!empty($person->email)) {
            if (DB::table('users')->where('email', $person->email)->exists()) {
                return true;
            }
        }

        // 2. Busca dinâmica no banco por tabelas com chave person_id (exceto people)
        try {
            $tables = DB::select("
                SELECT TABLE_NAME 
                FROM information_schema.COLUMNS 
                WHERE TABLE_SCHEMA = DATABASE() 
                  AND COLUMN_NAME = 'person_id' 
                  AND TABLE_NAME != 'people'
            ");

            foreach ($tables as $t) {
                $tableName = $t->TABLE_NAME ?? $t->table_name ?? null;
                if ($tableName && Schema::hasTable($tableName)) {
                    if (DB::table($tableName)->where('person_id', $person->id)->exists()) {
                        return true;
                    }
                }
            }
        } catch (\Throwable $e) {
            // Em caso de restrição em information_schema, prossegue para verificação declarativa
        }

        // 3. Mapeamento declarativo de tabelas do ERP (Contratos, FSM, Financeiro, Estoque)
        $domainTables = [
            'contracts' => ['client_id', 'person_id'],
            'work_orders' => ['client_id', 'technician_id', 'person_id'],
            'service_orders' => ['client_id', 'technician_id', 'person_id'],
            'invoices' => ['client_id', 'person_id'],
            'receivables' => ['client_id', 'person_id'],
            'payables' => ['supplier_id', 'person_id'],
            'financial_transactions' => ['person_id'],
            'financial_entries' => ['person_id'],
            'inventory_movements' => ['person_id', 'responsible_id'],
            'serials' => ['assigned_person_id', 'person_id'],
        ];

        foreach ($domainTables as $table => $columns) {
            if (Schema::hasTable($table)) {
                foreach ($columns as $column) {
                    if (Schema::hasColumn($table, $column)) {
                        if (DB::table($table)->where($column, $person->id)->exists()) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    public function delete(Person $person): bool
    {
        return DB::transaction(function () use ($person) {
            if ($this->hasSystemDependencies($person)) {
                throw new DomainException('Não é possível excluir esta pessoa pois ela já possui vínculos no sistema (ex: usuário, contratos ou lançamentos). Interrompa seu uso alterando o status para inativo.');
            }

            return (bool) $person->delete();
        });
    }

    public function toggleStatus(Person $person): Person
    {
        $newStatus = $person->status === 'active' ? 'inactive' : 'active';
        $person->update(['status' => $newStatus]);
        return $person;
    }
}
