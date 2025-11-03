@extends('layouts.app-master')

@section('content')

    @php
        setlocale(LC_TIME, 'ita');
        setlocale(LC_ALL, 'it_IT.UTF-8');
    @endphp

    <!-- [ Main Content ] start -->
    <div class="row">

        @if(session('success'))
            <div class="alert alert-success alert-auto">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                {{ session('success') }}
            </div>
    @endif

    <!-- [ table ] start -->
        <div class="col-md-12">
            <div class="card">

                <div class="card-header">
                    <div class="fa-pull-right">
                        <a href="{{ route('inserisci-spedizione') }}" class="btn btn-primary mb-3">
                            <i class="fa fa-fw fa-plus mr-1"></i> Crea nuovo
                        </a>
                    </div>
                </div>

                <div class="card-body table-border-style">
                    {{-- FILTRI --}}
                    <form method="get" class="filter-bar mb-3" id="ship-filters">
                        <div class="row g-2 align-items-end">

                            <div class="col-12 col-md-2">
                                <label class="form-label">Stato</label>
                                @php
                                    $statusOpt = ['all' => 'Tutti'] + \App\Models\Shipment::STATUS_LABELS;
                                @endphp
                                <select name="status" class="form-select form-select-sm">
                                    @foreach($statusOpt as $k => $label)
                                        <option value="{{ $k }}" @selected(($status ?? 'all') === $k)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-6 col-md-2">
                                <label class="form-label">Dal</label>
                                <input type="date" name="from" value="{{ $from }}" class="form-control form-control-sm">
                            </div>

                            <div class="col-6 col-md-2">
                                <label class="form-label">Al</label>
                                <input type="date" name="to" value="{{ $to }}" class="form-control form-control-sm">
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label">Cerca</label>
                                <input type="text" name="s" value="{{ $s }}" class="form-control form-control-sm"
                                       placeholder="Shipment ID, mittente, destinatario, città">
                            </div>

                            <div class="col-12 col-md-2 text-md-end">
                                <label class="form-label d-none d-md-block">&nbsp;</label>
                                <div class="d-flex gap-2 justify-content-md-end">
                                    @if($status || $from || $to || $s)
                                        <a href="{{ route('lista-spedizioni') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
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
                                <th class="text-center" style="width: 90px;">ID</th>
                                <th>Mittente</th>
                                <th>Destinatario</th>
                                <th class="d-none d-sm-table-cell">Corriere</th>
                                <th class="d-none d-sm-table-cell">Data ritiro</th>
                                <th class="d-none d-sm-table-cell">Stato</th>
                                <th class="text-center" style="width: 150px;">Azioni</th>
                            </tr>
                            </thead>
                            <tbody>

                            @forelse ($spedizioni as $s)
                                <tr>
                                    <td class="text-center font-size-sm">
                                        {{ $s->shipment_id ?: $s->id }}
                                    </td>

                                    <td class="font-size-sm">
                                        <div class="font-w600">{{ $s->sender_name }}</div>
                                        <div class="text-muted">{{ $s->sender_city }} ({{ $s->sender_province }})</div>
                                    </td>

                                    <td class="font-size-sm">
                                        <div class="font-w600">{{ $s->recipient_name }}</div>
                                        <div class="text-muted">{{ $s->recipient_city }} ({{ $s->recipient_province }})</div>
                                    </td>

                                    <td class="d-none d-sm-table-cell font-size-sm">
                                        {{ $s->courier ?: '—' }}
                                    </td>

                                    <td class="d-none d-sm-table-cell font-size-sm">
                                        {{ optional($s->pickupDate)->format('d/m/Y') }}
                                    </td>

                                    <td class="d-none d-sm-table-cell font-size-sm">
                                        <span class="{{ $s->status_class }}">{{ $s->status_label }}</span>
                                    </td>

                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="{{ route('modifica-spedizione', $s->id) }}"
                                               class="btn btn-sm btn-alt-primary" data-toggle="tooltip" title="Modifica">
                                                <i class="fa fa-fw fa-pencil-alt"></i>
                                            </a>

                                            @if(!empty($s->label_data))
                                                <a href="{{ route('spedizione.etichetta', $s->id) }}"
                                                   class="btn btn-sm btn-alt-success" data-toggle="tooltip" title="Scarica etichetta">
                                                    <i class="fa fa-fw fa-download"></i>
                                                </a>
                                            @endif

                                            <form action="{{ route('spedizione.destroy', $s->id) }}"
                                                  onsubmit="return confirm('Eliminare questa spedizione?')"
                                                  method="post">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-alt-danger" data-toggle="tooltip" title="Elimina">
                                                    <i class="fa fa-fw fa-times"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        Nessuna spedizione trovata.
                                    </td>
                                </tr>
                            @endforelse

                            </tbody>
                        </table>
                    </div>

                    <div>
                        {{ $spedizioni->links() }}
                    </div>

                </div>
            </div>
        </div>
        <!-- [ table ] end -->

    </div>
    <!-- [ Main Content ] end -->

@endsection
