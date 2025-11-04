<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    /** Lista + filtro SOLO per ragione_sociale */
    public function index(Request $request)
    {
        $s = trim((string)$request->get('s', ''));

        $clienti = Customer::query()
            ->when($s, fn($q) => $q->where('ragione_sociale', 'like', "%{$s}%"))
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->withQueryString();

        $ragioni = Customer::whereNotNull('ragione_sociale')
            ->where('ragione_sociale','<>','')
            ->orderBy('ragione_sociale')
            ->limit(500) // limite di sicurezza
            ->pluck('ragione_sociale')
            ->toArray();

        return view('clienti.index', compact('clienti','s','ragioni'));
    }

    /** Unica view per create/edit — passa $c = null (create) */
    public function create()
    {
        return view('clienti.form', ['c' => null]);
    }

    /** Salva nuovo (tabella: users) */
    public function store(Request $r)
    {
        $data = $r->validate([
            'ragione_sociale' => ['nullable','string','max:255'],
            'piva'            => ['nullable','string','max:16'],
            'codice_fiscale'  => ['nullable','string','max:16'],
            'email'           => ['required','email','max:191','unique:users,email'],
            'password'        => ['required','string','min:6','max:255'],
            'telefono'        => ['nullable','string','max:50'],
            'indirizzo'       => ['nullable','string','max:255'],
            'cap'             => ['nullable','string','max:10'],
            'citta'           => ['nullable','string','max:100'],
            'provincia'       => ['nullable','string','max:2'],
            'note'            => ['nullable','string'],
        ]);

        $data['password'] = Hash::make($data['password']);
        // created_at/updated_at li gestisce Laravel se $timestamps=true nel Model

        Customer::create($data);

        return redirect()->route('lista-clienti')->with('success','Cliente creato.');
    }

    /** Unica view per create/edit — passa $c valorizzato (edit) */
    public function edit(Customer $customer)
    {
        return view('clienti.form', ['c' => $customer]);
    }

    /** Update (REST canonico) */
    public function update(Request $r, Customer $customer)
    {
        $data = $r->validate([
            'ragione_sociale' => ['nullable','string','max:255'],
            'piva'            => ['nullable','string','max:16'],
            'codice_fiscale'  => ['nullable','string','max:16'],
            'email'           => ['required','email','max:191', Rule::unique('users','email')->ignore($customer->id)],
            'password'        => ['nullable','string','min:6','max:255'],
            'telefono'        => ['nullable','string','max:50'],
            'indirizzo'       => ['nullable','string','max:255'],
            'cap'             => ['nullable','string','max:10'],
            'citta'           => ['nullable','string','max:100'],
            'provincia'       => ['nullable','string','max:2'],
            'note'            => ['nullable','string'],
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $customer->update($data);

        return redirect()->route('lista-clienti')->with('success','Cliente aggiornato.');
    }

    /** Delete */
    public function destroy(Customer $customer)
    {
        $customer->delete();
        return back()->with('success','Cliente eliminato.');
    }

    public function show(Customer $customer)
    {
        return view('clienti.dettagli-cliente', ['c' => $customer]);
    }
}
