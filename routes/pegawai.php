<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\AuthController;

Route::middleware(['auth'])->group(function () {

  Route::get('/pegawai', [PegawaiController::class, 'index'])->name('pegawai.index');
  Route::get('/pegawai/create', [PegawaiController::class, 'create'])->name('pegawai.create');
  Route::post('/pegawai/store', [PegawaiController::class, 'store'])->name('pegawai.store');
  Route::get('/pegawai/edit/{id}', [PegawaiController::class, 'edit'])->name('pegawai.edit');
  Route::put('/pegawai/update/{id}', [PegawaiController::class, 'update'])->name('pegawai.update');
  Route::delete('/pegawai/{id}', [PegawaiController::class, 'destroy'])->name('pegawai.destroy');

  // Import & Datatable
  Route::post('/pegawai/import', [PegawaiController::class, 'import'])->name('pegawai.import');
  Route::get('/pegawai/data', [PegawaiController::class, 'data'])->name('pegawai.data');

  // Cetak Slip PDF Pegawai
  Route::get('/pegawai/slip/{id}/pdf', [AuthController::class, 'cetakPdf'])
    ->name('pegawai.slip.pdf');

});
