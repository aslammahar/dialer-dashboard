@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Number Lists</h4>
                    <a href="{{ route('number-lists.create') }}" class="btn btn-primary">Add New List</a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Data Vendor</th>
                                    <th>File Name</th>
                                    <th>List ID</th>
                                    <th>Total Numbers</th>
                                    <th>Blocks/Dubs</th>
                                    <th>Dialer Scrubbing</th>
                                    <th>DNC Clean</th>
                                    <th>Clean</th>
                                    <th>Sale</th>
                                    <th>Conversion</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($numberLists as $list)
                                    <tr>
                                        <td>{{ $list->date->format('Y-m-d') }}</td>
                                        <td>{{ $list->data_vendor }}</td>
                                        <td>{{ $list->file_name }}</td>
                                        <td>{{ $list->list_id }}</td>
                                        <td>{{ number_format($list->total_numbers) }}</td>
                                        <td>{{ number_format($list->blocks_dubs_from_same_file) }}</td>
                                        <td>{{ number_format($list->dialer_scrubbing) }}</td>
                                        <td>{{ number_format($list->dnc_clean_numbers) }}</td>
                                        <td>{{ number_format($list->clean) }}</td>
                                        <td>{{ number_format($list->sales_count) }}</td>
                                        <td> % 
                                            <span class="conversion-rate" 
                                                  data-clean="{{ $list->clean }}" 
                                                  data-sale="{{ $list->sales_count }}">
                                              {{ $list->conversion_display }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('number-lists.show', $list) }}" class="btn btn-sm btn-info">View</a>
                                                <a href="{{ route('number-lists.edit', $list) }}" class="btn btn-sm btn-warning">Edit</a>
                                                <form action="{{ route('number-lists.destroy', $list) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center">No records found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $numberLists->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Frontend conversion calculation
document.addEventListener('DOMContentLoaded', function() {
    const conversionElements = document.querySelectorAll('.conversion-rate');
    
    conversionElements.forEach(function(element) {
        const clean = parseInt(element.dataset.clean) || 0;
        const sale = parseInt(element.dataset.sale) || 0;
        
        if (clean > 0 && sale > 0) {
            const conversion = (  sale / clean * 100).toFixed(4);
            element.textContent = conversion;
        } else {
            element.textContent = '0.0000';
        }
    });
});
</script>
@endsection





