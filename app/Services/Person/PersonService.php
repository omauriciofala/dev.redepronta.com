<?php

namespace App\Services\Person;

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
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['person_type'])) {
            $query->where('person_type', $filters['person_type']);
        }

        if (!empty($filters['city_id'])) {
            $query->where('city_id', $filters['city_id']);
        }

        if (!empty($filters['persona'])) {
            match ($filters['persona']) {
                'employee' => $query->employees(),
                'supplier' => $query->suppliers(),
                'client' => $query->clients(),
                'requester' => $query->requesters(),
                default => null,
            };
        }

        return $query->orderBy('name', 'asc')->paginate($perPage);
    }

    public function create(array $data): Person
    {
        return DB::transaction(function () use ($data) {
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
            // Se houver qualquer dependência futura, a exclusão é bloqueada
            // e apenas inativa o registro
            $hasDependencies = false; // Em sprints futuros, verificar OS, chamados, notas

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
