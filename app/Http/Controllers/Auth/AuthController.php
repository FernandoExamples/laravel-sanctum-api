<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $body = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed'
        ]);

        $user = User::create([
            'name' => $body['name'],
            'email' => $body['email'],
            'password' => Hash::make($body['password']),
        ]);

        $user->sendEmailVerificationNotification();

        $token = $user->createToken('myapptokenkey')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response()->json($response, 201);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return [
            'message' => 'Loggued Out'
        ];
    }

    public function login(Request $request)
    {
        $body = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        if (!Auth::attempt($body)) {
            return response()->json([
                'message' => 'Bad credentials',
            ], 401);
        }

        $user = Auth::user();

        $token = $user->createToken('token')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token,
        ];

        return response()->json($response, 201);
    }

    public function getUser(Request $request)
    {
        return $request->user();
    }

    
}
