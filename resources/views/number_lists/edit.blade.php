{{-- resources/views/number_lists/edit.blade.php --}}
@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Edit Number List</h4>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('number-lists.update', $numberList) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="date" class="form-label">Date</label>
                                <input type="date" class="form-control @error('date') is-invalid @enderror" 
                                       id="date" name="date" value="{{ old('date', $numberList->date->format('Y-m-d')) }}" required>
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="vendor_id" class="form-label">Select Vendor</label>
                                <select name="vendor_id" id="vendor_id" class="form-control @error('vendor_id') is-invalid @enderror" required>
                                    <option value="">-- Choose Vendor --</option>
                                    @foreach($vendors as $vendor)
                                        <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                            {{ $vendor->vendor_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('vendor_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="data_vendor" class="form-label">Data Vendor</label>
                                <input type="text" class="form-control @error('data_vendor') is-invalid @enderror" 
                                       id="data_vendor" name="data_vendor" value="{{ old('data_vendor', $numberList->data_vendor) }}" required>
                                @error('data_vendor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="file_name" class="form-label">File Name</label>
                                <input type="text" class="form-control @error('file_name') is-invalid @enderror" 
                                       id="file_name" name="file_name" value="{{ old('file_name', $numberList->file_name) }}" required>
                                @error('file_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="list_id" class="form-label">List ID</label>
                                <input type="number" class="form-control @error('list_id') is-invalid @enderror" 
                                       id="list_id" name="list_id" value="{{ old('list_id', $numberList->list_id) }}" required>
                                @error('list_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="total_numbers" class="form-label">Total Numbers</label>
                                <input type="number" class="form-control @error('total_numbers') is-invalid @enderror" 
                                       id="total_numbers" name="total_numbers" value="{{ old('total_numbers', $numberList->total_numbers) }}" required>
                                @error('total_numbers')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="blocks_dubs_from_same_file" class="form-label">Blocks/Dubs from Same File</label>
                                <input type="number" class="form-control @error('blocks_dubs_from_same_file') is-invalid @enderror" 
                                       id="blocks_dubs_from_same_file" name="blocks_dubs_from_same_file" value="{{ old('blocks_dubs_from_same_file', $numberList->blocks_dubs_from_same_file) }}">
                                @error('blocks_dubs_from_same_file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="dialer_scrubbing" class="form-label">Dialer Scrubbing</label>
                                <input type="number" class="form-control @error('dialer_scrubbing') is-invalid @enderror" 
                                       id="dialer_scrubbing" name="dialer_scrubbing" value="{{ old('dialer_scrubbing', $numberList->dialer_scrubbing) }}">
                                @error('dialer_scrubbing')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="dnc_clean_numbers" class="form-label">DNC Clean Numbers</label>
                                <input type="number" class="form-control @error('dnc_clean_numbers') is-invalid @enderror" 
                                       id="dnc_clean_numbers" name="dnc_clean_numbers" value="{{ old('dnc_clean_numbers', $numberList->dnc_clean_numbers) }}">
                                @error('dnc_clean_numbers')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="clean" class="form-label">Clean</label>
                                <input type="number" class="form-control @error('clean') is-invalid @enderror" 
                                       id="clean" name="clean" value="{{ old('clean', $numberList->clean) }}">
                                @error('clean')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('number-lists.index') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update List</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection