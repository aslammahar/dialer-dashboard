@extends('layouts.admin')

@section('content')
    <h1>Edit Campaign for Project: {{ $our_project->name }}</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('our_projects.our_campaigns.update', [$our_project, $our_campaign]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT') {{-- Important for updates --}}

        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $our_campaign->name) }}" required>
        </div>

        <div class="form-group">
            <label for="description">Description:</label>
            <textarea name="description" id="description" class="form-control">{{ old('description', $our_campaign->description) }}</textarea>
        </div>

        <h2>Edit Form Fields</h2>

        <div id="form-fields-container">
            @if ($our_campaign->newFormFields)
                @foreach ($our_campaign->newFormFields as $index => $field)
                    <div class="form-group field-group">
                        <label>Label:</label>
                        <input type="text" name="fields[{{ $index }}][label]" class="form-control" value="{{ old("fields.$index.label", $field->label) }}" required>

                        <label>Name (Field Name):</label>
                        <input type="text" name="fields[{{ $index }}][name]" class="form-control" value="{{ old("fields.$index.name", $field->name) }}" required>

                        <label>Type:</label>
                        <select name="fields[{{ $index }}][type]" class="form-control field-type" required>
                            <option value="text" @if(old("fields.$index.type", $field->type) === 'text') selected @endif>Text</option>
                            <option value="email" @if(old("fields.$index.type", $field->type) === 'email') selected @endif>Email</option>
                            <option value="number" @if(old("fields.$index.type", $field->type) === 'number') selected @endif>Number</option>
                            <option value="textarea" @if(old("fields.$index.type", $field->type) === 'textarea') selected @endif>Textarea</option>
                            <option value="select" @if(old("fields.$index.type", $field->type) === 'select') selected @endif>Select</option>
                            <option value="date" @if(old("fields.$index.type", $field->type) === 'date') selected @endif>Date</option>
                            <option value="file" @if(old("fields.$index.type", $field->type) === 'file') selected @endif>File</option>
                        </select>

                        <div class="options-container" style="display: {{ (old("fields.$index.type", $field->type) === 'select') ? 'block' : 'none' }};">
                            <label>Options (comma-separated):</label>
                            <input type="text" name="fields[{{ $index }}][options]" class="form-control" value="{{ old("fields.$index.options", $field->options) }}">
                        </div>

                        <div class="field-value-container" style="display: {{ (in_array(old("fields.$index.type", $field->type), ['date','text','number','email','textarea'])) ? 'block' : 'none' }};">
                            <label class="field-value-label">Value:</label>
                            <input type="{{ old("fields.$index.type", $field->type) === 'date' ? 'date' : 'text' }}" name="fields[{{ $index }}][value]" class="form-control field-value" value="{{ old("fields.$index.value", $field->value) }}">
                        </div>

                        <label>Role:</label>
                        <select name="fields[{{ $index }}][role]" class="form-control" required>
                            <option value="user" @if(old("fields.$index.role", $field->role) === 'user') selected @endif>User</option>
                            <option value="qa" @if(old("fields.$index.role", $field->role) === 'qa') selected @endif>QA</option>
                            <option value="admin" @if(old("fields.$index.role", $field->role) === 'admin') selected @endif>Admin</option>
                            <option value="client" @if(old("fields.$index.role", $field->role) === 'client') selected @endif>Client</option>
                        </select>
                        <input type="hidden" name="fields[{{ $index }}][field_role]" value="{{ $field->field_role }}">

                        <label>Required:</label>
                        <input type="checkbox" name="fields[{{ $index }}][required]" value="1" @if(old("fields.$index.required", $field->required)) checked @endif>

                        <button type="button" class="btn btn-danger remove-field">Remove</button>
                    </div>
                @endforeach
            @endif
        </div>

        <button type="button" id="add-field" class="btn btn-success mt-3">Add Field</button>
        <button type="submit" class="btn btn-primary mt-3">Update Campaign</button>
        <a href="{{ route('our_projects.our_campaigns.index', $our_project) }}" class="btn btn-secondary mt-3">Cancel</a>
    </form>

    <template id="field-template">
    <div class="form-group field-group">
            <label>Label:</label>
            <input type="text" name="fields[__INDEX__][label]" class="form-control" required>

            <label>Name:</label>
            <input type="text" name="fields[__INDEX__][name]" class="form-control" required>

            <label>Type:</label>
            <select name="fields[__INDEX__][type]" class="form-control field-type" required>
                <option value="text">Text</option>
                <option value="email">Email</option>
                <option value="number">Number</option>
                <option value="textarea">Textarea</option>
                <option value="select">Select</option>
                <option value="date">Date</option>
                <option value="file">File</option>
            </select>

            <div class="options-container" style="display: none;">
                <label>Options (comma-separated - one option per line):</label> {{-- Updated label --}}
                <textarea name="fields[__INDEX__][options]" class="form-control"></textarea> {{-- Use textarea --}}
            </div>

            <div class="field-value-container" style="display: none;">
                <label class="field-value-label">Value:</label>
                <input type="text" name="fields[__INDEX__][value]" class="form-control field-value">
            </div>

            <label>Role:</label>
            <select name="fields[__INDEX__][role]" class="form-control" required>
                <option value="user">User</option>
                <option value="qa">QA</option>
                <option value="admin">Admin</option>
                <option value="client">Client</option>
            </select>
            <input type="hidden" name="fields[__INDEX__][field_role]" value="">

            <label>Required:</label>
            <input type="checkbox" name="fields[__INDEX__][required]" value="1">

            <button type="button" class="btn btn-danger remove-field">Remove</button>
        </div>
    </template>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const formFieldsContainer = document.getElementById('form-fields-container');
            const addFieldButton = document.getElementById('add-field');
            const fieldTemplate = document.getElementById('field-template').content.cloneNode(true);
            let fieldCounter = 0;

            addFieldButton.addEventListener('click', function() {
                console.log("Template before cloning:", document.getElementById('field-template').innerHTML); // Add this line

                const newField = fieldTemplate.cloneNode(true);
                const index = fieldCounter++;

                newField.querySelectorAll('[name]').forEach(el => {
                    el.name = el.name.replace('__INDEX__', index);
                    if (el.id) {
                        el.id = el.id.replace('__INDEX__', index);
                    }
                });

                const typeSelect = newField.querySelector('.field-type');
                const optionsContainer = newField.querySelector('.options-container');
                const roleSelect = newField.querySelector('select[name$="[role]"]');
                const hiddenRoleInput = newField.querySelector('input[name$="[field_role]"]');


                typeSelect.addEventListener('change', function() {
                    optionsContainer.style.display = this.value === 'select' ? 'block' : 'none';
                });

                roleSelect.addEventListener('change', function() {
                    hiddenRoleInput.value = this.value;
                });

                // Set initial value for hidden role input
                roleSelect.dispatchEvent(new Event('change'));

                newField.querySelector('.remove-field').addEventListener('click', function() {
                    newField.remove();
                });

                formFieldsContainer.appendChild(newField);
            });
        });
    </script>
@endsection