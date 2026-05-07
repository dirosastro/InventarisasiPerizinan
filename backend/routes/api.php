<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PerizinanController;
use App\Http\Controllers\Api\MasterDataController;

Route::get('/perizinan', [PerizinanController::class, 'index']);
Route::get('/perizinan/{id}', [PerizinanController::class, 'show']);
Route::post('/perizinan', [PerizinanController::class, 'store']);
Route::post('/perizinan/{id}', [PerizinanController::class, 'update']);
Route::delete('/perizinan/{id}', [PerizinanController::class, 'destroy']);

Route::get('/satker', [MasterDataController::class, 'getSatker']);
Route::get('/ppk', [MasterDataController::class, 'getPpk']);
Route::get('/ruas-jalan', [MasterDataController::class, 'getRuasJalan']);
