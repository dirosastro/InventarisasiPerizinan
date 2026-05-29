<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () { return view('index'); })->name('home');
Route::get('/admin', function () { return view('admin'); })->name('admin');
Route::get('/dashboard', function () { return view('dashboard'); })->name('dashboard');
Route::get('/login', function () { return view('login'); })->name('login');
Route::get('/manajemen-user', function () { return view('manajemen_user'); })->name('users');
Route::get('/perizinan-data', function () { return view('perizinan'); })->name('perizinan_view');
Route::get('/peta', function () { return view('peta'); })->name('peta');
Route::get('/test-wa', function () { return view('test_wa'); })->name('test-wa');

