<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('welcome');
});

Route::get('/u/reconf/{protocol}', [App\Http\Controllers\Public\ReconfController::class, 'show'])->name('public.reconf');

