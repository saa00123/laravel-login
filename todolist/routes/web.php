<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\UserController;

Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');
