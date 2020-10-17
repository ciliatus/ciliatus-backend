<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;


Route::middleware('web')->group(function () {
    Route::any('{all}', fn() => File::get(public_path() . '/ciliatus_index.html'))
        ->where('all', '^(?!api).*$');
});