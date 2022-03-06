<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArtisanController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/artisan/{command}', [ArtisanController::class, 'runCommand']);