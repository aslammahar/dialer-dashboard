@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-12">
        <h2>Create New Dialer</h2>
        
        <form action="{{ route('dialers.store') }}" method="POST">
            @csrf
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Dialer List Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="dialer_ip" class="form-label">Dialer IP *</label>
                                <input type="text" class="form-control @error('dialer_ip') is-invalid @enderror" 
                                       id="dialer_ip" name="dialer_ip" value="{{ old('dialer_ip') }}" required>
                                @error('dialer_ip')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="dialer_weblink" class="form-label">Dialer Weblink *</label>
                                <input type="text" class="form-control @error('dialer_weblink') is-invalid @enderror" 
                                       id="dialer_weblink" name="dialer_weblink" value="{{ old('dialer_weblink') }}" required>
                                @error('dialer_weblink')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="dialer_access" class="form-label">Dialer Access *</label>
                                <input type="text" class="form-control @error('dialer_access') is-invalid @enderror" 
                                       id="dialer_access" name="dialer_access" value="{{ old('dialer_access') }}" required>
                                @error('dialer_access')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="dialer_no" class="form-label">Dialer No *</label>
                                <input type="text" class="form-control @error('dialer_no') is-invalid @enderror" 
                                       id="dialer_no" name="dialer_no" value="{{ old('dialer_no') }}" required>
                                @error('dialer_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="dialer_team" class="form-label">Dialer Team *</label>
                                <input type="text" class="form-control @error('dialer_team') is-invalid @enderror" 
                                       id="dialer_team" name="dialer_team" value="{{ old('dialer_team') }}" required>
                                @error('dialer_team')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="recording_link" class="form-label">Recording Link</label>
                        <input type="text" class="form-control @error('recording_link') is-invalid @enderror" 
                               id="recording_link" name="recording_link" value="{{ old('recording_link') }}">
                        @error('recording_link')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5>Dialer Server Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="dialer_name" class="form-label">Dialer Name *</label>
                                <input type="text" class="form-control @error('dialer_name') is-invalid @enderror" 
                                       id="dialer_name" name="dialer_name" value="{{ old('dialer_name') }}" required>
                                @error('dialer_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="server_no" class="form-label">Server No *</label>
                                <input type="text" class="form-control @error('server_no') is-invalid @enderror" 
                                       id="server_no" name="server_no" value="{{ old('server_no') }}" required>
                                @error('server_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="server_ip" class="form-label">Server IP *</label>
                                <input type="text" class="form-control @error('server_ip') is-invalid @enderror" 
                                       id="server_ip" name="server_ip" value="{{ old('server_ip') }}" required>
                                @error('server_ip')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="folder_name" class="form-label">Folder Name *</label>
                                <input type="text" class="form-control @error('folder_name') is-invalid @enderror" 
                                       id="folder_name" name="folder_name" value="{{ old('folder_name') }}" required>
                                @error('folder_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="server_status" name="server_status" value="1" {{ old('server_status') ? 'checked' : '' }}>
                            <label class="form-check-label" for="server_status">Server Status (Active)</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('dialers.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Dialer</button>
            </div>
        </form>
    </div>
</div>
@endsection