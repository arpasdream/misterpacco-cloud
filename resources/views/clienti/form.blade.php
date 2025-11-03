@extends('layouts.app-master')

@section('content')

    @php
        $isEdit = (bool) $c;
        $title  = $isEdit ? "Modifica cliente #{$c->id}" : 'Nuovo cliente';
        $action = $isEdit ? route('cliente.update', $c->id) : route('cliente.store');
    @endphp

    <!-- [ Main Content ] start -->
    <div class="row custom-color">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $title }}</h5>
                    <div class="fa-pull-right">
                        <a href="{{ route('lista-clienti') }}" class="btn btn-primary mb-0">
                            <i class="fa fa-fw fa-list mr-1"></i> Lista clienti
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-auto">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-auto">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <strong>Attenzione!</strong> Controlla i campi sotto.
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card-body">

                    <form class="form-validation" action="{{ $action }}" method="POST">
                        @csrf
                        @if($isEdit)
                            @method('PUT') {{-- REST canonico --}}
                        @endif

                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label class="form-label" for="ragione_sociale">Ragione sociale <span class="text-danger">*</span></label>
                                <input type="text" class="form-control required"
                                       id="ragione_sociale" name="ragione_sociale"
                                       value="{{ old('ragione_sociale', $c->ragione_sociale ?? '') }}"
                                       maxlength="255" placeholder="Inserisci ragione sociale..">
                            </div>

                            <div class="mb-3 col-md-3">
                                <label class="form-label" for="piva">P. IVA <span class="text-danger">*</span></label>
                                <input type="text" class="form-control required" id="piva" name="piva"
                                       value="{{ old('piva', $c->piva ?? '') }}" maxlength="16" placeholder="Inserisci P.IVA..">
                            </div>

                            <div class="mb-3 col-md-3">
                                <label class="form-label" for="codice_fiscale">Codice Fiscale <span class="text-danger">*</span></label>
                                <input type="text" class="form-control required" id="codice_fiscale" name="codice_fiscale"
                                       value="{{ old('codice_fiscale', $c->codice_fiscale ?? '') }}" maxlength="16" placeholder="Inserisci CF..">
                            </div>
                        </div>

                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label class="form-label" for="email">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control required email" id="email" name="email"
                                       value="{{ old('email', $c->email ?? '') }}" maxlength="191" required placeholder="Inserisci email..">
                            </div>

                            <div class="mb-3 col-md-6">
                                <label class="form-label" for="password">
                                    Password {!! $isEdit ? '<small>(lascia vuoto per non cambiare)</small>' : '<span class="text-danger">*</span>' !!}
                                </label>
                                <input type="password" class="form-control {{ $isEdit ? '' : 'required' }}"
                                       id="password" name="password"
                                       placeholder="{{ $isEdit ? 'Nuova password (opzionale)..' : 'Inserisci password..' }}"
                                       @unless($isEdit) required @endunless maxlength="255">
                            </div>
                        </div>

                        <div class="row">
                            <div class="mb-3 col-md-4">
                                <label class="form-label" for="telefono">Telefono <span class="text-danger">*</span></label>
                                <input type="text" class="form-control required" id="telefono" name="telefono"
                                       value="{{ old('telefono', $c->telefono ?? '') }}" maxlength="50" placeholder="Inserisci telefono..">
                            </div>
                            <div class="mb-3 col-md-8">
                                <label class="form-label" for="indirizzo">Indirizzo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control required" id="indirizzo" name="indirizzo"
                                       value="{{ old('indirizzo', $c->indirizzo ?? '') }}" maxlength="255" placeholder="Via/Piazza, numero civico..">
                            </div>
                        </div>

                        <div class="row">
                            <div class="mb-3 col-md-2">
                                <label class="form-label" for="cap">CAP <span class="text-danger">*</span></label>
                                <input type="text" class="form-control required" id="cap" name="cap"
                                       value="{{ old('cap', $c->cap ?? '') }}" maxlength="10">
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label" for="citta">Città <span class="text-danger">*</span></label>
                                <input type="text" class="form-control required" id="citta" name="citta"
                                       value="{{ old('citta', $c->citta ?? '') }}" maxlength="100">
                            </div>
                            <div class="mb-3 col-md-2">
                                <label class="form-label" for="provincia">Prov. <span class="text-danger">*</span></label>
                                <input type="text" class="form-control required" id="provincia" name="provincia"
                                       value="{{ old('provincia', $c->provincia ?? '') }}" maxlength="2">
                            </div>
                        </div>

                        <div class="row">
                            <div class="mb-3 col-md-12">
                                <label class="form-label" for="note">Note</label>
                                <textarea class="form-control" id="note" name="note" rows="4"
                                          placeholder="Note aggiuntive..">{{ old('note', $c->note ?? '') }}</textarea>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary mt-3">Salva</button>
                        <a href="{{ route('lista-clienti') }}" class="btn btn-light mt-3">Chiudi</a>

                    </form>

                </div>
            </div>

        </div>
    </div>

@endsection
