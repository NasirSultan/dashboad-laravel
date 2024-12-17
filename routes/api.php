<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// routes/api.php

use App\Http\Controllers\ClassroomController;

Route::get('/classrooms', [ClassroomController::class, 'index']); // Get all classrooms
Route::get('/classrooms/{id}', [ClassroomController::class, 'show']);
Route::get('/classroom', [ClassroomController::class, 'show']);