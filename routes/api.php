<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

// This route requires the user to be authenticated via Sanctum.
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Registration route (no authentication required for registration)
Route::post('/name', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);