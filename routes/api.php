<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectController;

// This route requires the user to be authenticated via Sanctum.
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Registration route (no authentication required for registration)
Route::post('/name', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

// addproject route -
Route::post('/projects', [ProjectController::class, 'store']);

// list rouete
Route::get('/list', [ProjectController::class, 'list']);

// delete rouete
Route::delete('/delete/{id}', [ProjectController::class, 'delete']);
Route::get('/product/{id}', [ProjectController::class, 'getproduct']);
Route::put('/update/{id}', [ProjectController::class, 'updateproduct']);
Route::get('/search/{key}', [ProjectController::class, 'search']);