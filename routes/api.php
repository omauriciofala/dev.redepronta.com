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
use App\Http\Controllers\Api\V1\DepotTypeController;
use App\Http\Controllers\Api\V1\DepotController;
use App\Http\Controllers\Api\V1\MaterialCategoryController;
use App\Http\Controllers\Api\V1\UnitController;
use App\Http\Controllers\Api\V1\MaterialOwnerController;
use App\Http\Controllers\Api\V1\OwnerMaterialController;
use App\Http\Controllers\Api\V1\MaterialController;
use App\Http\Controllers\Api\V1\StockController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\RoleController;
use App\Http\Controllers\Api\V1\PermissionController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\OpenApiController;

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

    // Autenticação & Sessão
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });

    // Gestão de Usuários, Papéis de Usuários e Permissões
    Route::apiResource('users', UserController::class);
    Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus']);
    Route::apiResource('roles', RoleController::class);
    Route::get('permissions', [PermissionController::class, 'index']);

    // Suprimentos, Depósitos Físicos & Clusters Regionais (Sprint 2 - WMS)
    Route::apiResource('clusters', ClusterController::class);
    Route::apiResource('depot-types', DepotTypeController::class);
    Route::apiResource('depots', DepotController::class);
    Route::apiResource('units', UnitController::class);
    Route::apiResource('material-categories', MaterialCategoryController::class);
    Route::apiResource('material-owners', MaterialOwnerController::class);
    Route::get('material-owners/{materialOwner}/materials/template', [OwnerMaterialController::class, 'template']);
    Route::post('material-owners/{materialOwner}/materials/preview', [OwnerMaterialController::class, 'preview']);
    Route::post('material-owners/{materialOwner}/materials/import', [OwnerMaterialController::class, 'import']);
    Route::apiResource('material-owners.materials', OwnerMaterialController::class)->parameters([
        'material-owners' => 'materialOwner',
        'materials' => 'ownerMaterial',
    ]);
    Route::get('materials/units', [MaterialController::class, 'units']);
    Route::get('materials/import-template', [MaterialController::class, 'downloadTemplate']);
    Route::post('materials/import', [MaterialController::class, 'import']);
    Route::apiResource('materials', MaterialController::class);

    // Operações e Posição Regional de Estoque
    Route::prefix('stock')->group(function () {
        Route::get('regional-position', [StockController::class, 'regionalPosition']);
        Route::get('balances', [StockController::class, 'balances']);
        Route::get('serials', [StockController::class, 'serials']);
        Route::post('movement', [StockController::class, 'movement']);
        Route::post('transfer', [StockController::class, 'transfer']);
        Route::post('entry', [StockController::class, 'entry']);
        Route::post('attachments', [StockController::class, 'uploadAttachment']);
        Route::get('movements', [StockController::class, 'movements']);
        Route::get('documents', [StockController::class, 'documents']);
        Route::get('documents/{protocol}', [StockController::class, 'documentDetail']);
    });

    // Change-log Vivo
    Route::get('changelog', [ChangelogController::class, 'index']);

    // Documentação da API (OpenAPI 3.1)
    Route::get('docs/openapi.json', [OpenApiController::class, 'spec']);

    // Ferramentas de Desenvolvedor / Sandbox
    Route::prefix('dev')->group(function () {
        Route::get('stats', [DeveloperController::class, 'stats']);
        Route::post('reset', [DeveloperController::class, 'reset']);
        Route::post('populate-people', [DeveloperController::class, 'populatePeople']);
    });
});
