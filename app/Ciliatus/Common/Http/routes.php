<?php

use App\Ciliatus\Common\Http\Route;

Route::middleware('api')->prefix('api/v1/common')->group(function () {
    Route::post('broadcast/authenticate', '\App\Ciliatus\Common\Http\Controllers\BroadcastAuthController@authenticate');
    Route::resource('users', App\Ciliatus\Common\Http\Controllers\UserController::class);
    Route::put('alerts/acknowledge', 'App\Ciliatus\Common\Http\Controllers\\AlertController@acknowledge');
});
