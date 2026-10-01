<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Docs\OpenApiService;
use Illuminate\Http\JsonResponse;

class OpenApiController extends Controller
{
    public function __construct(
        protected OpenApiService $openApiService
    ) {}

    /**
     * Retorna a especificação canônica OpenAPI 3.1 da API v1.
     */
    public function spec(): JsonResponse
    {
        return response()->json(
            $this->openApiService->getSpec(),
            200,
            ['Content-Type' => 'application/json; charset=UTF-8'],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );
    }
}
