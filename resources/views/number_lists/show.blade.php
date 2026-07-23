{{-- resources/views/number_lists/show.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Number List Details</h4>
                    <div>
                        <a href="{{ route('number-lists.edit', $numberList) }}" class="btn btn-warning">Edit</a>
                        <a href="{{ route('number-lists.index') }}" class="btn btn-secondary">Back</a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th>Date:</th>
                                    <td>{{ $numberList->date->format('Y-m-d') }}</td>
                                </tr>
                                <tr>
                                    <th>Data Vendor:</th>
                                    <td>{{ $numberList->data_vendor }}</td>
                                </tr>
                                <tr>
                                    <th>File Name:</th>
                                    <td>{{ $numberList->file_name }}</td>
                                </tr>
                                <tr>
                                    <th>List ID:</th>
                                    <td>{{ $numberList->list_id }}</td>
                                </tr>
                                <tr>
                                    <th>Total Numbers:</th>
                                    <td>{{ number_format($numberList->total_numbers) }}</td>
                                </tr>
                                <tr>
                                    <th>Blocks/Dubs:</th>
                                    <td>{{ number_format($numberList->blocks_dubs_from_same_file) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th>Dialer Scrubbing:</th>
                                    <td>{{ number_format($numberList->dialer_scrubbing) }}</td>
                                </tr>
                                <tr>
                                    <th>DNC Clean Numbers:</th>
                                    <td>{{ number_format($numberList->dnc_clean_numbers) }}</td>
                                </tr>
                                <tr>
                                    <th>Clean:</th>
                                    <td>{{ number_format($numberList->clean) }}</td>
                                </tr>
                                <tr>
                                    <th>Sale:</th>
                                    <td>{{ number_format($numberList->sales_count) }}</td>
                                </tr>
                                <tr>
                                    <th>Conversion:</th>
                                    <td class="conversion-rate" 
                                        data-clean="{{ $numberList->clean }}" 
                                        data-sale="{{ $numberList->sales_count }}"> 
                                        {{ $numberList->conversion_display }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Frontend conversion calculation
document.addEventListener('DOMContentLoaded', function() {
    const element = document.querySelector('.conversion-rate');
    const clean = parseInt(element.dataset.clean) || 0;
    const sale = parseInt(element.dataset.sale) || 0;
    
    if (clean > 0 && sale > 0) {
        const conversion = (sale /  clean * 100 ).toFixed(4);
        element.textContent = conversion;
    } else {
        element.textContent = '0.0000';
    }
});
</script>
@endsection