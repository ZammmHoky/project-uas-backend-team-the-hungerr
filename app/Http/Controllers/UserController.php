<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // Register
    public function register(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|min:6',
            'c_password'    => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'data'    => $validator->errors(),
            ], 400);
        }

        // Simpan data user ke database
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil didaftarkan',
            'data'    => [
                'user' => $user->name,
                'email' => $user->email,
            ],
        ], 201);
    }

    // Login
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'data'    => $validator->errors(),
            ], 400);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'data'    => [
                    'user' => $user->name,
                    'email' => $user->email,
                ],
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Login gagal',
                'data'    => ['error' => 'Email atau password salah'],
            ], 401);
        }
    }
    // Logout
    public function logout()
    {
        Auth::logout();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil',
        ], 200);
    }
}
