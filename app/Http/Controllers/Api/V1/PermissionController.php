<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Access\AccessService;
use Illuminate\Http\JsonResponse;

class PermissionController extends Controller
{
    public function __construct(
        protected AccessService $accessService
    ) {}

    public function index(): JsonResponse
    {
        $perms = $this->accessService->listPermissions();

        return response()->json([
            'data' => $perms,
        ]);
    }
}
