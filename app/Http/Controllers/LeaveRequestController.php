<?php

// app/Http/Controllers/LeaveRequestController.php
namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;

class LeaveRequestController extends Controller
{
    // Submit a leave request
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'reason' => 'required|string|max:255',
    //         'date' => 'required|date',
    //     ]);

    //     $leaveRequest = LeaveRequest::create([
    //         'user_id' => Auth::id(),
    //         'reason' => $request->reason,
    //         'date' => $request->date,
    //     ]);

    //     return response()->json(['message' => 'Leave request submitted', 'data' => $leaveRequest], 201);
    // }


    public function store(Request $request)
    {
        // Validate the incoming request to ensure 'reason' and 'date' are provided
        $request->validate([
            'reason' => 'nullable|string|max:255', // reason is optional
            'date' => 'required|date|unique:attendances,date', // Ensure no duplicate attendance for the same date
        ]);
    
        // Get the authenticated user's ID
        $userId = Auth::id();
    
        // Check if an attendance record already exists for this user and date
        $attendance = Attendance::where('user_id', $userId)
            ->where('date', $request->date)
            ->first();
    
        if (!$attendance) {
            // If no attendance record exists for this date, create a new attendance record
            $attendance = new Attendance();
            $attendance->user_id = $userId;
            $attendance->status = 'leave';  // Mark as 'leave' status
            $attendance->date = $request->date;
            $attendance->save();
        } else {
            // If the attendance record exists but status is not 'leave', update the status
            if ($attendance->status != 'leave') {
                $attendance->status = 'leave';
                $attendance->save();
            }
        }
    
        // Store the leave request in the LeaveRequest table
        $leaveRequest = LeaveRequest::create([
            'user_id' => $userId,  // This associates the leave request with the user
            'reason' => $request->reason,  // Optional field
            'date' => $request->date,
        ]);
    
        // Return a response with a success message
        return response()->json([
            'message' => 'Leave request submitted successfully',
            'data' => $leaveRequest,
        ], 201);
    }
    


    public function manageLeaveRequests(Request $request)
    {
        // Check if the user is an admin by checking their role
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // Fetch all leave requests
        $leaveRequests = LeaveRequest::with('user')->get();

        // If there is a leave_id and status in the request, handle the status update
        if ($request->has('leave_id') && $request->has('status')) {
            $leaveRequest = LeaveRequest::findOrFail($request->leave_id);
            $leaveRequest->status = $request->status;
            $leaveRequest->save();

            return response()->json(['message' => 'Leave request updated successfully', 'data' => $leaveRequest], 200);
        }

        // Return all leave requests
        return response()->json(['data' => $leaveRequests], 200);
    }
}
