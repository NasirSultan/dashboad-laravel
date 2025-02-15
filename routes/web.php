<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Controller;
Route::get('/integrate-joke', [Controller::class, 'integrate']);

Route::get('/', function () {
    return view('welcome');
});





