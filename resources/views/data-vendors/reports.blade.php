@extends('layouts.admin')

@section('content')
    <div class="container">
        <h2 class="mb-4">Reports for Vendor: {{ $vendor->name }}</h2>

        @if($reports->isEmpty())
            <div class="alert alert-warning">No reports found for this vendor.</div>
        @else
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Date</th>
                        <th>Vendor</th>
                        <th>File Name</th>
                        <th>List ID</th>
                        <th>Total Numbers</th>
                        <th>Blocks Dubs</th>
                        <th>Dialer Scrubbing</th>
                        <th>DNC Clean Numbers</th>
                        <th>Clean</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reports as $report)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($report->date)->format('d-M-Y') }}</td>
                            <td>{{ $report->vendor->vendor_name ?? 'N/A' }}</td>
                            <td>{{ $report->file_name }}</td>
                            <td>{{ $report->list_id }}</td>
                            <td>{{ $report->total_numbers }}</td>
                            <td>{{ $report->blocks_dubs_from_same_file }}</td>
                            <td>{{ $report->dialer_scrubbing }}</td>
                            <td>{{ $report->dnc_clean_numbers }}</td>
                            <td>{{ $report->clean }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection