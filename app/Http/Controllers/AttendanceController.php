<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\User;
use Barryvdh\DomPDF\Facade as PDF;

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

// app/Http/Controllers/AttendanceController.php
// public function getUserSpecificReport(Request $request)
// {
//     // Get the authenticated user
//     $user = $request->user();

//     // Check if the authenticated user is not an admin
//     if ($user->role !== 'admin') {
//         return response()->json(['error' => 'Unauthorized'], 403); // Unauthorized if not admin
//     }

//     // Validate input data
//     $validated = $request->validate([
//         'user_id' => 'required|exists:users,id',
//         'from_date' => 'required|date',
//         'to_date' => 'required|date',
//     ]);

//     // Fetch attendance records for the specific user within the date range
//     $attendance = Attendance::where('user_id', $validated['user_id'])
//         ->whereBetween('date', [$validated['from_date'], $validated['to_date']])
//         ->join('users', 'users.id', '=', 'attendances.user_id') // Corrected table name
//         ->select('attendances.*', 'users.name') // Corrected table name
//         ->get();

//     // Return the attendance data as JSON
//     return response()->json($attendance);
// }

public function getUserSpecificReport(Request $request)
{
    // Get the authenticated user
    $user = $request->user();

    // Check if the authenticated user is an admin
    if ($user->role !== 'admin') {
        return response()->json(['error' => 'Unauthorized'], 403); // Unauthorized if not admin
    }

    // Validate input data
    $validated = $request->validate([
        'user_id' => 'nullable|exists:users,id', // Ensure user_id is valid if provided
        'from_date' => 'required|date|before_or_equal:to_date', // Ensure from_date is before to_date
        'to_date' => 'required|date',
    ]);

    // Build the query
    $query = Attendance::query()
        ->whereBetween('attendances.date', [$validated['from_date'], $validated['to_date']])
        // Left join with leave_requests table to get the leave status
        ->leftJoin('leave_requests', function ($join) {
            $join->on('leave_requests.user_id', '=', 'attendances.user_id')
                ->on('leave_requests.date', '=', 'attendances.date');
        })
        // Join with the users table to get the user's name
        ->join('users', 'users.id', '=', 'attendances.user_id') 
        ->select('attendances.*', 'users.name', 'leave_requests.status as leave_status');

    // If user_id is provided, filter by the specific user
    if (isset($validated['user_id']) && $validated['user_id']) {
        $query->where('attendances.user_id', $validated['user_id']);
    }

    // Execute the query
    $attendance = $query->get();

    // If no records found, return an appropriate message
    if ($attendance->isEmpty()) {
        return response()->json(['message' => 'No attendance records found for the given criteria.'], 404);
    }

    // Return the attendance data as JSON
    return response()->json($attendance);
}




public function attendanceGradeReportForSelectedOrAllUsers(Request $request)
{
    // Ensure the user is authenticated
    $user = $request->user();

    // Check if the authenticated user is an admin
    if ($user->role !== 'admin') {
        return response()->json(['error' => 'Unauthorized'], 403); // Unauthorized if not admin
    }

    // Validate the request
    $validated = $request->validate([
        'user_ids' => 'nullable|array', // user_ids are optional
        'user_ids.*' => 'exists:users,id', // Ensure all user IDs exist in the users table, if provided
        'from_date' => 'required|date',
        'to_date' => 'required|date|after_or_equal:from_date',
    ]);

    // Get the list of user IDs from the request, if provided
    $userIds = $request->input('user_ids', []); // Default to an empty array if no user_ids are provided

    // Fetch users with the role 'user', either specific user IDs or all
    if (!empty($userIds)) {
        $users = User::where('role', 'user')->whereIn('id', $userIds)->get();
    } else {
        $users = User::where('role', 'user')->get(); // Fetch all users with role 'user'
    }

    // Initialize an empty array to store the attendance report
    $attendanceReport = [];

    // Loop through each selected user (or all users) and generate their attendance report
    foreach ($users as $user) {
        // Get attendance records for the user within the specified date range
        $attendanceRecords = Attendance::where('user_id', $user->id)
                                        ->whereBetween('date', [$validated['from_date'], $validated['to_date']])
                                        ->get();

        // Initialize counters for Present, Absent, and Leave days
        $presentCount = 0;
        $absentCount = 0;
        $leaveCount = 0;

        // Loop through the attendance records
        foreach ($attendanceRecords as $record) {
            if ($record->status === 'present') {
                $presentCount++;
            } elseif ($record->status === 'absent') {
                $absentCount++;
            } elseif ($record->status === 'leave') {
                $leaveCount++;
            }
        }

        // Calculate grade based on present days
        $grade = $this->calculateGrade($presentCount);

        // Add the user's attendance data to the report
        $attendanceReport[] = [
            'user_id' => $user->id, // Ensure the user ID is included
            'user_name' => $user->name,
            'attendance_period' => "{$validated['from_date']} to {$validated['to_date']}",
            'present_days' => $presentCount,
            'absent_days' => $absentCount,
            'leave_days' => $leaveCount,
            'grade' => $grade,
        ];
    }

    // Return the final attendance report
    return response()->json($attendanceReport);
}

// Helper function to calculate grade based on present days
public function calculateGrade($attendanceCount)
{
    if ($attendanceCount >= 16) {
        return 'A';
    } elseif ($attendanceCount >= 13) {
        return 'B';
    } elseif ($attendanceCount >= 10) {
        return 'C';
    } elseif ($attendanceCount >= 6) {
        return 'D';
    } else {
        return 'F';
    }
}





}


