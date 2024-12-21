<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// routes/api.php

use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\AttendanceController;
Route::get('/classroom', [ClassroomController::class, 'index']); // Get all classrooms
// Route::get('/classrooms/{id}', [ClassroomController::class, 'show']);
Route::get('/classroom', [ClassroomController::class, 'show']);



Route::post('/register', [ClassroomController::class, 'register']);

Route::post('/login', [ClassroomController::class, 'login']);



Route::middleware('auth:sanctum')->group(function () {
    // Mark attendance (POST request)
    Route::post('/attendance', [AttendanceController::class, 'markAttendance']);

    // Get attendance history (GET request)
    Route::get('/attendance/history', [AttendanceController::class, 'getAttendanceHistory']);
});


Route::get('/percentage', [AttendanceController::class, 'getAttendancePercentage'])->middleware('auth:sanctum');

Route::post('/leave-request', [AttendanceController::class, 'sendLeaveRequest'])->middleware('auth:sanctum');

Route::get('/check-request', [AttendanceController::class, 'checkLeaveStatus'])->middleware('auth:sanctum');




// routes/api.php
Route::middleware('auth:sanctum')->get('/user', [ClassroomController::class, 'getUserDetails']);


Route::middleware('auth:sanctum')->put('/user/update-profile', [ClassroomController::class, 'updateProfile']);
