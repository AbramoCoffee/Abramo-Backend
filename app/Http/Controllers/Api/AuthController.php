<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //login api
    public function login(Request $request)
    {
        //validate the request...
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        //check if the user exists
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        //check if the password is correct
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials'
            ], 401);
        }

        //generate token
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil Login',
            'token' => $token,
            'user' => $user
        ], 200);
    }

    public function register(Request $request)
    {
        // validate the request...
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role' => 'required|in:owner,kitchen,cashier',
        ]);

        // store the request...
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = $request->role;
        $save = $user->save();

        if ($save) {
            return response()->json([
                'status' => 'success',
                'message' => 'Register berhasil',
                'user' => $user
            ], 200);
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Register gagal',
                'user' => $user
            ], 400);
        }
    }

    //logout
    public function logout(Request $request)
    {
        $logout = $request->user()->currentAccessToken()->delete();

        if ($logout) {
            return response()->json([
                'status' => 'success',
                'message' => 'Logged out'
            ], 200);
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Logged out'
            ], 400);
        }
    }
}
