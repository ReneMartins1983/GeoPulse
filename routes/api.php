<?php

use App\Http\Controllers\Api\StatsController;
use App\Http\Controllers\Api\VehicleController;
use Illuminate\Support\Facades\Route;

// API pública (somente leitura) consumida pelo dashboard
Route::get('/stats', [StatsController::class, 'index']);
Route::get('/vehicles', [VehicleController::class, 'index']);
Route::get('/vehicles/{vehicle}/readings', [VehicleController::class, 'readings']);
