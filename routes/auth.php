<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Login
Route::get('auth/login', [AuthController::class, 'showLoginNip'])->name('login.nip');
Route::post('auth/check-nip', [AuthController::class, 'checkNip'])->name('login.checkNip');

Route::get('auth/login-password/{nip}', [AuthController::class, 'showLoginPassword'])->name('login.password');
Route::post('auth/login-password', [AuthController::class, 'login'])->name('login.post');

Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard Admin
Route::middleware(['auth', 'admin'])->group(function () {
  Route::get('/dashboard/admin', [AuthController::class, 'admin'])
    ->name('admin.dashboard');
});

// Dashboard Pegawai
Route::middleware(['auth', 'pegawai'])->group(function () {
  Route::get('/dashboard/pegawai', [AuthController::class, 'pegawai'])
    ->name('pegawai.dashboard');
});
