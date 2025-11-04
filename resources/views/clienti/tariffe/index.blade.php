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
                {{-- HEADER --}}
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        Tariffe —
                        <span class="text-muted">
                            {{ $cliente->ragione_sociale ?? $cliente->name ?? $cliente->email }}
                        </span>
                    </h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('lista-clienti') }}" class="btn btn-outline-secondary">
                            <i class="fa fa-fw fa-arrow-left mr-1"></i> Clienti
                        </a>
                        <a href="{{ route('inserisci-tariffa', $cliente->id) }}" class="btn btn-primary">
                            <i class="fa fa-fw fa-plus mr-1"></i> Nuova tariffa
                        </a>
                    </div>
                </div>

                {{-- TABELLA --}}
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-vcenter">
                            <thead>
                            <tr>
                                <th>Range peso (kg)</th>
                                <th>Prezzo (€)</th>
                                <th style="width:140px;" class="text-center">Azioni</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($tariffe as $t)
                                <tr>
                                    <td class="align-middle">
                                        <div>
                                            <span class="badge bg-info">{{ number_format($t->min_peso, 2, ',', '.') }}</span>
                                            <span class="mx-1">→</span>
                                            <span class="badge bg-info">{{ number_format($t->max_peso, 2, ',', '.') }}</span>
                                        </div>
                                    </td>

                                    <td class="align-middle">
                                        <strong>{{ number_format($t->prezzo, 2, ',', '.') }}</strong>
                                    </td>

                                    <td class="text-center align-middle">
                                        <div class="btn-group">
                                            <a href="{{ route('modifica-tariffa', [$cliente->id, $t->id]) }}"
                                               class="btn btn-sm btn-alt-primary" data-toggle="tooltip" title="Modifica">
                                                <i class="fa fa-fw fa-pencil-alt"></i>
                                            </a>

                                            <form action="{{ route('tariffa.destroy', [$cliente->id, $t->id]) }}"
                                                  method="post"
                                                  onsubmit="return confirm('Eliminare questa tariffa?')">
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
                                    <td colspan="5" class="text-center text-muted py-4">Nessuna tariffa inserita.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(method_exists($tariffe, 'links'))
                        <div>{{ $tariffe->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
