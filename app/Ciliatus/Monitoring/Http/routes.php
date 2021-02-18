<?php

use App\Ciliatus\Common\Http\Route;

Route::middleware('api')->prefix('api/v1/monitoring')->group(function () {
    Route::resource('physical_sensors', App\Ciliatus\Monitoring\Http\Controllers\PhysicalSensorController::class);
    Route::resource('logical_sensors', App\Ciliatus\Monitoring\Http\Controllers\LogicalSensorController::class);
    Route::get('logical_sensors/{logical_sensor}/readings/{start}/{end}', '\App\Ciliatus\Monitoring\Http\Controllers\LogicalSensorController@readings');
    Route::post('ingest', 'App\Ciliatus\Monitoring\Http\Controllers\IngestController@ingest');
    Route::post('batch_ingest', 'App\Ciliatus\Monitoring\Http\Controllers\IngestController@batch_ingest');
});
