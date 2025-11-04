@extends('layouts.app-master')

@section('content')

    @php
        $isEdit = isset($tariffa) && $tariffa;
        $title  = $isEdit ? 'Modifica tariffa' : 'Nuova tariffa';
        $action = $isEdit
            ? route('tariffa.update', [$cliente->id, $tariffa->id])
            : route('tariffa.store',  $cliente->id);
    @endphp

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                {{-- HEADER --}}
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        {{ $title }} —
                        <span class="text-muted">
                        {{ $cliente->ragione_sociale ?? $cliente->name ?? $cliente->email }}
                    </span>
                    </h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('lista-tariffe', $cliente->id) }}" class="btn btn-outline-secondary">
                            <i class="fa fa-fw fa-arrow-left mr-1"></i> Torna alle tariffe
                        </a>
                    </div>
                </div>

                {{-- FORM --}}
                <div class="card-body">
                    <form action="{{ $action }}" method="post" class="row g-3">
                        @csrf
                        @if($isEdit)
                            @method('PUT')
                        @endif

                        <div class="col-md-3">
                            <label class="form-label">Peso minimo (kg)</label>
                            <input type="number" step="0.01" name="min_peso"
                                   value="{{ old('min_peso', $isEdit ? $tariffa->min_peso : '') }}"
                                   class="form-control @error('min_peso') is-invalid @enderror" required>
                            @error('min_peso')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Peso massimo (kg)</label>
                            <input type="number" step="0.01" name="max_peso"
                                   value="{{ old('max_peso', $isEdit ? $tariffa->max_peso : '') }}"
                                   class="form-control @error('max_peso') is-invalid @enderror" required>
                            @error('max_peso')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Prezzo (€)</label>
                            <input type="number" step="0.01" name="prezzo"
                                   value="{{ old('prezzo', $isEdit ? $tariffa->prezzo : '') }}"
                                   class="form-control @error('prezzo') is-invalid @enderror" required>
                            @error('prezzo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <button class="btn btn-primary">
                                <i class="fa fa-fw fa-save mr-1"></i> {{ $isEdit ? 'Aggiorna' : 'Salva' }}
                            </button>
                            <a href="{{ route('lista-tariffe', $cliente->id) }}" class="btn btn-secondary">Annulla</a>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

@endsection
