<?php

namespace App\Services\Person;

use App\Models\City;
use App\Models\Person;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PersonService
{
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Person::with(['city.state', 'gender']);

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

        if (!empty($filters['state_id'])) {
            $query->whereHas('city', function ($q) use ($filters) {
                $q->where('state_id', $filters['state_id']);
            });
        } elseif (!empty($filters['state_code'])) {
            $query->whereHas('city.state', function ($q) use ($filters) {
                $q->where('code', strtoupper($filters['state_code']));
            });
        }

        if (!empty($filters['persona'])) {
            match ($filters['persona']) {
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
            return Person::create($data);
        });
    }

    public function update(Person $person, array $data): Person
    {
        return DB::transaction(function () use ($person, $data) {
            $person->update($data);
            return $person->fresh(['city.state', 'gender']);
        });
    }

    public function delete(Person $person): bool
    {
        return DB::transaction(function () use ($person) {
            $hasDependencies = false;

            if ($hasDependencies) {
                $person->update(['status' => 'inactive']);
                throw new InvalidArgumentException('A pessoa possui dependências ativas e não pode ser excluída fisicamente. O cadastro foi marcado como inativo.');
            }

            return $person->delete();
        });
    }

    public function toggleStatus(Person $person): Person
    {
        $newStatus = $person->status === 'active' ? 'inactive' : 'active';
        $person->update(['status' => $newStatus]);
        return $person;
    }
}
