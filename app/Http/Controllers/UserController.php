<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $req)
    {
        $user = new User;
        $user->name = $req->input('name');
        $user->email = $req->input('email');
        $user->password = Hash::make($req->input('password'));
        $user->save();
        return $user;
    }
    public function login(Request $req)
{
    // Attempt to retrieve the user by email
    $user = User::where('email', $req->email)->first();

    // If the user is not found or the password doesn't match, return an error
    if (!$user || !Hash::check($req->password, $user->password)) {
        return response()->json([
            'error' => 'Email or password is incorrect'
        ], 401);  // 401 Unauthorized status code
    }

    // Hide sensitive fields like 'password' before returning the user data
    $user->makeHidden(['password']);

    // Return the user data (with password hidden)
    return response()->json([
        'user' => $user,
    ]);
}
}
