@extends('layouts.admin')

@section('content')
    <h1>Edit Response (CLoser's Fields)</h1>

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

    <form action="{{ route('our_campaigns.responses.closer_update', [$our_campaign, $campaign_response]) }}" 
          method="POST" 
          id="closerUpdateForm">
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

        <!-- closer Fields -->
        <div class="card">
            <div class="card-header">
            </div>
            <div class="card-body">
                @if (isset($fieldsGroupedByRole['closer']) && $fieldsGroupedByRole['closer']->isNotEmpty())
                    @foreach ($fieldsGroupedByRole['closer'] as $field)
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
                                           name="closer[{{ $field->name }}]" 
                                           id="{{ $field->name }}" 
                                           class="form-control @error('closer.'.$field->name) is-invalid @enderror" 
                                           value="{{ old('closer.'.$field->name, $submittedData[$field->name] ?? '') }}"
                                           @if ($field->required) required @endif>
                                    @break

                                @case('email')
                                    <input type="email" 
                                           name="closer[{{ $field->name }}]" 
                                           id="{{ $field->name }}" 
                                           class="form-control @error('closer.'.$field->name) is-invalid @enderror" 
                                           value="{{ old('closer.'.$field->name, $submittedData[$field->name] ?? '') }}"
                                           @if ($field->required) required @endif>
                                    @break

                                @case('number')
                                    <input type="number" 
                                           name="closer[{{ $field->name }}]" 
                                           id="{{ $field->name }}" 
                                           class="form-control @error('closer.'.$field->name) is-invalid @enderror" 
                                           value="{{ old('closer.'.$field->name, $submittedData[$field->name] ?? '') }}"
                                           @if ($field->required) required @endif>
                                    @break

                                @case('textarea')
                                    <textarea name="closer[{{ $field->name }}]" 
                                              id="{{ $field->name }}" 
                                              class="form-control @error('closer.'.$field->name) is-invalid @enderror" 
                                              @if ($field->required) required @endif>{{ old('closer.'.$field->name, $submittedData[$field->name] ?? '') }}</textarea>
                                    @break

                                @case('select')
                                    <select name="closer[{{ $field->name }}]" 
                                            id="{{ $field->name }}" 
                                            class="form-control @error('closer.'.$field->name) is-invalid @enderror" 
                                            @if ($field->required) required @endif>
                                        <option value="">Select an option</option>
                                        @if ($field->options)
                                            @foreach (explode(',', $field->options) as $option)
                                                @php $option = trim($option); @endphp
                                                <option value="{{ $option }}" 
                                                        @if(old('closer.'.$field->name, $submittedData[$field->name] ?? '') == $option) selected @endif>
                                                    {{ $option }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @break

                                @case('date')
                                    <input type="date" 
                                           name="closer[{{ $field->name }}]" 
                                           id="{{ $field->name }}" 
                                           class="form-control @error('closer.'.$field->name) is-invalid @enderror" 
                                           value="{{ old('closer.'.$field->name, $submittedData[$field->name] ?? '') }}"
                                           @if ($field->required) required @endif>
                                    @break
                            @endswitch

                            @error('closer.'.$field->name)
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    @endforeach
                @else
                    <p>No closer fields defined for this campaign.</p>
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