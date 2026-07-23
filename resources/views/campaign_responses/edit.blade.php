@extends('layouts.admin')

@section('content')
    <h1>Edit Response</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('our_campaigns.responses.update', [$our_campaign, $campaign_response]) }}" 
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

        <!-- QA Fields -->
        <div class="card">
            <div class="card-header">
                <h2>QA Fields</h2>
            </div>
            <div class="card-body">
                @if (isset($fieldsGroupedByRole['qa']) && $fieldsGroupedByRole['qa']->isNotEmpty())
                    @foreach ($fieldsGroupedByRole['qa'] as $field)
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
                                           name="qa[{{ $field->name }}]" 
                                           id="{{ $field->name }}" 
                                           class="form-control @error('qa.' . $field->name) is-invalid @enderror" 
                                           value="{{ old('qa.' . $field->name, $submittedData[$field->name] ?? '') }}"
                                           @if ($field->required) required @endif>
                                    @break

                                @case('email')
                                    <input type="email" 
                                           name="qa[{{ $field->name }}]" 
                                           id="{{ $field->name }}" 
                                           class="form-control @error('qa.' . $field->name) is-invalid @enderror" 
                                           value="{{ old('qa.' . $field->name, $submittedData[$field->name] ?? '') }}"
                                           @if ($field->required) required @endif>
                                    @break

                                @case('number')
                                    <input type="number" 
                                           name="qa[{{ $field->name }}]" 
                                           id="{{ $field->name }}" 
                                           class="form-control @error('qa.' . $field->name) is-invalid @enderror" 
                                           value="{{ old('qa.' . $field->name, $submittedData[$field->name] ?? '') }}"
                                           @if ($field->required) required @endif>
                                    @break

                                @case('textarea')
                                    <textarea name="qa[{{ $field->name }}]" 
                                              id="{{ $field->name }}" 
                                              class="form-control @error('qa.' . $field->name) is-invalid @enderror" 
                                              @if ($field->required) required @endif>{{ old('qa.' . $field->name, $submittedData[$field->name] ?? '') }}</textarea>
                                    @break

                                @case('select')
                                    <select name="qa[{{ $field->name }}]" 
                                            id="{{ $field->name }}" 
                                            class="form-control @error('qa.' . $field->name) is-invalid @enderror" 
                                            @if ($field->required) required @endif>
                                        <option value="">Select an option</option>
                                        @if ($field->options)
                                            @foreach (explode(',', $field->options) as $option)
                                                @php $option = trim($option); @endphp
                                                <option value="{{ $option }}" 
                                                        @if(old('qa.' . $field->name, $submittedData[$field->name] ?? '') == $option) selected @endif>
                                                    {{ $option }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @break

                                @case('date')
                                    <input type="date" 
                                           name="qa[{{ $field->name }}]" 
                                           id="{{ $field->name }}" 
                                           class="form-control @error('qa.' . $field->name) is-invalid @enderror" 
                                           value="{{ old('qa.' . $field->name, $submittedData[$field->name] ?? '') }}"
                                           @if ($field->required) required @endif>
                                    @break
                            @endswitch

                            @error('qa.' . $field->name)
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    @endforeach
                @else
                    <p>No QA fields defined for this campaign.</p>
                @endif
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Update Response</button>
            <a href="{{ route('our_campaigns.responses.index', [$our_campaign]) }}" 
               class="btn btn-secondary ms-2">Back to Responses</a>
        </div>
    </form>
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