<?php

use Illuminate\Support\Facades\Route;

Route::middleware('api')->prefix('api/v1/automation')->group(function () {
    Route::resource('appliances', App\Ciliatus\Automation\Http\Controllers\ApplianceController::class);
    Route::resource('appliance_groups', App\Ciliatus\Automation\Http\Controllers\ApplianceGroupController::class);
    Route::resource('workflow_executions', App\Ciliatus\Automation\Http\Controllers\WorkflowExecutionController::class);
});
