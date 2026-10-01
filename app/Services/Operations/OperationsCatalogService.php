<?php

namespace App\Services\Operations;

use App\Models\Department;
use App\Models\TicketCategory;
use App\Models\TicketReason;
use App\Models\Person;
use App\Models\Depot;
use App\Models\City;
use App\Models\User;

class OperationsCatalogService
{
    public function getCatalogs(int $accountId): array
    {
        $departments = Department::with(['ticketCategories.reasons'])
            ->where('account_id', $accountId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $workers = Person::where('account_id', $accountId)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->where('is_employee', true)
                  ->orWhere('is_outsourced', true);
            })
            ->select('id', 'name', 'document_number', 'phone', 'is_employee', 'is_outsourced')
            ->orderBy('name')
            ->get();

        $operators = User::where('account_id', $accountId)
            ->where('status', 'active')
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        $depots = Depot::with('cluster')
            ->where('account_id', $accountId)
            ->where('is_active', true)
            ->select('id', 'name', 'code', 'type', 'cluster_id')
            ->orderBy('name')
            ->get();

        return [
            'departments' => $departments,
            'workers' => $workers,
            'operators' => $operators,
            'depots' => $depots,
        ];
    }
}
