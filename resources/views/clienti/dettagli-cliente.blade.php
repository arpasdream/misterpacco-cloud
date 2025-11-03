@extends('layouts.app-master')

@section('content')
    <div class="row custom-color">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">

                <div class="card-header d-flex justify-content-between align-items-center bg-light">
                    <h4 class="mb-0 text-primary">
                        <i class="fa fa-user-circle mr-2"></i> Dettagli cliente
                    </h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('lista-clienti') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fa fa-list mr-1"></i> Lista clienti
                        </a>
                        <a href="{{ route('modifica-cliente', $c->id) }}" class="btn btn-primary btn-sm">
                            <i class="fa fa-edit mr-1"></i> Modifica
                        </a>
                    </div>
                </div>

                <div class="card-body p-4">

                    {{-- SEZIONE INTESTAZIONE RAPIDA --}}
                    <div class="bg-light rounded p-3 mb-4 border">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                            <div>
                                <h5 class="mb-1 fw-bold text-dark">
                                    {{ $c->ragione_sociale ?: '—' }}
                                </h5>
                                <div class="text-muted small">
                                    ID cliente: <strong>#{{ $c->id }}</strong> |
                                    Creato il: {{ optional($c->created_at)->format('d/m/Y H:i') }}
                                </div>
                            </div>
                            <div class="mt-3 mt-md-0 text-md-end">
                            <span class="badge bg-primary-subtle text-primary px-3 py-2">
                                {{ strtoupper($c->provincia ?: 'NA') }}
                            </span>
                            </div>
                        </div>
                    </div>

                    {{-- DETTAGLI PRINCIPALI --}}
                    <div class="row">

                        {{-- COLONNA SINISTRA --}}
                        <div class="col-lg-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-primary text-white py-2">
                                    <i class="fa fa-id-card mr-2"></i> Anagrafica
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr>
                                            <th class="w-30 text-muted">Email</th>
                                            <td><a href="mailto:{{ $c->email }}">{{ $c->email }}</a></td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Telefono</th>
                                            <td>{{ $c->telefono ?: '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Creato il</th>
                                            <td>{{ optional($c->created_at)->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Aggiornato il</th>
                                            <td>{{ optional($c->updated_at)->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- COLONNA DESTRA --}}
                        <div class="col-lg-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-primary text-white py-2">
                                    <i class="fa fa-file-alt mr-2"></i> Documenti & Indirizzo
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr>
                                            <th class="w-30 text-muted">P. IVA</th>
                                            <td>{{ $c->piva ?: '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Codice Fiscale</th>
                                            <td>{{ $c->codice_fiscale ?: '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Indirizzo</th>
                                            <td>{{ $c->indirizzo ?: '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Città</th>
                                            <td>{{ $c->citta ?: '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">CAP</th>
                                            <td>{{ $c->cap ?: '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Provincia</th>
                                            <td>{{ strtoupper($c->provincia ?: '—') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- NOTE --}}
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-primary text-white py-2">
                                    <i class="fa fa-sticky-note mr-2"></i> Note
                                </div>
                                <div class="card-body">
                                    @if($c->note)
                                        <div class="p-3 bg-light border rounded">{!! nl2br(e($c->note)) !!}</div>
                                    @else
                                        <div class="text-muted fst-italic">Nessuna nota disponibile.</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>

                </div> {{-- fine card-body --}}
            </div> {{-- fine card --}}
        </div>
    </div>
@endsection
