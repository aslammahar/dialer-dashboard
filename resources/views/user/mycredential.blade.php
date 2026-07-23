<!-- resources/views/users/mycredentials.blade.php -->
@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Update My Credentials</div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('mycredentials.update') }}">
                        @csrf

                        <div class="form-group">
                            <label for="dialer_id">Dialer ID</label>
                            <input id="dialer_id" type="text" class="form-control @error('dialer_id') is-invalid @enderror" name="dialer_id" value="{{ old('dialer_id', $user->dialer_id) }}" required autocomplete="dialer_id" autofocus>

                            @error('dialer_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="pseudo_name">Pseudo Name</label>
                            <input id="pseudo_name" type="text" class="form-control @error('pseudo_name') is-invalid @enderror" name="pseudo_name" value="{{ old('pseudo_name', $user->pseudo_name) }}" autocomplete="pseudo_name">

                            @error('pseudo_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Update Credentials</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
