<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{asset('css/summernote/summernote-bs4.css')}}">
    <style>
        /* Add custom CSS to create horizontal scroll for the table */
        .table-container {
            overflow-x: auto;
        }
  
    </style>

<style>
    .pagination-container {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }

    .pagination-container button {
        padding: 5px 10px;
        margin: 0 5px;
        border: 1px solid #ccc;
        background-color: #fff;
        cursor: pointer;
    }

    .pagination-container button:disabled {
        cursor: not-allowed;
    }
</style>

</head>
<body>

    @extends('layouts.admin')


@section('page-title')


<a href="avatar-leads"><u> Avatar Leads </u></a><br>

<a href="avatar-q-a-leads" ><u> Edit Avatar Leads </u> </a> <br><br>
    {{__('Manage Leads')}}
@endsection



@push('script-page')
    <!-- Remove these two lines as they are already included above -->
    <!--<script src="{{asset('css/summernote/jquery-3.5.1.js')}}"></script>-->
    <!--<script src="{{asset('css/summernote/dataTables.min.js')}}"></script>-->
    <script>
        function exportSelectedLeads() {
            var selectedLeads = [];
            var checkboxes = document.querySelectorAll('input[name="selected_leads[]"]:checked');
    
            checkboxes.forEach(function(checkbox) {
                selectedLeads.push(checkbox.value);
            });
    
            if (selectedLeads.length > 0) {
                var form = document.getElementById('exportLeadsForm');
                var input = document.getElementById('selectedLeadsInput');
                input.value = selectedLeads.join(',');
                form.submit();
            } else {
                alert('Please select at least one lead to export.');
            }
        }
    </script>
    


<script>
    function selectAllLeads() {
        var checkboxes = document.querySelectorAll('input[name="selected_leads[]"]');
        var selectAllCheckbox = document.getElementById('selectAllCheckbox');
        
        checkboxes.forEach(function(checkbox) {
            checkbox.checked = selectAllCheckbox.checked;
        });
    }

    document.getElementById('selectAllCheckbox').addEventListener('change', selectAllLeads);
</script>

    



    
@endpush

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Lead')}}</li>
    <li class="breadcrumb-item">{{__('Export Leads')}}</li>
    
   
@endsection



@section('content')
    <!-- Export Selected Leads Form -->
    <form action="{{ route('leads-export') }}" method="post" id="exportLeadsForm">
        @csrf
        <input type="hidden" name="selected_leads" id="selectedLeadsInput">
        <label>
            <input type="checkbox" id="selectAllCheckbox"> Select All
        </label>
    <!-- Wrap the table in a div with the "table-container" class -->
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Dialer ID</th>
                    <th>Agent Email</th>
                    <th>Beneficiary</th>
                    <th>Plan</th>
                    <th>Zip Code</th>
                    <th>Phone</th>
                    <th>Age</th>
                    <th>State</th>
                    <th>City</th>
                    <th>Spouse Age</th>
                    <th>Smoker</th>
                    <th>Color/Hobby</th>
                    <th>Licensed Agent Name</th>
                    <th>Call Back Time</th>
                    <th>Date</th>
                    <th>Created At</th>
                    <th>Select</th>
                </tr>
            </thead>
            <tbody>
                @if ($dailyLeads !== null && count($dailyLeads) > 0)
                @foreach ($dailyLeads as $lead)
                    <tr>
                        <td>{{ $lead->id }}</td>
                        <td>{{ $lead->subject }}</td>
                        <td>
                            @foreach ($lead->users as $user)
                                {{ $user->email }}<br>
                            @endforeach
                        </td>
                        <td>{{ $lead->beneficiary }}</td>
                        <td>{{ $lead->plan }}</td>
                        <td>{{ $lead->zip_code }}</td>
                        <td>{{ $lead->phone }}</td>
                        <td>{{ $lead->age }}</td>
                        <td>{{ $lead->state }}</td>
                        <td>{{ $lead->city }}</td>
                        <td>{{ $lead->spouse_age }}</td>
                        <td>{{ $lead->smoker }}</td>
                        <td>{{ $lead->color_hobby }}</td>
                        <td>{{ $lead->licensed_agent_name }}</td>
                        <td>{{ $lead->call_back_time }}</td>
                        
                        <td>{{ $lead->created_at->format('Y-m-d') }}</td>
                        <td>{{ $lead->created_at }}</td>
                        <td>
                            <input type="checkbox" name="selected_leads[]" value="{{ $lead->id }}">
                        </td>
                    </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="17">No leads available.</td>
                </tr>
                @endif

            </tbody>
        </table>
    </div> <!-- End of table-container div -->

    <!-- Display pagination links -->
    {{-- {{ $dailyLeads->withQueryString()->links() }} --}}

    <!-- Display pagination links -->
    {{-- <div class="row"></div>
    @if ($dailyLeads !== null && count($dailyLeads) > 0)
        {{ $dailyLeads->withQueryString()->links() }}
    @endif
</div> --}}
<nav aria-label="...">
    <ul class="pagination justify-content-center">
        <li class="page-item">
            <a class="page-link" href="{{ route('leads.daily', ['page' => $currentPage - 1]) }}">Previous</a>
        </li>
        <li class="page-item"><a class="page-link" href="{{ route('leads.daily') }}?page=1">1</a></li>
        <li class="page-item"><a class="page-link" href="{{ route('leads.daily') }}?page=2">2</a></li>
        <li class="page-item"><a class="page-link" href="{{ route('leads.daily') }}?page=3">3</a></li>
        <!-- Add more page items as needed -->
        <li class="page-item">
            <a class="page-link" href="{{ route('leads.daily', ['page' => $currentPage + 1]) }}">Next</a>
        </li>
        
    </ul>
</nav>

   

    <!-- Export Selected Leads Button -->
    <div style="text-align: right; margin-top: 10px;">
        <button type="button" class="btn btn-info" onclick="exportSelectedLeads()">Export Selected Leads</button>
    </div>
</form>
@endsection


</body>
</html>


