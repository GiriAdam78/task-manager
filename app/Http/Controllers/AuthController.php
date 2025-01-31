<?php

namespace App\Http\Controllers;

use Hash;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'User sudah ada, Silahkan Buat User Baru'], 422);
        }

        $user = User::create([
            'name' => $validator['name'],
            'email' => $validator['email'],
            'password' => Hash::make($validator['password']),
        ]);

        return response()->json(['message' => 'User registered successfully']);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!auth()->attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials',
                'error'   => 'Email Salah !',
            ], 401);
        }

        $token = $request->user()->createToken('API Token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'email' => $credentials['email']
        ]);
    }

    public function profile(Request $request)
    {
        return response()->json($request->user());
    }

}
