<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KomponenController;

Route::middleware(['auth'])->group(function () {

  Route::get('/komponen/index', [KomponenController::class, 'index'])->name('komponen.index');
  Route::get('/komponen/create', [KomponenController::class, 'create'])->name('komponen.create');
  Route::post('/komponen/store', [KomponenController::class, 'store'])->name('komponen.store');
  Route::get('/komponen/edit/{id}', [KomponenController::class, 'edit'])->name('komponen.edit');
  Route::put('/komponen/update/{id}', [KomponenController::class, 'update'])->name('komponen.update');
  Route::delete('/komponen/{id}', [KomponenController::class, 'destroy'])->name('komponen.destroy');

});
