

<style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f8f9fa;
    }
    .form-container {
        max-width: 700px;
        background: white;
        padding: 2rem;
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        margin: auto;
        margin-top: 50px;
        margin-bottom: 50px;

        text-align: center;
    }
    h1 {
        font-weight: bold;
        color: #333;
        margin-bottom: 20px;
    }
    .form-group {
        margin-bottom: 20px;
        text-align: left;
    }
    label {
        font-weight: 600;
        color: #555;
        display: block;
    }
    .form-control {
        width: 100%;
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #ccc;
        font-size: 16px;
        transition: all 0.3s ease-in-out;
        background-color: #fafafa;
    }
    .form-control:focus {
        border-color: #4285f4;
        box-shadow: 0px 0px 8px rgba(66, 133, 244, 0.2);
        background-color: white;
    }
    .btn-submit {
        background-color: #4285f4;
        color: white;
        font-weight: bold;
        padding: 12px;
        border: none;
        width: 100%;
        border-radius: 5px;
        transition: 0.3s ease-in-out;
        margin-top: 10px;
    }
    .btn-submit:hover {
        background-color: #2c69c8;
    }
    .btn-copy {
        background-color: #28a745;
        color: white;
        font-weight: bold;
        padding: 10px;
        border: none;
        border-radius: 5px;
        transition: 0.3s ease-in-out;
        margin-top: 10px;
        cursor: pointer;
        width: 100%;
    }
    .btn-copy:hover {
        background-color: #218838;
    }
    .alert-success {
        background: #d4edda;
        color: #155724;
        border-left: 5px solid #28a745;
        padding: 10px;
        border-radius: 5px;
        text-align: center;
        margin-bottom: 10px;
    }
    .copy-success {
        display: none;
        color: green;
        margin-top: 10px;
        font-weight: bold;
    }
</style>

<div class="form-container">
    <h1>{{ $our_campaign->name }}</h1>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <button class="btn-copy" onclick="copyFormLink()">📋 Copy Form Link</button>
    <p class="copy-success" id="copySuccess">✅ Link copied successfully!</p>

    <form method="POST" action="{{ route('our_campaigns.submit', $our_campaign) }}">
        @csrf

        @foreach ($formFields as $field)
            @if ($field->field_role === 'user') {{-- Check field_role --}}
                <div class="form-group">
                    <label for="{{ $field->name }}">{{ $field->label }}</label>
                    
                    @if ($field->type == 'text')
                        <input type="text" name="{{ $field->name }}" id="{{ $field->name }}" class="form-control" @if ($field->required) required @endif value="{{ old($field->name) }}">
                    
                    @elseif ($field->type == 'email')
                        <input type="email" name="{{ $field->name }}" id="{{ $field->name }}" class="form-control" @if ($field->required) required @endif value="{{ old($field->name) }}">

                    @elseif ($field->type == 'number')
                        <input type="number" name="{{ $field->name }}" id="{{ $field->name }}" class="form-control" @if ($field->required) required @endif value="{{ old($field->name) }}">
                    
                    @elseif ($field->type == 'textarea')
                        <textarea name="{{ $field->name }}" id="{{ $field->name }}" class="form-control" @if ($field->required) required @endif>{{ old($field->name) }}</textarea>

                    @elseif ($field->type == 'select')
                        <select name="{{ $field->name }}" id="{{ $field->name }}" class="form-control" @if ($field->required) required @endif>
                            <option value="" disabled selected>Select an option</option>
                            @php $optionsArray = explode(',', $field->options); @endphp
                            @foreach ($optionsArray as $option)
                                <option value="{{ trim($option) }}" @if(old($field->name) == trim($option)) selected @endif>{{ trim($option) }}</option>
                            @endforeach
                        </select>
                    
                    @elseif ($field->type == 'date')
                        <input type="date" name="{{ $field->name }}" id="{{ $field->name }}" class="form-control" @if ($field->required) required @endif value="{{ old($field->name) }}">

                    @elseif ($field->type == 'file')
                        <input type="file" name="{{ $field->name }}" id="{{ $field->name }}" class="form-control" @if ($field->required) required @endif>
                    @endif
                </div>
            @endif {{-- Close the if statement --}}
        @endforeach

        <button type="submit" class="btn-submit">Submit</button>
    </form>
</div>
<script>
    function copyFormLink() {
        const formLink = "{{ url()->current() }}"; // Get current form URL
        navigator.clipboard.writeText(formLink).then(() => {
            document.getElementById("copySuccess").style.display = "block";
            setTimeout(() => {
                document.getElementById("copySuccess").style.display = "none";
            }, 2000);
        }).catch(err => console.error('Failed to copy: ', err));
    }
</script>
