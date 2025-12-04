<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BaseDatosController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [BaseDatosController::class, 'index'])->name('dashboard');
    
    Route::get('/bases-datos/crear', [BaseDatosController::class, 'create'])->name('basesdatos.create');
    Route::post('/bases-datos', [BaseDatosController::class, 'store'])->name('basesdatos.store');
    Route::get('/bases-datos/{baseDatos}', [BaseDatosController::class, 'show'])->name('basesdatos.show');
    Route::post('/bases-datos/{baseDatos}/registros', [BaseDatosController::class, 'storeRegistro'])->name('registros.store');
    Route::delete('/bases-datos/{baseDatos}', [BaseDatosController::class, 'destroy'])->name('basesdatos.destroy');
    Route::put('/bases-datos/{baseDatos}/registros/{registro}', [BaseDatosController::class, 'updateRegistro'])->name('registros.update');
    Route::post('/bases-datos/{baseDatos}/columna-config', [BaseDatosController::class, 'updateColumnConfig'])->name('basesdatos.column.config');
});

require __DIR__.'/auth.php';
