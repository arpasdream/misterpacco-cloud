<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\UsersRateController;

Route::middleware(['auth'])->group(function () {
    // LISTA
    Route::get('/clienti', [CustomerController::class, 'index'])->name('lista-clienti');

    // CREATE + STORE
    Route::get('/clienti/nuovo', [CustomerController::class, 'create'])->name('inserisci-cliente');
    Route::post('/clienti', [CustomerController::class, 'store'])->name('cliente.store');

    // EDIT + UPDATE (REST canonico)
    Route::get('/clienti/{customer}/edit', [CustomerController::class, 'edit'])->name('modifica-cliente');
    Route::match(['put','patch'], '/clienti/{customer}', [CustomerController::class, 'update'])->name('cliente.update');

    // DELETE
    Route::delete('/clienti/{customer}', [CustomerController::class, 'destroy'])->name('cliente.destroy');

    // DETTAGLI CLIENTE
    Route::get('/clienti/{customer}/dettagli', [CustomerController::class, 'show'])->name('dettagli-cliente');

    // ================== TARIFFE CLIENTI ==================

    Route::get('/clienti/{customer}/tariffe', [UsersRateController::class, 'index'])->name('lista-tariffe');
    Route::get('/clienti/{customer}/tariffe/nuova', [UsersRateController::class, 'create'])->name('inserisci-tariffa');
    Route::post('/clienti/{customer}/tariffe', [UsersRateController::class, 'store'])->name('tariffa.store');
    Route::get('/clienti/{customer}/tariffe/{rate}/edit', [UsersRateController::class, 'edit'])->name('modifica-tariffa');
    Route::put('/clienti/{customer}/tariffe/{rate}', [UsersRateController::class, 'update'])->name('tariffa.update');
    Route::delete('/clienti/{customer}/tariffe/{rate}', [UsersRateController::class, 'destroy'])->name('tariffa.destroy');
});
