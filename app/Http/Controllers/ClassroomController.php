<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Auth;

// method 1

class ClassroomController extends Controller
{
  
//     public function index()
//     {
//         $classrooms = DB::table('classrooms')
//             ->join('students', 'classrooms.student_id', '=', 'students.id')
//             ->join('subjects', 'classrooms.subject_id', '=', 'subjects.id') 
//             // ->select('classrooms.id as classroom_id', 'classrooms.name as classroom_name', 
//             //          'students.name as student_name', 'subjects.name as subject_name')
//             ->get(); 

//         return response()->json($classrooms); 
//     }

//     public function show(Request $request)
//     {
//         $search = $request->input('search', ''); 
    
//         $query = DB::table('classrooms')
//             ->leftJoin('students', 'classrooms.student_id', '=', 'students.id')
//             ->leftJoin('subjects', 'classrooms.subject_id', '=', 'subjects.id')
//             ->select('classrooms.id as classroom_id', 
//                      'classrooms.name as classroom_name', 
//                      DB::raw('COALESCE(students.name, "No student assigned") as student_name'),
//                      DB::raw('COALESCE(subjects.name, "No subject assigned") as subject_name'))
//             ->distinct(); 
    
        
//         if ($search) {
//             $query->where(function($q) use ($search) {
//                 $q->where('classrooms.name', 'like', '%'.$search.'%')
//                   ->orWhere('students.name', 'like', '%'.$search.'%')
//                   ->orWhere('subjects.name', 'like', '%'.$search.'%');
//             });
//         }
    
       
//         $classrooms = $query->get();
    
        
//         if ($classrooms->isEmpty()) {
//             return response()->json(['message' => 'No classrooms found'], 404);
//         }
    
//         return response()->json($classrooms); 
//     }
    




// Method 2

// class ClassroomController extends Controller
// {
//     // Reusable query to fetch classroom data
//     private function getClassroomsQuery()
//     {
//         return DB::table('classrooms')
//             ->join('students', 'classrooms.student_id', '=', 'students.id')
//             ->join('subjects', 'classrooms.subject_id', '=', 'subjects.id')
//             ->select(
//                 'classrooms.id as classroom_id',
//                 'classrooms.name as classroom_name',
//                 'students.name as student_name',
//                 'subjects.name as subject_name'
//             );
//     }

//     // Display all classrooms
//     public function index()
//     {
//         $classrooms = $this->getClassroomsQuery()->get();

//         return response()->json($classrooms);
//     }

//     // Search classrooms
//     public function show(Request $request)
//     {
//         $search = $request->input('search');

//         $query = $this->getClassroomsQuery();

//         if ($search) {
//             $query->where(function ($q) use ($search) {
//                 $q->where('classrooms.name', 'like', '%' . $search . '%')
//                   ->orWhere('students.name', 'like', '%' . $search . '%')
//                   ->orWhere('subjects.name', 'like', '%' . $search . '%');
//             });
//         }

//         $classrooms = $query->get();

//         if ($classrooms->isEmpty()) {
//             return response()->json(['message' => 'No classrooms found'], 404);
//         }

//         return response()->json($classrooms);
//     }
// }







public function register(Request $req)
{
    // Validate input
    $validator = Validator::make($req->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
       // Ensure password is provided and confirmed
        'role' => 'required|in:user,admin',  // Ensure role is either 'user' or 'admin'
        // Optional profile picture validation
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 400);
    }

    // Check if the user is an admin
    if ($req->role == 'admin') {
        // Additional check to prevent more than one admin
        $adminCount = User::where('role', 'admin')->count();
        if ($adminCount > 0) {
            return response()->json(['error' => 'Only one admin is allowed'], 400);
        }
    }

    // Create the user
    $user = new User;
    $user->name = $req->input('name');
    $user->email = $req->input('email');
    $user->password = Hash::make($req->input('password'));
    $user->role = $req->input('role');
    $user->email_verified_at = null; // Or set this to current timestamp if required

    // If a profile picture is uploaded, store it
    if ($req->hasFile('profile_picture')) {
        $imagePath = $req->file('profile_picture')->store('profile_pictures', 'public');
        $user->profile_picture = $imagePath;
    }

    // Save the user to the database
    $user->save();

    // Return success response with the newly created user
    return response()->json([
        'message' => 'User registered successfully',
        'user' => $user
    ], 201);


    
}



    // Login method
    

    public function login(Request $req)
{
    // Validate login credentials
    $validator = Validator::make($req->all(), [
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 400);
    }

    // Attempt to retrieve the user by email
    $user = User::where('email', $req->email)->first();

    // If the user is not found or the password doesn't match, return an error
    if (!$user || !Hash::check($req->password, $user->password)) {
        return response()->json([
            'error' => 'Email or password is incorrect'
        ], 401);  // 401 Unauthorized status code
    }

    // Hide sensitive fields like 'password' before returning the user data
    $user->makeHidden(['password', 'remember_token']);

    // Create a token for the user
    $token = $user->createToken('YourAppName')->plainTextToken;

    // Return the user data and the token
    return response()->json([
        'user' => $user,
        'token' => $token
    ]);
}



   // Method to view user details using token authentication


public function getUserDetails(Request $request)
{
    $user = Auth::user(); // Get the authenticated user
    return response()->json([
        'name' => $user->name,
        'email' => $user->email,
        'role' => $user->role,
        'profile_picture' => $user->profile_picture, // Return profile picture URL
    ]);
}

   




public function updateProfile(Request $req)
    {
        // Validate the input fields (only if they are present)
        $validator = Validator::make($req->all(), [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . auth()->user()->id,
            'password' => 'nullable|min:6',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'role' => 'nullable|in:user,admin',
        ]);

        // Check validation failure
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Retrieve authenticated user
        $user = auth()->user();

        // Update name if provided
        if ($req->has('name')) {
            $user->name = $req->input('name');
        }

        // Update email if provided
        if ($req->has('email')) {
            $user->email = $req->input('email');
        }

        // Update password if provided
        if ($req->has('password')) {
            $user->password = Hash::make($req->input('password'));
        }

        // Update profile picture if provided
        if ($req->hasFile('profile_picture')) {
            // Delete the old profile picture if exists (optional)
            if ($user->profile_picture && file_exists(storage_path('app/public/' . $user->profile_picture))) {
                unlink(storage_path('app/public/' . $user->profile_picture));
            }

            // Store the new profile picture
            $imagePath = $req->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $imagePath;
        }

        // Update role if provided (make sure not to change admin role if there's already an admin)
        if ($req->has('role') && $req->input('role') != 'admin') {
            $user->role = $req->input('role');
        }

        // Save the updated user information
        $user->save();

        // Return a successful response
        return response()->json([
            'message' => 'Profile updated successfully.',
            'user' => $user
        ], 200);
    }

}