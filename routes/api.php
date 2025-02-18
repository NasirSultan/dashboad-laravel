<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MailController;
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/send-mail', [MailController::class, 'sendMail']);
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MessageController;
Route::post('/register', [AuthController::class, 'register']);
Route::post('/message', [MessageController::class, 'scheduleMessage']);

Route::post('/schedule-message', [MessageController::class, 'scheduleMessage']);
Route::get('/scheduled-messages', [MessageController::class, 'getScheduledMessages']);