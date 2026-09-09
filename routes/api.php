<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\PersonController;
use App\Http\Controllers\Api\V1\GeoController;
use App\Http\Controllers\Api\V1\IntegrationController;

use App\Http\Controllers\Api\V1\ChangelogController;

Route::prefix('v1')->group(function () {
    // APIs e Integrações (CNPJá, ViaCEP, Catálogo de Serviços)
    Route::get('integrations/list', [IntegrationController::class, 'list']);
    Route::get('integrations/cnpj/{tax_id}', [IntegrationController::class, 'cnpj']);

    // Geografia e Referências Canônicas
    Route::get('states', [GeoController::class, 'states']);
    Route::get('cities', [GeoController::class, 'cities']);
    Route::get('genders', [GeoController::class, 'genders']);
    Route::get('cep/{postal_code}', [GeoController::class, 'cep']);

    // Pessoas
    Route::apiResource('people', PersonController::class);
    Route::patch('people/{person}/toggle-status', [PersonController::class, 'toggleStatus']);

    // Change-log Vivo
    Route::get('changelog', [ChangelogController::class, 'index']);
});
