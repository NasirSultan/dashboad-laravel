<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MailController;
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/send-mail', [MailController::class, 'sendMail']);
use App\Http\Controllers\UserController;


Route::resource('users', UserController::class);