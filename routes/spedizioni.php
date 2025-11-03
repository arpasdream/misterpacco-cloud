<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShipmentController;

Route::middleware(['auth'])->group(function () {

    // Lista
    Route::get('/spedizioni', [ShipmentController::class, 'index'])->name('lista-spedizioni');

    // Creazione
    Route::get('/spedizioni/nuova', [ShipmentController::class, 'create'])->name('inserisci-spedizione');
    Route::post('/spedizioni/nuova', [ShipmentController::class, 'store'])->name('spedizione.store');

    // Show + Edit + Update
    Route::get('/spedizioni/{spedizione}', [ShipmentController::class, 'show'])->name('mostra-spedizione');
    Route::get('/spedizioni/{spedizione}/edit', [ShipmentController::class, 'edit'])->name('modifica-spedizione');
    Route::match(['put','patch','post'], '/spedizioni/{spedizione}', [ShipmentController::class, 'update'])->name('spedizione.update');

    // Delete
    Route::delete('/spedizioni/{spedizione}', [ShipmentController::class, 'destroy'])->name('spedizione.destroy');

    // Download etichetta
    Route::get('/spedizioni/{spedizione}/etichetta', [ShipmentController::class, 'downloadLabel'])->name('spedizione.etichetta');
});
