<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


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

            do {
                $idNumber = mt_rand(1000000, 9999999);
            } while (User::where('id_number', $idNumber)->exists());

    
            $user = User::create([
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
                'id_number' => $idNumber,
            ]);

            $token = $user->createToken('finapp')->plainTextToken;

            return response()->json([
                'message' => 'Registered successfully',
                'user' => $user,
                'token' => $token
            ]);

            // Auth::login($user);
            // return response()->json(['user' => $user, 'token' => $user->createToken('finapp')->plainTextToken]);
            // return response()->json(['message' => 'Registered successfully', 'user' => $user]);
       
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
            $token = $user->createToken('finapp')->plainTextToken;
            // return response()->json(['user' => $user, 'token' => $user->createToken('finapp')->plainTextToken]);
            // return response()->json(['message' => 'Logged in', 'user' => $user]);
            
            return response()->json([
                'message' => 'Logged in successfully',
                'user' => $user,
                'token' => $token
            ]);

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


    // Get user
    public function user(Request $request) 
    {
        try {
            return response()->json($request->user());
        
        } catch (\Throwable $th) {
            return $response = ([
                "message" => "Something went wrong",
                "error" => $th->getMessage(),
            ]);
            return response()->json($response, 500);
        }
    }



    public function updateProfile(Request $request)
    {
        try {
            $user = Auth::user();

            // Validate the request
            $validator = Validator::make($request->all(), [
                'username' => 'sometimes|string|max:255|unique:users,username,' . $user->id,
                'email' => 'sometimes|email|max:255|unique:users,email,' . $user->id,
                'password' => 'sometimes|confirmed|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Update fields if provided
            if ($request->has('username')) {
                $user->username = $request->username;
            }

            if ($request->has('email')) {
                // Additional email validation to ensure it's a real email format
                if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
                    return response()->json([
                        'message' => 'Invalid email format',
                        'errors' => ['email' => 'The email must be a valid email address.'],
                    ], 422);
                }
                $user->email = $request->email;
            }

            if ($request->has('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            return response()->json([
                'message' => 'Profile updated successfully',
                'user' => $user,
            ], 200);


        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
