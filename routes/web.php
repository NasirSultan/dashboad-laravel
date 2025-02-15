<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


use App\Http\Controllers\UserController;

Route::get('/greet', [UserController::class, 'greetUser']);
Route::get('/continer', [UserController::class, 'Container']);