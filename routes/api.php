<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\PersonController;
use App\Http\Controllers\Api\V1\GeoController;

Route::prefix('v1')->group(function () {
    // Geografia e Referências Canônicas
    Route::get('states', [GeoController::class, 'states']);
    Route::get('cities', [GeoController::class, 'cities']);
    Route::get('genders', [GeoController::class, 'genders']);

    // Pessoas
    Route::apiResource('people', PersonController::class);
    Route::patch('people/{person}/toggle-status', [PersonController::class, 'toggleStatus']);
});
