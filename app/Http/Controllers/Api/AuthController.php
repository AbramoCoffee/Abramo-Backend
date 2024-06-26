<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role' => 'required|in:owner,kitchen,cashier',
        ]);


        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ];

        //create product
        $user = User::create($data);

        //return response
        if ($data) {
            return response()->json([
                'status' => 'Success',
                'message' => 'Pengguna berhasil ditambahkan',
                'data' => $user,
            ]);
        } else {
            return response()->json([
                'status' => 'Failed',
                'message' => "Pengguna gagal ditambahkan",
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
