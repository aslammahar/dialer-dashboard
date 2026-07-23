@extends('layouts.admin')

@section('content')
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Selection</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
   
<form action="{{ route('oour_campaigns.responses.update2', [$our_campaign, $campaign_response]) }}" 
          method="POST" 
          id="updateForm">
        @csrf
        @method('PUT')

        <!-- Original Submission Data (Disabled) -->
        <div class="card mb-4">
            <div class="card-header">
                <h2>Original Submission Data</h2>
            </div>
            <div class="card-body">
                @if ($campaign_response->submission_data)
                    @foreach ($campaign_response->submission_data as $key => $value)
                        <div class="form-group mb-3">
                            <label for="original_{{ $key }}" class="form-label">{{ ucfirst($key) }}</label>
                            <input type="text" 
                                   id="original_{{ $key }}" 
                                   class="form-control" 
                                   value="{{ $value }}" 
                                   disabled>
                        </div>
                    @endforeach
                @else
                    <p>No original data submitted for this response.</p>
                @endif
            </div>
        </div>

        <!-- Admin Fields -->
        <div class="card">
            <div class="card-header">
                <h2>Admin Fields</h2>
            </div>
            <div class="card-body">
                @if (isset($fieldsGroupedByRole['admin']) && $fieldsGroupedByRole['admin']->isNotEmpty())
                    @foreach ($fieldsGroupedByRole['admin'] as $field)
                        <div class="form-group mb-3">
                            <label for="{{ $field->name }}" class="form-label">
                                {{ $field->label }}
                                @if ($field->required)
                                    <span class="text-danger">*</span>
                                @endif
                            </label>

                            @switch($field->type)
                                @case('text')
                                    <input type="text" 
                                           name="{{ $field->name }}" 
                                           id="{{ $field->name }}" 
                                           class="form-control @error($field->name) is-invalid @enderror" 
                                           value="{{ old($field->name, $submittedData[$field->name] ?? '') }}"
                                           @if ($field->required) required @endif>
                                    @break

                                @case('email')
                                    <input type="email" 
                                           name="{{ $field->name }}" 
                                           id="{{ $field->name }}" 
                                           class="form-control @error($field->name) is-invalid @enderror" 
                                           value="{{ old($field->name, $submittedData[$field->name] ?? '') }}"
                                           @if ($field->required) required @endif>
                                    @break

                                @case('number')
                                    <input type="number" 
                                           name="{{ $field->name }}" 
                                           id="{{ $field->name }}" 
                                           class="form-control @error($field->name) is-invalid @enderror" 
                                           value="{{ old($field->name, $submittedData[$field->name] ?? '') }}"
                                           @if ($field->required) required @endif>
                                    @break

                                @case('textarea')
                                    <textarea name="{{ $field->name }}" 
                                              id="{{ $field->name }}" 
                                              class="form-control @error($field->name) is-invalid @enderror" 
                                              @if ($field->required) required @endif>{{ old($field->name, $submittedData[$field->name] ?? '') }}</textarea>
                                    @break

                                @case('select')
                                    <select name="{{ $field->name }}" 
                                            id="{{ $field->name }}" 
                                            class="form-control @error($field->name) is-invalid @enderror" 
                                            @if ($field->required) required @endif>
                                        <option value="">Select an option</option>
                                        @if ($field->options)
                                            @foreach (explode(',', $field->options) as $option)
                                                @php $option = trim($option); @endphp
                                                <option value="{{ $option }}" 
                                                        @if(old($field->name, $submittedData[$field->name] ?? '') == $option) selected @endif>
                                                    {{ $option }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @break

                                @case('date')
                                    <input type="date" 
                                           name="{{ $field->name }}" 
                                           id="{{ $field->name }}" 
                                           class="form-control @error($field->name) is-invalid @enderror" 
                                           value="{{ old($field->name, $submittedData[$field->name] ?? '') }}"
                                           @if ($field->required) required @endif>
                                    @break
                            @endswitch

                            @error($field->name)
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    @endforeach
                @else
                    <p>No Admin fields defined for this campaign.</p>
                @endif
            </div>
        </div>
    
        <div class="mb-3">
        <label for="parent" class="form-label">Select Parent:</label>
        <select id="parent" name="parent" class="form-select">
            <option value="">Select Parent</option>
            @foreach($parentClients as $parent)
                <option value="{{ $parent->id }}">{{ $parent->name }}</option>
            @endforeach
        </select>
    </div>

    <!-- Child Client Dropdown -->
    <div class="mb-3">
        <label for="child" class="form-label">Select Child:</label>
        <select id="child" name="child" class="form-select">
            <option value="">Select Child</option>
        </select>
    </div>



<div class="mt-4">
    <button type="submit" class="btn btn-primary">Update Response</button>
    <a href="{{ route('our_campaigns.responses.index', [$our_campaign]) }}" 
       class="btn btn-secondary ms-2">Back to Responses</a>
</div>
    </form>




    <script>
     $(document).ready(function() {
    $('#parent').change(function() {
        var parentId = $(this).val();
        $('#child').html('<option value="">Select Child</option>');

        if (parentId) {
            $.ajax({
                url: '{{ url("clients") }}/' + parentId + '/children',
                type: 'GET',
                success: function(response) {
                    console.log("Received data:", response); // Debugging output

                    $('#child').empty().append('<option value="">Select Child</option>');

                    if (response.length > 0) {
                        $.each(response, function(index, user) {
                            $('#child').append('<option value="'+ user.id +'">'+ user.name +'</option>');
                        });
                    } else {
                        $('#child').append('<option value="">No Clients Available</option>');
                    }
                },
                error: function(xhr, status, error) {
                    console.log("Error:", xhr.responseText); // Debugging output
                }
            });
        }
    });
});

</script>

   
    




@endsection

@push('scripts')
<script>
document.getElementById('updateForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    fetch(this.action, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json',
        },
        body: new FormData(this)
    })
    .then(response => response.text())
    .then(data => {
        try {
            const jsonData = JSON.parse(data);
            if (jsonData.success) {
                window.location.href = jsonData.redirect;
            } else {
                throw new Error(jsonData.message || 'Update failed');
            }
        } catch (error) {
            // If response is not JSON, it might be a redirect
            if (data.includes('Redirecting to')) {
                window.location.reload();
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the response');
    });
});
</script>
@endpush