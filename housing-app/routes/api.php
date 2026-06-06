<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\PropertyApiController;

Route::prefix('v1')->group(function () {
    // Property CRUD
    Route::get('/properties', [PropertyApiController::class, 'index']);
    Route::get('/properties/{id}', [PropertyApiController::class, 'show']);

    // Analytics
    Route::get('/analytics', [PropertyApiController::class, 'analytics']);
    Route::get('/statistics', [PropertyApiController::class, 'statistics']);

    // Data Warehouse
    Route::get('/warehouse', [PropertyApiController::class, 'warehouse']);

    // Prediction
    Route::post('/predict', [PropertyApiController::class, 'predict']);
    Route::get('/model-info', [PropertyApiController::class, 'modelInfo']);

    // Legacy route
    Route::post('/prediction', [PredictionController::class, 'predict']);
});
