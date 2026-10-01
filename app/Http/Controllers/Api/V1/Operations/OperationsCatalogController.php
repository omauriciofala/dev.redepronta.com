<?php

namespace App\Http\Controllers\Api\V1\Operations;

use App\Http\Controllers\Controller;
use App\Services\Operations\OperationsCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OperationsCatalogController extends Controller
{
    public function __construct(
        protected OperationsCatalogService $catalogService
    ) {}

    protected function getAccountId(): int
    {
        return session('active_account_id') ?? \App\Models\Account::first()?->id ?? 1;
    }

    public function index(): JsonResponse
    {
        $catalogs = $this->catalogService->getCatalogs($this->getAccountId());

        return response()->json(['data' => $catalogs]);
    }
}
