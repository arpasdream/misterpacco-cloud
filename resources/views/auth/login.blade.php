@extends('layouts.auth-master')

@section('content')

    <div class="card">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="card-body">
                    <img src="/assets/images/logo-dark.png" alt="Logo" class="img-fluid mb-4 d-block mx-auto" style="max-width: 100%;">

                    <h4 class="mb-3 f-w-400">Accedi al tuo account</h4>

                    @include('layouts.partials.messages')

                    <form action="{{ route('login.perform') }}" method="post">
                        @csrf

                        <div class="form-group mb-2">
                            <label class="form-label">Codice Accesso</label>
                            <input type="text" name="codice" id="codice" class="form-control @error('codice') is-invalid @enderror" placeholder="inserisci codice"  value="{{ old('codice') }}" maxlength="10">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="inserisci password" maxlength="10">
                        </div>

                        <!--<div class="form-group text-start mt-2">
                            <div class="checkbox checkbox-primary d-inline">
                                <input type="checkbox" name="checkbox-fill-1" id="checkbox-fill-a1">
                                <label for="checkbox-fill-a1" class="cr"> Rimani connesso</label>
                            </div>
                        </div>-->

                        <button class="btn btn-primary mb-4 btf">Accedi</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
