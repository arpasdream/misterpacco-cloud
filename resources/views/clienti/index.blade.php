@extends('layouts.app-master')

@section('content')

    <div class="row">
        @if(session('success'))
            <div class="col-12">
                <div class="alert alert-success alert-auto">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Clienti</h5>
                    <a href="{{ route('inserisci-cliente') }}" class="btn btn-primary">
                        <i class="fa fa-fw fa-plus mr-1"></i> Nuovo cliente
                    </a>
                </div>

                <div class="card-body table-border-style">
                    {{-- FILTRI --}}
                    <form method="get" class="filter-bar mb-3">
                        <div class="row g-2 align-items-end">
                            <div class="col-12 col-md-6">
                                <label class="form-label">Cerca per ragione sociale</label>
                                <input type="text" name="s" value="{{ $s }}" class="form-control form-control-sm"
                                       placeholder="Inserisci ragione sociale...">
                            </div>
                            <div class="col-12 col-md-6 text-md-end">
                                <label class="form-label d-none d-md-block">&nbsp;</label>
                                <div class="d-flex gap-2 justify-content-md-end">
                                    @if($s)
                                        <a href="{{ route('lista-clienti') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                                    @endif
                                    <button class="btn btn-primary btn-sm" type="submit">Filtra</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-vcenter">
                            <thead>
                            <tr>
                                <th style="width:80px;" class="text-center">ID</th>
                                <th>Cliente</th>
                                <th style="width:220px;">Documenti</th>
                                <th style="width:180px;">Contatti</th>
                                <th style="width:220px;">Indirizzo</th>
                                <th style="width:160px;">Creato il</th>
                                <th style="width:140px;" class="text-center">Azioni</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($clienti as $c)
                                @php
                                    $nome   = $c->ragione_sociale ?: '';
                                    $piva   = $c->piva ?: null;
                                    $cf     = $c->codice_fiscale ?: null;
                                    $tel    = $c->telefono ?: null;
                                    $addr   = trim(implode(' ', array_filter([$c->indirizzo])));
                                    $loc    = trim(implode(' ', array_filter([$c->cap, $c->citta, $c->provincia ? "({$c->provincia})" : null])));
                                @endphp
                                <tr>
                                    <td class="text-center align-middle">{{ $c->id }}</td>

                                    {{-- Cliente (ragione sociale + email) --}}
                                    <td class="align-middle">
                                        @if($nome)<div class="fw-600">{{ $nome }}</div>@endif
                                        <div class="text-muted small">{{ $c->email }}</div>
                                    </td>

                                    {{-- Documenti (P.IVA / CF) --}}
                                    <td class="align-middle">
                                        @if($piva)
                                            <span>{{ $piva }}</span>
                                        @endif
                                        @if($cf)
                                            <span>{{ $cf }}</span>
                                        @endif
                                    </td>

                                    {{-- Contatti (telefono) --}}
                                    <td class="align-middle">
                                        @if($tel)
                                            <a href="tel:{{ preg_replace('/\s+/', '', $tel) }}">{{ $tel }}</a>
                                        @endif
                                    </td>

                                    {{-- Indirizzo (via + località) --}}
                                    <td class="align-middle">
                                        @if($addr)
                                            <div>{{ $addr }}</div>
                                        @endif
                                        @if($loc)
                                            <div class="text-muted small">{{ $loc }}</div>
                                        @endif
                                    </td>

                                    <td class="align-middle">
                                        {{ optional($c->created_at)->format('d/m/Y H:i') }}
                                    </td>

                                    <td class="text-center align-middle">
                                        <div class="btn-group">
                                            <a href="{{ route('dettagli-cliente', $c->id) }}"
                                               class="btn btn-sm btn-alt-secondary" data-toggle="tooltip" title="Dettagli">
                                                <i class="fa fa-fw fa-eye"></i>
                                            </a>

                                            <a href="{{ route('modifica-cliente', $c->id) }}"
                                               class="btn btn-sm btn-alt-primary" data-toggle="tooltip" title="Modifica">
                                                <i class="fa fa-fw fa-pencil-alt"></i>
                                            </a>
                                            <form action="{{ route('cliente.destroy', $c->id) }}" method="post"
                                                  onsubmit="return confirm('Eliminare questo cliente?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-alt-danger" data-toggle="tooltip" title="Elimina">
                                                    <i class="fa fa-fw fa-times"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Nessun cliente trovato.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div>{{ $clienti->links() }}</div>
                </div>
            </div>
        </div>
    </div>

@endsection
