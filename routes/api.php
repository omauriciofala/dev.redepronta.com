<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\PersonController;
use App\Http\Controllers\Api\V1\GeoController;
use App\Http\Controllers\Api\V1\IntegrationController;
use App\Http\Controllers\Api\V1\ChangelogController;
use App\Http\Controllers\Api\V1\PersonGroupController;
use App\Http\Controllers\Api\V1\CnaeController;
use App\Http\Controllers\Api\V1\NeighborhoodController;

Route::prefix('v1')->group(function () {
    // APIs e Integrações (CNPJá, ViaCEP, Catálogo de Serviços)
    Route::get('integrations/list', [IntegrationController::class, 'list']);
    Route::get('integrations/cnpj/{tax_id}', [IntegrationController::class, 'cnpj']);

    // Geografia e Referências Canônicas
    Route::get('states', [GeoController::class, 'states']);
    Route::post('states', [GeoController::class, 'storeState']);
    Route::get('cities', [GeoController::class, 'cities']);
    Route::post('cities', [GeoController::class, 'storeCity']);
    Route::put('cities/{city}', [GeoController::class, 'updateCity']);
    Route::get('genders', [GeoController::class, 'genders']);
    Route::get('cep/{postal_code}', [GeoController::class, 'cep']);

    // Cadastros Básicos
    Route::apiResource('person-groups', PersonGroupController::class);
    Route::apiResource('cnaes', CnaeController::class);
    Route::apiResource('neighborhoods', NeighborhoodController::class);

    // Pessoas
    Route::apiResource('people', PersonController::class);
    Route::patch('people/{person}/toggle-status', [PersonController::class, 'toggleStatus']);

    // Change-log Vivo
    Route::get('changelog', [ChangelogController::class, 'index']);
});
