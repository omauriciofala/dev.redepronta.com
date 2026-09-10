<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\PersonController;
use App\Http\Controllers\Api\V1\GeoController;
use App\Http\Controllers\Api\V1\IntegrationController;
use App\Http\Controllers\Api\V1\ChangelogController;
use App\Http\Controllers\Api\V1\PersonGroupController;
use App\Http\Controllers\Api\V1\CnaeController;
use App\Http\Controllers\Api\V1\NeighborhoodController;
use App\Http\Controllers\Api\V1\DeveloperController;
use App\Http\Controllers\Api\V1\ClusterController;
use App\Http\Controllers\Api\V1\DepotController;
use App\Http\Controllers\Api\V1\MaterialController;
use App\Http\Controllers\Api\V1\StockController;

Route::prefix('v1')->group(function () {
    // APIs e Integrações (CNPJá, ViaCEP, Catálogo de Serviços)
    Route::get('integrations/list', [IntegrationController::class, 'list']);
    Route::get('integrations/cnpj/{tax_id}', [IntegrationController::class, 'cnpj']);

    // Geografia e Referências Canônicas
    Route::get('states', [GeoController::class, 'states']);
    Route::post('states', [GeoController::class, 'storeState']);
    Route::put('states/{state}', [GeoController::class, 'updateState']);
    Route::delete('states/{state}', [GeoController::class, 'destroyState']);

    Route::get('cities', [GeoController::class, 'cities']);
    Route::post('cities', [GeoController::class, 'storeCity']);
    Route::put('cities/{city}', [GeoController::class, 'updateCity']);
    Route::delete('cities/{city}', [GeoController::class, 'destroyCity']);
    Route::get('genders', [GeoController::class, 'genders']);
    Route::get('cep/{postal_code}', [GeoController::class, 'cep']);

    // Cadastros Básicos
    Route::apiResource('person-groups', PersonGroupController::class);
    Route::apiResource('cnaes', CnaeController::class);
    Route::apiResource('neighborhoods', NeighborhoodController::class);

    // Pessoas
    Route::apiResource('people', PersonController::class);
    Route::patch('people/{person}/toggle-status', [PersonController::class, 'toggleStatus']);

    // Suprimentos, Depósitos Físicos & Clusters Regionais (Sprint 2 - WMS)
    Route::apiResource('clusters', ClusterController::class);
    Route::apiResource('depots', DepotController::class);
    Route::get('materials/units', [MaterialController::class, 'units']);
    Route::apiResource('materials', MaterialController::class);

    // Operações e Posição Regional de Estoque
    Route::prefix('stock')->group(function () {
        Route::get('regional-position', [StockController::class, 'regionalPosition']);
        Route::get('balances', [StockController::class, 'balances']);
        Route::get('serials', [StockController::class, 'serials']);
        Route::post('transfer', [StockController::class, 'transfer']);
        Route::post('entry', [StockController::class, 'entry']);
        Route::get('movements', [StockController::class, 'movements']);
    });

    // Change-log Vivo
    Route::get('changelog', [ChangelogController::class, 'index']);

    // Ferramentas de Desenvolvedor / Sandbox
    Route::prefix('dev')->group(function () {
        Route::get('stats', [DeveloperController::class, 'stats']);
        Route::post('reset', [DeveloperController::class, 'reset']);
        Route::post('populate-people', [DeveloperController::class, 'populatePeople']);
    });
});
