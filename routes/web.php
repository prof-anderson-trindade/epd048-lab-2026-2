<?php

use App\Http\Controllers\OlaController;
use App\Http\Controllers\RecadoController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::get('/ola/{nome?}', OlaController::class);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::get('mural', [RecadoController::class, 'index'])->name('mural');
    Route::post('mural', [RecadoController::class, 'store'])->name('mural.store');
    Route::delete('mural/{recado}', [RecadoController::class, 'destroy'])->name('mural.destroy');
});

require __DIR__.'/settings.php';
