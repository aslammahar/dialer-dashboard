<!-- resources/views/search_avatar_leads.blade.php -->

@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Search Avatar Leads</h2>
    <form action="{{ route('search_avatar_leads') }}" method="GET">
        <div class="form-group">
            <label for="search">Search by Lead ID or Phone Number:</label>
            <input type="text" name="search" id="search" class="form-control" placeholder="Enter Lead ID or Phone Number">
        </div>
        <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <script>
        function updateCount(id) {
            const closerStatus = document.getElementById('closer_status_' + id).value;
            const countSelect = document.getElementById('count_' + id);
            countSelect.value = (closerStatus === 'dnc') ? '0' : '1';
        }
    </script>

    @if(isset($avatarLeads))

    <h3>Search Results</h3>
    <div class="table-responsive">

        <table class="table datatable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Lead ID</th>
                    <!-- <th>Phone Number</th> -->
                    <th>Dialer Id</th>
                    <th>Dialer No</th>
                    <th>Center</th>
                    <th>Created At</th>
                    <th>Closer Status</th>
                    <th>Count</th>
                    <th>Sales Update</th>
                    <!-- <th>Count</th> -->
                </tr>
            </thead>
            <tbody>
                @foreach($avatarLeads as $avatarLead)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $avatarLead->lead_id }}</td>
                    <!-- <td>{{ $avatarLead->phone_number }}</td> -->
                    <td>{{ $avatarLead->dialer_id }}</td>
                    <td>{{ $avatarLead->dialername }}</td>
                    <td>{{ $avatarLead->centername }}</td>
                    <td>{{ $avatarLead->created_at }}</td>
                    <td>{{ $avatarLead->closer_status }}</td>
                    <td>{{ $avatarLead->count }}</td>
                    <td>
                        @can('update avatar lead')
                        <form action="{{ route('update_search_avatar_leads', $avatarLead->id) }}" method="POST" class="d-flex">
                            @csrf
                            @method('PUT')
                            <div class="form-group mr-4">
                                <select name="closer_status" id="closer_status_{{ $avatarLead->id }}" class="form-control" style="padding-right:50px;" onchange="updateCount('{{ $avatarLead->id }}')">
                                    <option value="sale_made">Sale Made</option>
                                    <option value="dnc">DNC</option>
                                    <option value="callback">Callback</option>
                                    <option value="dropped">Dropped</option>
                                </select>
                            </div>
                            <div class="form-group mr-4">
                                <select name="count" id="count_{{ $avatarLead->id }}" class="form-control" style="padding-right:50px;">
                                    @for ($i = 1; $i <= 20; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                    <option value="0">0</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary" style="margin-left: 60px;">Update</button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
    @endif
</div>
@endsection