<?php

use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'App\Http\Controllers'], function()
{

    Route::group(['middleware' => ['auth']], function() {

        /**
         * Utenti Routes
         */
        Route::get('/lista-utenti', 'UtenteController@index')->name('lista-utenti');
        Route::get('/inserisci-utente', 'UtenteController@create')->name('inserisci-utente');
        Route::post('/inserisci-utente', 'UtenteController@store')->name('utente.store');
        Route::get('/modifica-utente/{utente}', 'UtenteController@edit')->name('modifica-utente');
        Route::post('/modifica-utente/{id}', 'UtenteController@update')->name('utente.update');
        Route::delete('/elimina-utente/{id}', 'UtenteController@destroy')->name('utente.destroy');
    });

});
