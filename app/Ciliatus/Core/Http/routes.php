<?php

use App\Ciliatus\Common\Http\Route;

Route::middleware('api')->prefix('api/v1/core')->group(function () {
    Route::resource('locations', App\Ciliatus\Core\Http\Controllers\LocationController::class);
    Route::resource('habitats', App\Ciliatus\Core\Http\Controllers\HabitatController::class);
    Route::resource('habitat_types', App\Ciliatus\Core\Http\Controllers\HabitatTypeController::class);
    Route::resource('animals', App\Ciliatus\Core\Http\Controllers\AnimalController::class);
    Route::resource('agents', App\Ciliatus\Core\Http\Controllers\AgentController::class);
});
