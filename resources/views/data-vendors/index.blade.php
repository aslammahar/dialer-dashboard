@extends('layouts.admin')

@section('content')
    <h1>Data Vendors</h1>

    <!-- Create Button -->
    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createVendorModal">
        Create Data Vendor
    </button>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Vendor Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dataVendors as $dataVendor)
                <tr>
                    <td>{{ $dataVendor->id }}</td>
                    <td>{{ $dataVendor->vendor_name }}</td>
                    <td>
                        <a href="{{ route('data-vendor.users', $dataVendor->id) }}" class="btn btn-sm btn-success">
                            Assign Users
                        </a>
                        <a href="{{ route('data-vendor.reports', $dataVendor->id) }}" class="btn btn-sm btn-info">
                            View Report
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Create Vendor Modal -->
    <div class="modal fade" id="createVendorModal" tabindex="-1" aria-labelledby="createVendorModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('data-vendor.create') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createVendorModalLabel">Create Data Vendor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="vendorName" class="form-label">Vendor Name</label>
                            <input type="text" name="name" id="vendorName" class="form-control" required>
                        </div>
                        {{-- <div class="mb-3">
                            <label for="vendorEmail" class="form-label">Vendor Email</label>
                            <input type="email" name="vendor_email" id="vendorEmail" class="form-control">
                        </div> --}}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection