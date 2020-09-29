<?php

use Illuminate\Support\Facades\Route;

Route::middleware('api')->prefix('api/v1/monitoring')->group(function () {
    Route::resource('physical_sensors', App\Ciliatus\Monitoring\Http\Controllers\PhysicalSensorController::class);
    Route::resource('logical_sensors', App\Ciliatus\Monitoring\Http\Controllers\LogicalSensorController::class);
    Route::post('ingest', 'App\Ciliatus\Monitoring\Http\Controllers\IngestController@ingeste');
    Route::post('batch_ingest', 'App\Ciliatus\Monitoring\Http\Controllers\IngestController@batch_ingest');
});
