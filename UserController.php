<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return User::all();
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User tidak ditemukan'
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $user
        ]);
    }

    public function createusers(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'name' => 'required',
            'username' => 'required|unique:users,username', // Ganti dari email ke username
            'password' => 'required|min:6',
            'role' => 'required', // Validasi untuk role
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson());
        }

        $save = User::create([
            'name' => $req->get('name'),
            'username' => $req->get('username'), // Ganti dari email ke username
            'password' => Hash::make($req->get('password')),
            'role' => $req->get('role'), // Menyimpan role
        ]);

        if ($save) {
            return response()->json(['status' => true, 'message' => 'Sukses menambah user']);
        } else {
            return response()->json(['status' => false, 'message' => 'Gagal menambah user']);
        }
    }

    public function updateusers(Request $req, $id)
    {
        // Validasi input yang diterima
        $validator = Validator::make($req->all(), [
            'name' => 'required', // Kolom nama wajib diisi
            'username' => 'required|unique:users,username,' . $id, // Validasi username unik kecuali milik user yang sedang diupdate
            'password' => 'nullable|min:6', // Password boleh diupdate, dan minimal 6 karakter
            'role' => 'required|in:siswa,karyawan,admin', // Pastikan role sesuai dengan pilihan yang valid
        ]);
    
        // Jika validasi gagal
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson());
        }
    
        // Mencari user berdasarkan ID
        $user = User::find($id);
    
        // Jika user tidak ditemukan
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not found']);
        }
    
        // Update data user
        $user->name = $req->get('name'); // Update nama
        $user->username = $req->get('username'); // Update username
    
        // Jika password diberikan, maka hash password dan update
        if ($req->has('password') && $req->get('password') !== null) {
            $user->password = Hash::make($req->get('password')); // Menghash password sebelum disimpan
        }
    
        // Update role
        $user->role = $req->get('role');
        
        // Simpan perubahan
        $user->save();
    
        // Response sukses
        return response()->json(['status' => true, 'message' => 'User updated successfully']);
    }    


    public function deleteusers($id)
    {
        $user = User::find($id);

        if ($user) {
            $user->delete();
            return response()->json(['status' => true, 'message' => 'User berhasil dihapus']);
        } else {
            return response()->json(['status' => false, 'message' => 'User tidak ditemukan']);
        }
    }
}
