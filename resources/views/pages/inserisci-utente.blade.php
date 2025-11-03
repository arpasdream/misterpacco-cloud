@extends('layouts.app-master')

@section('content')

    <!-- [ Main Content ] start -->
    <div class="row custom-color">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header">
                    <div class="fa-pull-right">
                        <a href="{{ route('lista-utenti') }}" class="btn btn-primary mb-3">
                            <i class="fa fa-fw fa-clock mr-1"></i> Lista utenti
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-auto">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        {{session('success')}}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-auto">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        {{session('error')}}
                    </div>
                @endif

                <div class="card-body">

                    <form class="form-validation" action="{{ route('utente.store') }}" method="POST">
                        @csrf

                        <div class="row">

                            <div class="mb-3 col-md-6">
                                <label class="form-label" for="tipo_utente">Livello <span class="text-danger">*</span></label>
                                <select name="tipo_utente" id="tipo_utente" class="form-control required">

                                    <option value="">Seleziona livello</option>

                                    <option value="admin">Amministratore</option>
                                    <option value="operatore">Operatore</option>
                                </select>
                            </div>

                        </div>

                        <div class="row">

                            <div class="mb-3 col-md-6">
                                <label class="form-label" for="nome_cognome">Nome e Cognome <span class="text-danger">*</span></label>
                                <input type="text" class="form-control required" id="nome_cognome" name="nome_cognome" placeholder="Inserisci nome e cognome.." value="" maxlength="50" />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label" for="email">Email </label>
                                <input type="email" class="form-control required email" id="email" name="email" placeholder="Inserisci email.." value="" maxlength="50" />
                            </div>

                        </div>

                        <div class="row">

                            <div class="mb-3 col-md-6">
                                <label class="form-label" for="codice">Codice accesso <span class="text-danger">*</span> <small>(max 10 caratteri alfanumerici)</small></label>
                                <input name="codice" type="text" class="form-control required" id="codice" value="" placeholder="Inserisci codice.." maxlength="10" />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label" for="password" class="fa-pull-left">Password <span class="text-danger">*</span> <small>(max 10 caratteri alfanumerici)</small></label>
                                <label class="fa-pull-right"><span toggle="#password-field" class="fa fa-fw fa-eye field_icon toggle-password"></span></label>
                                <input type="password" class="form-control required" id="password" name="password" placeholder="Inserisci password.." value="" maxlength="10" />
                            </div>

                        </div>

                        <button type="submit" class="btn btn-primary mt-3">Salva</button> <a href="{{ route('lista-utenti') }}" class="btn btn-light mt-3">Chiudi</a>

                    </form>

                </div>
            </div>

        </div>
        <!-- [ form-element ] end -->
    </div>

@endsection
