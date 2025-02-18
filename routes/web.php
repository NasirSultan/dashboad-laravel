<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\UserController;


Route::resource('users', UserController::class);
Route::post('/register', [UserController::class, 'register']);