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
                {{session('success')}}
            </div>
        @endif

		<!-- [ table ] start -->
		<div class="col-md-12">
			<div class="card">
				<div class="card-header">
					<div class="fa-pull-right">
						<a href="{{ route('inserisci-utente') }}" class="btn btn-primary mb-3">
							<i class="fa fa-fw fa-plus mr-1"></i> Crea nuovo
						</a>
					</div>
				</div>
				<div class="card-body table-border-style">

					<div class="table-responsive">

						<table class="table table-bordered table-striped table-vcenter">
							<thead>
							<tr>
								<th class="text-center" style="width: 100px;">Codice</th>
								<th>Nome</th>
								<th class="d-none d-sm-table-cell">Email</th>
								<th class="d-none d-sm-table-cell">Livello</th>
								<th class="text-center" style="width: 100px;">Azioni</th>
							</tr>
							</thead>
							<tbody>

							@foreach ($utenti as $utente)

							<tr>
								<td class="text-center font-size-sm">{{ $utente->codice }}</td>

								<td class="font-w600 font-size-sm">{{ $utente->nome }}</td>

								<td class="d-none d-sm-table-cell font-size-sm">{{ $utente->email }}</td>

								<td class="d-none d-sm-table-cell font-size-sm">
									@if($utente->tipo_utente=='admin')
										<span class="badge badge-primary">{{ $utente->tipo_utente }}</span>
									@else
										<span class="badge badge-secondary">{{ $utente->tipo_utente }}</span>
									@endif
								</td>

								<td class="text-center">
									<div class="btn-group">

										<a href="{{ route('modifica-utente', $utente->id) }}" class="btn btn-sm btn-alt-primary" data-toggle="tooltip" title="modifica" data-original-title="Edit">
											<i class="fa fa-fw fa-pencil-alt"></i>
										</a>

										<form action="{{ route('utente.destroy', $utente->id) }}" onsubmit="return confirm('Sei sicuro di eliminare questo utente?')" method="post">
											@csrf
											@method('DELETE')
											<button type="submit" class="btn btn-sm btn-alt-primary" data-toggle="tooltip" title="elimina" data-original-title="Delete"><i class="fa fa-fw fa-times"></i></button>
										</form>

									</div>
								</td>
							</tr>

							@endforeach

							</tbody>
						</table>

					</div>

				</div>
			</div>
		</div>
		<!-- [ table ] end -->

	</div>
	<!-- [ Main Content ] end -->

@endsection
