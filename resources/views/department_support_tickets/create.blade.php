@extends('layouts.admin')

@section('page-title', 'Submit Department Support Request')

@section('content')
<div class="card">
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('department_support_tickets.store') }}" method="POST">
            @csrf
            <div class="row">
                {{-- Department --}}
                <div class="col-md-4">
                    <label for="department" class="form-label">Select Department</label>
                    <select id="department" class="form-control" required>
                        <option value="">Select Department</option>
                        @foreach($departments->groupBy('role_id') as $roleId => $group)
                            <option value="{{ $roleId }}">{{ \App\Models\Role::find($roleId)->name ?? 'Unknown' }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Title --}}
                <div class="col-md-4">
                    <label for="title" class="form-label">Select Title</label>
                    <select name="department_support_id" id="title" class="form-control" required>
                        <option value="">Select Title</option>
                    </select>
                </div>

                {{-- Subject --}}
                <div class="col-md-4">
                    <label class="form-label">Subject</label>
                    <input type="text" name="subject" class="form-control" placeholder="Enter subject" required>
                </div>

                {{-- Description --}}
                <div class="col-md-12 mt-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Describe your issue..."></textarea>
                </div>

                {{-- Submit --}}
                <div class="col-md-3 mt-3">
                    <button type="submit" class="btn btn-primary w-100">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $('#department').on('change', function() {
        var roleId = $(this).val();
        if (roleId) {
            $.ajax({
                url: '/get-titles/' + roleId,
                type: 'GET',
                success: function(titles) {
                    $('#title').empty().append('<option value="">Select Title</option>');
                    $.each(titles, function(id, title) {
                        $('#title').append('<option value="' + id + '">' + title + '</option>');
                    });
                }
            });
        }
    });
</script>
@endsection
