<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\DataWarehouseController;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\PropertyApiController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::prefix('analytics')->name('analytics.')->group(function () {
    Route::get('/', [AnalyticsController::class, 'index'])->name('index');
    Route::get('/by-city', [AnalyticsController::class, 'byCity'])->name('byCity');
    Route::get('/by-legal', [AnalyticsController::class, 'byLegal'])->name('byLegal');
    Route::get('/by-furniture', [AnalyticsController::class, 'byFurnitureState'])->name('byFurniture');
    Route::get('/by-bedrooms', [AnalyticsController::class, 'byBedrooms'])->name('byBedrooms');
});

Route::prefix('datawarehouse')->name('warehouse.')->group(function () {
    Route::get('/', [DataWarehouseController::class, 'index'])->name('index');
    Route::get('/iceberg-cube', [DataWarehouseController::class, 'icebergCube'])->name('icebergCube');
});

Route::prefix('iceberg-cube')->name('iceberg.')->group(function () {
    Route::get('/', [DataWarehouseController::class, 'icebergCube'])->name('index');
});

Route::prefix('predict')->name('predict.')->group(function () {
    Route::get('/', [PredictionController::class, 'index'])->name('index');
    Route::post('/', [PredictionController::class, 'predict'])->name('predict');
});

Route::prefix('api')->name('api.')->group(function () {
    Route::get('/properties', [PropertyApiController::class, 'index'])->name('properties.index');
    Route::get('/properties/{id}', [PropertyApiController::class, 'show'])->name('properties.show');
    Route::get('/analytics', [PropertyApiController::class, 'analytics'])->name('analytics');
    Route::get('/statistics', [PropertyApiController::class, 'statistics'])->name('statistics');
    Route::post('/predict', [PredictionController::class, 'predict'])->name('predict');
});
