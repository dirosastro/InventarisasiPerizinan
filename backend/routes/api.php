<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PerizinanController;
use App\Http\Controllers\Api\MasterDataController;
use App\Http\Controllers\Api\UserController;

Route::get('/perizinan', [PerizinanController::class, 'index']);
Route::get('/perizinan/{id}', [PerizinanController::class, 'show']);
Route::post('/perizinan', [PerizinanController::class, 'store']);
Route::post('/perizinan/{id}', [PerizinanController::class, 'update']);
Route::delete('/perizinan/{id}', [PerizinanController::class, 'destroy']);

Route::get('/satker', [MasterDataController::class, 'getSatker']);
Route::get('/ppk', [MasterDataController::class, 'getPpk']);
Route::get('/ruas-jalan', [MasterDataController::class, 'getRuasJalan']);

// User Management Routes
Route::get('/users', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);
Route::post('/login', [UserController::class, 'login']);

// WhatsApp Test Route
Route::post('/wa-test', function (Request $request) {
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
