<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json([
            'success' => true,
            'message' => 'Daftar Pengguna',
            'data'    => $users
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama'     => 'required|string|max:255',
            'username' => 'required|string|unique:users,email', // Kita gunakan kolom email sebagai username untuk sementara agar tidak merubah skema besar
            'password' => 'required|string|min:6',
            'role'     => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'nama'     => $request->nama,
            'email'    => $request->username, // Map username ke email
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil didaftarkan',
            'data'    => $user
        ], 201);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        if ($user->email === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'User sistem tidak dapat dihapus'
            ], 403);
        }

        $user->delete();
        return response()->json([
            'success' => true,
            'message' => 'User berhasil dihapus'
        ], 200);
    }

    public function login(Request $request)
    {
        // Fitur Auto-Create Admin (Jika belum ada user sama sekali)
        if (User::count() === 0) {
            User::create([
                'nama'     => 'Super Admin',
                'email'    => 'admin',
                'password' => Hash::make('admin123'),
                'role'     => 'superadmin',
            ]);
        }

        $user = User::where('email', $request->username)->first();

        // Jika user admin belum ada di database (tapi ada user lain), buatkan khusus untuk login pertama
        if (!$user && $request->username === 'admin' && $request->password === 'admin123') {
             $user = User::create([
                'nama'     => 'Super Admin',
                'email'    => 'admin',
                'password' => Hash::make('admin123'),
                'role'     => 'superadmin',
            ]);
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Username atau password salah'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data'    => [
                'nama' => $user->nama,
                'username' => $user->email,
                'role' => $user->role
            ]
        ], 200);
    }
}
