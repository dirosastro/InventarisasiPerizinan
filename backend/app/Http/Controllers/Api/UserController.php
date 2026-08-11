<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json([
            'success' => true,
            'message' => 'Daftar Pengguna',
            'data' => $users
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'username' => 'required|string|unique:users,email', // Kita gunakan kolom email sebagai username untuk sementara agar tidak merubah skema besar
            'password' => 'required|string|min:6',
            'role' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->username, // Map username ke email
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        Log::info('New user registered.', [
            'created_by' => auth()->user() ? auth()->user()->email : 'system',
            'new_username' => $user->email,
            'role' => $user->role,
            'ip' => $request->ip()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil didaftarkan',
            'data' => $user
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

        $deletedUsername = $user->email;
        $user->delete();

        Log::info('User deleted.', [
            'deleted_by' => auth()->user() ? auth()->user()->email : 'system',
            'deleted_username' => $deletedUsername,
            'ip' => request()->ip()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dihapus'
        ], 200);
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $throttleKey = 'login-attempts:' . $request->input('username') . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            Log::warning('Login attempt blocked due to rate limiting.', [
                'username' => $request->input('username'),
                'ip' => $request->ip(),
                'seconds_blocked' => $seconds
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terlalu banyak percobaan login. Silakan coba lagi dalam ' . $seconds . ' detik.'
            ], 429);
        }

        // Fitur Auto-Create Admin (Jika belum ada user sama sekali)
        if (User::count() === 0) {
            User::create([
                'nama' => 'Super Admin',
                'email' => 'admin',
                'password' => Hash::make('admin123'),
                'role' => 'superadmin',
            ]);
        }

        $user = User::where('email', $request->username)->first();

        // Jika user admin belum ada di database (tapi ada user lain), buatkan khusus untuk login pertama
        if (!$user && $request->username === 'admin' && $request->password === 'admin123') {
            $user = User::create([
                'nama' => 'Super Admin',
                'email' => 'admin',
                'password' => Hash::make('admin123'),
                'role' => 'superadmin',
            ]);
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            RateLimiter::hit($throttleKey, 60);
            Log::warning('Failed login attempt.', [
                'username' => $request->input('username'),
                'ip' => $request->ip()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Username atau password salah'
            ], 401);
        }

        // Login user secara server-side (tanpa remember token karena kolomnya tidak ada)
        Auth::login($user, false);

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        Log::info('User logged in successfully.', [
            'username' => $user->email,
            'ip' => $request->ip()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => [
                'nama' => $user->nama,
                'username' => $user->email,
                'role' => $user->role
            ]
        ], 200);
    }
}
