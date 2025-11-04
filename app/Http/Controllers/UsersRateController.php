<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UsersRate;

class UsersRateController extends Controller
{
    public function index($customer_id)
    {
        $cliente = User::findOrFail($customer_id);
        $tariffe = UsersRate::where('user_id', $cliente->id)
            ->orderBy('min_peso')
            ->get();

        return view('clienti.tariffe.index', compact('cliente','tariffe'));
    }

    // CREATE -> mostra la stessa form senza $tariffa
    public function create($customer_id)
    {
        $cliente = User::findOrFail($customer_id);
        return view('clienti.tariffe.form', [
            'cliente'  => $cliente,
            'tariffa'  => null, // assente => create
        ]);
    }

    // STORE
    public function store(Request $request, $customer_id)
    {
        $cliente = User::findOrFail($customer_id);

        $request->validate([
            'min_peso' => ['required','numeric','min:0'],
            'max_peso' => ['required','numeric','gte:min_peso'],
            'prezzo'   => ['required','numeric','min:0'],
        ]);

        UsersRate::create([
            'user_id'  => $cliente->id,
            'min_peso' => $request->min_peso,
            'max_peso' => $request->max_peso,
            'prezzo'   => $request->prezzo,
        ]);

        return redirect()->route('lista-tariffe', $cliente->id)
            ->with('success','Tariffa creata.');
    }

    // EDIT -> mostra la stessa form con $tariffa
    public function edit($customer_id, $rate_id)
    {
        $cliente = User::findOrFail($customer_id);
        $tariffa = UsersRate::findOrFail($rate_id);

        return view('clienti.tariffe.form', compact('cliente','tariffa'));
    }

    // UPDATE
    public function update(Request $request, $customer_id, $rate_id)
    {
        $tariffa = UsersRate::findOrFail($rate_id);

        $request->validate([
            'min_peso' => ['required','numeric','min:0'],
            'max_peso' => ['required','numeric','gte:min_peso'],
            'prezzo'   => ['required','numeric','min:0'],
        ]);

        $tariffa->update([
            'min_peso' => $request->min_peso,
            'max_peso' => $request->max_peso,
            'prezzo'   => $request->prezzo,
        ]);

        return redirect()->route('lista-tariffe', $customer_id)
            ->with('success','Tariffa aggiornata.');
    }

    // DELETE
    public function destroy($customer_id, $rate_id)
    {
        $tariffa = UsersRate::findOrFail($rate_id);
        $tariffa->delete();

        return redirect()->route('lista-tariffe', $customer_id)
            ->with('success','Tariffa eliminata.');
    }
}
