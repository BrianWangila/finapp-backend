<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    
    public function register(Request $request) 
    {
        // Validate login request
        try {
            $validated = $request->validate([
                'username' => 'required|string|unique:users',
                'email' => 'required|email|unique:users',
                'password' => 'required|confirmed|min:6',
            ]);
    
            $user = User::create([
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
            ]);

            Auth::login($user);
    
            // return response()->json(['user' => $user, 'token' => $user->createToken('finapp')->plainTextToken]);
            return response()->json(['message' => 'Registered successfully', 'user' => $user]);
       
        } catch (\Throwable $th) {
            return $response = ([
                "message" => "Something went wrong",
                "error" => $th->getMessage(),
            ]);
            return response()->json($response, 500);
        }
        
    }


    // logging in
    public function login(Request $request) 
    {
        try {
            $credentials = $request->only('email', 'password');

            if (!Auth::attempt($credentials)) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }
    
            $user = Auth::user();
            // return response()->json(['user' => $user, 'token' => $user->createToken('finapp')->plainTextToken]);
            return response()->json(['message' => 'Logged in', 'user' => $user]);

        } catch (\Throwable $th) {
            return $response = ([
                "message" => "Something went wrong",
                "error" => $th->getMessage(),
            ]);
            return response()->json($response, 500);
        }
        
    }


    // Logout
    public function logout(Request $request) 
    {
        try {
            $request->user()->currentAccessToken()->delete();
            // Auth::logout();
            return response()->json(['message' => 'Logged out']);
        
        } catch (\Throwable $th) {
            return $response = ([
                "message" => "Something went wrong",
                "error" => $th->getMessage(),
            ]);
            return response()->json($response, 500);
        }
        
    }
}
