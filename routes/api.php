<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeaveRequestController;


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






Route::middleware('auth:sanctum')->group(function () {
    Route::post('/leave-requests', [LeaveRequestController::class, 'store']); // User: Submit leave request
    Route::get('/leave-requests', [LeaveRequestController::class, 'index']); // Admin: View all requests
    Route::patch('/leave-requests/{id}', [LeaveRequestController::class, 'updateStatus']); // Admin: Approve/Reject
});




// routes/api.php


Route::middleware('auth:sanctum')->group(function () {
    // Admin route to manage leave requests (view all and approve/reject)
    Route::post('/admin/manage-leave-requests', [LeaveRequestController::class, 'manageLeaveRequests']);
});


// admin crud operation on attendance
// Route::middleware('auth:sanctum')->group(function () {
//     // Admin routes
//     Route::get('/attendances', [AttendanceController::class, 'getAllAttendances']); // View all
//     Route::post('/attendances', [AttendanceController::class, 'addAttendance']);   // Add attendance
//     Route::put('/attendances/{id}', [AttendanceController::class, 'updateAttendance']); // Update
//     Route::delete('/attendances/{id}', [AttendanceController::class, 'deleteAttendance']); // Delete
// });


Route::middleware('auth:sanctum')->group(function () {
    // Get all attendance records
    Route::get('/attendances', [AttendanceController::class, 'index']);
    
    // Add attendance
    Route::post('/attendances', [AttendanceController::class, 'store']);
    
    // Update attendance based on filters (for example, by student_id or date)
    Route::put('/attendances/update', [AttendanceController::class, 'update']); 
    
    // Delete attendance based on filters (e.g., by student_id or date)
    Route::delete('/attendances/delete', [AttendanceController::class, 'destroy']);
});
