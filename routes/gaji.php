<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GajiController;
use App\Http\Controllers\ImportController;

Route::middleware(['auth'])->group(function () {

  Route::get('/slipgaji', [GajiController::class, 'index'])->name('slipgaji.index');

  Route::get('/slipgaji/create/duatahun', [GajiController::class, 'createDuaTahun'])
    ->name('slipgaji.create.duatahun');

  Route::get('/slipgaji/create/bulanan', [GajiController::class, 'createBulanan'])
    ->name('slipgaji.create.bulanan');

  Route::post('/slipgaji/store/bulanan', [GajiController::class, 'store_bulanan'])
    ->name('slipgaji.store.bulanan');

  Route::post('/slipgaji/store-duatahun', [GajiController::class, 'store_duatahun'])
    ->name('slipgaji.store.duatahun');

  Route::get('/slipgaji/edit/{id}', [GajiController::class, 'edit'])->name('slipgaji.edit');
  Route::get('/slipgaji/edit2/{id}', [GajiController::class, 'edit_bulanan'])->name('slipgaji.edit_bulanan');

  Route::put('/slipgaji/update/{id}', [GajiController::class, 'update'])->name('slipgaji.update');
  Route::put('/slipgaji/update2/{id}', [GajiController::class, 'update2'])->name('slipgaji.update2');

  Route::delete('/slipgaji/destroy/{id}', [GajiController::class, 'destroy'])->name('slipgaji.destroy');

  // Cek
  Route::get('/cek-gaji/{nip}', [GajiController::class, 'cekGajiTerbaru']);
  Route::get('/cek-nip/{nip}', [GajiController::class, 'cekNip'])->name('cek.nip');
});
