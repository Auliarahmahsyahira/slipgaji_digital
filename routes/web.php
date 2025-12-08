<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\KomponenController;
use App\Http\Controllers\GajiController;

// Login
Route::get('auth/login', [AuthController::class, 'showLoginNip'])->name('login.nip');
Route::post('auth/check-nip', [AuthController::class, 'checkNip'])->name('login.checkNip');

Route::get('auth/login-password/{nip}', [AuthController::class, 'showLoginPassword'])->name('login.password');
Route::post('auth/login-password', [AuthController::class, 'login'])->name('login.post');

Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Admin
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard/admin', [AuthController::class, 'admin'])
        ->name('admin.dashboard');
});

// Pegawai
Route::middleware(['auth', 'pegawai'])->group(function () {
    Route::get('/dashboard/pegawai', [AuthController::class, 'pegawai'])
        ->name('pegawai.dashboard');
});

Route::get('/pegawai/slip/{id}/pdf', [AuthController::class, 'cetakPdf'])->name('pegawai.slip.pdf');

// Membuat Data Pegawai
Route::get('/pegawai', [PegawaiController::class, 'index'])->name('pegawai.index');
Route::get('/pegawai/create', [PegawaiController::class, 'create'])->name('pegawai.create');
Route::post('/pegawai/store', [PegawaiController::class, 'store'])->name('pegawai.store');
Route::delete('/pegawai/{id}', [PegawaiController::class, 'destroy'])->name('pegawai.destroy');
Route::get('/pegawai/edit/{id}', [PegawaiController::class, 'edit'])->name('pegawai.edit');
Route::put('/pegawai/update/{id}', [PegawaiController::class, 'update'])->name('pegawai.update');
// Pegawai Import
Route::post('/pegawai/import', [PegawaiController::class, 'import'])->name('pegawai.import');


// Membuat Komponen Gaji
Route::get('/komponen/index', [KomponenController::class, 'index'])->name('komponen.index');
Route::get('/komponen/create', [KomponenController::class, 'create'])->name('komponen.create');
Route::post('/komponen/store', [KomponenController::class, 'store'])->name('komponen.store');
Route::get('/komponen/edit/{id}', [KomponenController::class, 'edit'])->name('komponen.edit');
Route::put('/komponen/update/{id}', [KomponenController::class, 'update'])->name('komponen.update');
Route::delete('/komponen/{id}', [KomponenController::class, 'destroy'])->name('komponen.destroy');

// Slip Gaji
Route::get('/slipgaji', [GajiController::class, 'index'])->name('slipgaji.index');
Route::get('/slipgaji/create', [GajiController::class, 'create'])->name('slipgaji.create');
Route::get('/cek-nip/{nip}', [GajiController::class, 'cekNip'])->name('cek.nip');
Route::post('/slipgaji/store', [GajiController::class, 'store'])->name('slipgaji.store');
Route::post('/slipgaji/edit-selected', [GajiController::class, 'editSelected'])->name('slipgaji.editSelected');
Route::get('/slipgaji/edit/{id}', [GajiController::class, 'edit'])->name('slipgaji.edit');
Route::get('/slipgaji/edit2/{id}', [GajiController::class, 'edit2'])->name('slipgaji.edit2');
Route::put('/slipgaji/update/{id}', [GajiController::class, 'update'])->name('slipgaji.update');
Route::put('/slipgaji/update2/{id}', [GajiController::class, 'update2'])->name('slipgaji.update2');
Route::delete('/slipgaji/deleteSelected', [GajiController::class, 'deleteSelected'])->name('slipgaji.deleteSelected');

// Slip Gaji Import
Route::get('/gaji/import', [GajiController::class, 'showImportForm'])->name('gaji.import.form');
Route::post('/gaji/import/tetap', [GajiController::class, 'importTetap'])->name('gaji.import.tetap');
Route::post('/gaji/import/bulanan', [GajiController::class, 'importBulanan'])->name('gaji.import.bulanan');