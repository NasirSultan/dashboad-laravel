<?php

// app/Http/Controllers/LeaveRequestController.php
namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveRequestController extends Controller
{
    // Submit a leave request
    public function store(Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
            'date' => 'required|date',
        ]);

        $leaveRequest = LeaveRequest::create([
            'user_id' => Auth::id(),
            'reason' => $request->reason,
            'date' => $request->date,
        ]);

        return response()->json(['message' => 'Leave request submitted', 'data' => $leaveRequest], 201);
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
