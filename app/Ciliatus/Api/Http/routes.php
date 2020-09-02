<?php

use Illuminate\Support\Facades\Route;

Route::middleware('api')->prefix('api/v1/auth')->group(function () {
    Route::get('check', 'App\Ciliatus\Api\Http\Controllers\AuthController@check__show');
});
