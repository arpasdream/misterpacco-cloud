<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Utente;

class UtenteController extends Controller
{
    public function index()
    {
        $utenti = DB::table('utenti')
            ->orderBy('id', 'desc')
            ->get();

        return view('pages.lista-utenti', compact('utenti'));
    }

    public function create()
    {
        return view('pages.inserisci-utente');
    }

    public function store(Request $request)
    {
        //LETTURA PARAMETRI
        $codice = $request->codice;
        $nome = $request->nome_cognome;
        $email = $request->email;
        $password = $request->password;
        $password = bcrypt($password);
        $tipo_utente = $request->tipo_utente;

        // $mail_iscrizione = $request->mail_iscrizione;

        //richiama utenti per controllo esistenza
        $rs_utenti = DB::table('utenti')
            ->select('codice')
            ->where('codice', '=', $codice)
            ->get();

        if(count($rs_utenti)==0) {

            $data=array('codice'=>$codice, 'nome'=>$nome, 'email'=>$email, 'password'=>$password, 'tipo_utente'=>$tipo_utente);
            DB::table('utenti')
                ->insert($data);

            return redirect()->route('lista-utenti')
                ->with('success', 'Inserimento effettuato correttamente!');

        }else{

            $errormsg = 'Il cliente già esiste.';
            return redirect()->back()
                ->with('error', 'Inserimento non effettuato! ' . $errormsg);
        }
    }

    public function edit($id)
    {
        $utente = Utente::find($id);

        return view('pages.modifica-utente', compact('utente'));
    }

    public function update(Request $request, $id)
    {
        //LETTURA PARAMETRI
        $codice = $request->codice;
        $password = $request->password;
        $nome = $request->nome_cognome;
        $email = $request->email;
        $tipo_utente = $request->tipo_utente;

        //richiama utenti per controllo esistenza
        $rs_utenti = DB::table('utenti')
            ->select('codice')
            ->where('codice', '=', $codice)
            ->where('id', '!=', $id)
            ->get();

        if(count($rs_utenti)==0) {

            if($password!='') {

                $password = bcrypt($password);
                $data = array('codice' => $codice, 'nome' => $nome, 'email' => $email, 'password' => $password, 'tipo_utente' => $tipo_utente);

            }else{

                $data = array('codice' => $codice, 'nome' => $nome, 'email' => $email, 'tipo_utente' => $tipo_utente);

            }
            DB::table('utenti')
                ->where('id',$id)->update($data);

            return redirect()->route('lista-utenti')
                ->with('success', 'Modifica effettuata correttamente!');

        }else{

            $errormsg = 'Il cliente già esiste.';
            return redirect()->back()
                ->with('error', 'Modifica non effettuata! ' . $errormsg);
        }
    }

    public function destroy($id)
    {
        $utente = Utente::find($id);
        $utente->delete();
        return redirect()->route('lista-utenti')
            ->with('success', 'Utente eliminato correttamente!');
    }

}
