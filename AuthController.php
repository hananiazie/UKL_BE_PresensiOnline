<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
{
    // Validasi menggunakan username dan password
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'username' => 'required|string|unique:users,username|max:255', // Validasi untuk username
        'password' => 'required|string|min:8|confirmed', // Password harus minimal 8 karakter dan cocok dengan konfirmasi
        'role' => 'required|string|in:siswa,karyawan,admin', // Role
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'errors' => $validator->errors()
        ]);
    }

    // Proses registrasi user
    $user = User::create([
        'name' => $request->name,
        'username' => $request->username, // Simpan username
        'password' => Hash::make($request->password), // Hash password
        'role' => $request->role, // Simpan role
    ]);

    // Membuat token JWT setelah registrasi berhasil
    $token = JWTAuth::fromUser($user);

    return response()->json([
        'status' => true,
        'message' => 'User registered successfully',
        'token' => $token
    ]);
}

}
