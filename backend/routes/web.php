<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PerizinanController;
use App\Http\Controllers\Api\MasterDataController;
use App\Http\Controllers\Api\DasarHukumController;

// ─── Public Web Routes ───────────────────────────────────────────────────────
Route::get('/', function () { return view('index'); })->name('home');
Route::get('/peta', function () { return view('peta'); })->name('peta');
Route::get('/dasar-hukum', function () { return view('dasar_hukum'); })->name('dasar-hukum');

// ─── Guest Web Route ─────────────────────────────────────────────────────────
Route::get('/login', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('login');
})->name('login');

// ─── Protected Web Routes (All Logged-in Users) ─────────────────────────────
Route::get('/admin', function () {
    if (!auth()->check()) return redirect()->route('login');
    return view('admin');
})->name('admin');

Route::get('/dashboard', function () {
    if (!auth()->check()) return redirect()->route('login');
    return view('dashboard');
})->name('dashboard');

Route::get('/perizinan-data', function () {
    if (!auth()->check()) return redirect()->route('login');
    return view('perizinan');
})->name('perizinan_view');

Route::get('/test-wa', function () {
    if (!auth()->check()) return redirect()->route('login');
    return view('test_wa');
})->name('test-wa');

Route::get('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

// ─── Protected Web Routes (Super Admin Only) ────────────────────────────────
Route::get('/manajemen-user', function () {
    if (!auth()->check()) return redirect()->route('login');
    if (auth()->user()->role !== 'superadmin') return redirect()->route('dashboard');
    return view('manajemen_user');
})->name('users');

// ─── API Routes ──────────────────────────────────────────────────────────────
Route::prefix('api')->group(function () {
    // Public API
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/cs-chat', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'number' => 'required|string',
            'message' => 'required|string',
            'name' => 'nullable|string'
        ]);

        $wa = new \App\Services\WhatsAppService();
        $name = $request->name ?: 'Visitor';
        
        $messageText = "*Pesan Baru dari Website Simpanan*\n\nHalo {$name}, terima kasih telah menghubungi kami.\nPesan Anda:\n\"{$request->message}\"\n\nTunggu sebentar ya, Admin kami akan segera membalas pesan Anda di chat ini.";
        
        $result = $wa->sendMessage($request->number, $messageText);

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Pesan terkirim!']);
        }
        return response()->json(['success' => false, 'message' => 'Gagal mengirim pesan, bot mungkin offline.'], 500);
    });
    Route::get('/perizinan', [PerizinanController::class, 'index']);
    Route::get('/perizinan/{id}', [PerizinanController::class, 'show']);
    Route::get('/perizinan/download/{id}', [PerizinanController::class, 'download']);
    Route::get('/satker', [MasterDataController::class, 'getSatker']);
    Route::get('/ppk', [MasterDataController::class, 'getPpk']);
    Route::get('/ruas-jalan', [MasterDataController::class, 'getRuasJalan']);
    Route::get('/dasar-hukum', [DasarHukumController::class, 'index']);
    Route::get('/dasar-hukum/download/{id}/{type}', [DasarHukumController::class, 'downloadFile']);

    // Protected API (All Logged-in Users)
    Route::post('/perizinan', function (\Illuminate\Http\Request $request) {
        if (!auth()->check()) return response()->json(['success' => false, 'message' => 'Sesi Anda telah berakhir.'], 401);
        return app(PerizinanController::class)->store($request);
    });
    Route::post('/perizinan/upload-temp', function (\Illuminate\Http\Request $request) {
        if (!auth()->check()) return response()->json(['success' => false, 'message' => 'Sesi Anda telah berakhir.'], 401);
        return app(PerizinanController::class)->uploadTemp($request);
    });
    Route::post('/perizinan/{id}', function (\Illuminate\Http\Request $request, $id) {
        if (!auth()->check()) return response()->json(['success' => false, 'message' => 'Sesi Anda telah berakhir.'], 401);
        return app(PerizinanController::class)->update($request, $id);
    });
    Route::delete('/perizinan/{id}', function (\Illuminate\Http\Request $request, $id) {
        if (!auth()->check()) return response()->json(['success' => false, 'message' => 'Sesi Anda telah berakhir.'], 401);
        return app(PerizinanController::class)->destroy($id);
    });

    Route::post('/wa-test', function (\Illuminate\Http\Request $request) {
        if (!auth()->check()) return response()->json(['success' => false, 'message' => 'Sesi Anda telah berakhir.'], 401);
        $request->validate([
            'number' => 'required|string',
            'message' => 'required|string'
        ]);

        $wa = new \App\Services\WhatsAppService();
        $result = $wa->sendMessage($request->number, $request->message);

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Pesan terkirim ke bot!', 'data' => $result]);
        }

        return response()->json(['success' => false, 'message' => 'Gagal terhubung ke bot WhatsApp. Pastikan bot aktif.'], 500);
    });

    // Protected API (Super Admin Only)
    Route::get('/users', function (\Illuminate\Http\Request $request) {
        if (!auth()->check()) return response()->json(['success' => false, 'message' => 'Sesi Anda telah berakhir.'], 401);
        if (auth()->user()->role !== 'superadmin') return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        return app(UserController::class)->index($request);
    });
    Route::post('/users', function (\Illuminate\Http\Request $request) {
        if (!auth()->check()) return response()->json(['success' => false, 'message' => 'Sesi Anda telah berakhir.'], 401);
        if (auth()->user()->role !== 'superadmin') return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        return app(UserController::class)->store($request);
    });
    Route::delete('/users/{id}', function (\Illuminate\Http\Request $request, $id) {
        if (!auth()->check()) return response()->json(['success' => false, 'message' => 'Sesi Anda telah berakhir.'], 401);
        if (auth()->user()->role !== 'superadmin') return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        return app(UserController::class)->destroy($id);
    });

    // Dasar Hukum Protected API
    Route::post('/dasar-hukum', [DasarHukumController::class, 'store']);
    Route::post('/dasar-hukum/{id}', [DasarHukumController::class, 'update']);
    Route::delete('/dasar-hukum/{id}', [DasarHukumController::class, 'destroy']);
});
