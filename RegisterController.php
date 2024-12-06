<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        // Set validation
        $validator = Validator::make($request->all(), [
            'name'      => 'required',
            'username'  => 'required|unique:users,username', // Ganti dari email ke username
            'password'  => 'required|min:8|confirmed',
            'role'      => 'required|in:siswa,karyawan,admin' // Pastikan role juga ada
        ]);

        // If validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Create user
        $user = User::create([
            'name'      => $request->name,
            'username'  => $request->username, // Ganti email menjadi username
            'password'  => Hash::make($request->password), // Hash password
            'role'      => $request->role, // Tambahkan role jika perlu
        ]);

        // Return response JSON user is created
        if ($user) {
            return response()->json([
                'success' => true,
                'user'    => $user,  
            ], 201);
        }

        // Return JSON process insert failed 
        return response()->json([
            'success' => false,
            'message' => 'User creation failed'
        ], 409);
    }
}
