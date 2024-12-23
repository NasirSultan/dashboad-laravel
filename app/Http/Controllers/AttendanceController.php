<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\User;

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
    
        // Log the response for debugging
        \Log::info('Leave request sent successfully');
    
        // Return JSON response
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




// admin crud

// Get all attendances

public function index()
{
    try {
        // Eager load the user relationship
        $attendances = Attendance::with('user')->get();

        // Format the data to include user names
        $attendancesWithNames = $attendances->map(function($attendance) {
            return [
                'attendance_id' => $attendance->id,
                'user_id' => $attendance->user_id,
                'status' => $attendance->status,
                'date' => $attendance->date,
                'user_name' => $attendance->user->name, // Access the user's name
            ];
        });

        return response()->json($attendancesWithNames);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


// Add attendance
public function store(Request $request)
{
    try {
        // Validate input
        $request->validate([
            'user_id' => 'required|exists:users,id',  // Ensure the user exists
            'status' => 'required|string',
            'date' => 'required|date',
        ]);

        // Create new attendance record
        $attendance = new Attendance();
        $attendance->user_id = $request->user_id;  // Store user_id instead of student_id
        $attendance->status = $request->status;
        $attendance->date = $request->date;
        $attendance->save();

        // Retrieve the attendance record along with the user name
        $attendance = Attendance::with('user')
            ->where('id', $attendance->id)
            ->first();

        // Return the response with the user_name included
        return response()->json([
            'message' => 'Attendance added successfully!',
            'attendance' => $attendance,
        ], 201);

    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

// Update attendance based on filters
public function update(Request $request)
{
    try {
        // Assuming you're updating based on user_id and date
        $attendance = Attendance::where('user_id', $request->user_id)
                                ->where('date', $request->date)
                                ->first();

        if ($attendance) {
            $attendance->status = $request->status;
            $attendance->save();
            return response()->json(['message' => 'Attendance updated successfully!']);
        } else {
            return response()->json(['error' => 'Attendance not found'], 404);
        }
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


// Delete attendance based on filters
public function destroy(Request $request)
{
    try {
        // Assuming you're deleting based on user_id and date
        $attendance = Attendance::where('user_id', $request->user_id)
                                ->where('date', $request->date)
                                ->first();

        if ($attendance) {
            $attendance->delete();
            return response()->json(['message' => 'Attendance deleted successfully!']);
        } else {
            return response()->json(['error' => 'Attendance not found'], 404);
        }
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

}


