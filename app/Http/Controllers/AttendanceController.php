<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    // Store or update attendance for the logged-in student
    public function markAttendance(Request $request)
    {
        $request->validate([
            'status' => 'required|in:present,absent',
        ]);
    
        // Get the currently authenticated user (student)
        $user = Auth::user();
    
        // Check if the attendance for today already exists (ignoring the time part)
        $attendance = DB::table('attendances')
                        ->where('user_id', $user->id)
                        ->whereDate('date', Carbon::today())  // Only compare the date, ignore time
                        ->first();
    
        if ($attendance) {
            // If attendance exists, update the status
            DB::table('attendances')
                ->where('user_id', $user->id)
                ->whereDate('date', Carbon::today())  // Only compare the date, ignore time
                ->update(['status' => $request->status]);
        } else {
            // If attendance doesn't exist, create a new record
            DB::table('attendances')->insert([
                'user_id' => $user->id,
                'date' => Carbon::today(),  // Store the full date
                'status' => $request->status,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    
        return response()->json(['message' => 'Attendance marked successfully']);
    }
    



    

    // Get attendance history for the logged-in student
    public function getAttendanceHistory()
    {
        // Get the authenticated user
        $user = Auth::user();

        // Retrieve all attendance records for the user
        $attendance = DB::table('attendances')
                        ->where('user_id', $user->id)
                        ->orderBy('date', 'desc')
                        ->get();

        return response()->json(['attendance' => $attendance]);
    }






    // Calculate the attendance percentage for the logged-in student
    public function getAttendancePercentage()
    {
        $user = Auth::user();

        // Get the total number of attendance records for the user
        $totalAttendance = DB::table('attendances')
                            ->where('user_id', $user->id)
                            ->count();

        // Get the number of days the user was present
        $presentDays = DB::table('attendances')
                         ->where('user_id', $user->id)
                         ->where('status', 'present')
                         ->count();

        // Calculate the attendance percentage
        $percentage = ($totalAttendance > 0) ? ($presentDays / $totalAttendance) * 100 : 0;

        return response()->json(['attendance_percentage' => round($percentage, 2)]);
    }









    public function sendLeaveRequest(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:255',
        ]);

        // Get the authenticated user
        $user = Auth::user();

        // Create a new leave request
        DB::table('leave_requests')->insert([
            'user_id' => $user->id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'status' => 'pending', // Default status is pending
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Leave request sent successfully']);
    }










    public function checkLeaveStatus()
    {
        // Get the authenticated user
        $user = Auth::user();

        // Retrieve all leave requests for the user
        $leaveRequests = DB::table('leave_requests')
                            ->where('user_id', $user->id)
                            ->orderBy('created_at', 'desc')
                            ->get();

        return response()->json(['leave_requests' => $leaveRequests]);
    }
}


