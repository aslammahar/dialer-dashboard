@extends('layouts.admin')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
<li class="breadcrumb-item">{{__('HRM')}}</li>

@endsection

@section('content')
<br>

<!-- css starts here  -->
<style>
    .table-responsive {
        margin-top: 20px;
    }

    .table th,
    .table td {
        vertical-align: middle;
    }

    .audio-controls {
        width: 100%;
    }

    .btn-update {
        width: 100%;
    }
</style>

<!-- css ends  here  -->

<!-- <a href="daily-leads"> <u> Voice Leads </u> </a><br>
<a href="avatar-leads"><u> Avatar Leads </u></a><br>
<a href="avatar-calls"><u> Manage Avatar Calls </u></a><br>
<td>
    <a href="avatar-q-a-leads"><u> Edit Avatar Leads </u> </a>
</td> -->

<div class="row">
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mb-/3 mb-sm-0">
                        <div class="d-flex align-items-center">
                            <div class="theme-avtar bg-success">
                                <i class="ti ti-cast"></i>
                            </div>
                            <div class="ms-3">
                                <small class="text-muted">{{ __('Assign Leads') }} </small>
                                <li><a href="avatar-q-a-leads" class="btn btn-sm btn-success">Assign Leads</a></li>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto text-end">
                        <h3 class="m-0"> </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mb-/3 mb-sm-0">
                        <div class="d-flex align-items-center">
                            <div class="theme-avtar bg-danger">
                                <i class="ti ti-cast"></i>
                            </div>
                            <div class="ms-3">
                                <small class="text-muted">{{ __('MY Leads') }} </small>
                                <li><a href="avatar-calls" class="btn btn-sm btn-danger">My Leads</a></li>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto text-end">
                        <h3 class="m-0"> </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mb-/3 mb-sm-0">
                        <div class="d-flex align-items-center">
                            <div class="theme-avtar bg-warning">
                                <i class="ti ti-cast"></i>
                            </div>
                            <div class="ms-3">
                                <small class="text-muted">{{ __('Agent Stats') }} </small>
                                <li><a href="avatar-leads_qa-stats" class="btn btn-sm btn-warning">Agent Stats</a></li>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto text-end">
                        <h3 class="m-0"> </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mb-/3 mb-sm-0">
                        <div class="d-flex align-items-center">
                            <div class="theme-avtar bg-secondary">
                                <i class="ti ti-cast"></i>
                            </div>
                            <div class="ms-3">
                                <small class="text-muted">{{ __('Re Qa Leads') }} </small>
                                <li><a href="search-lead" class="btn btn-sm btn-secondary">Re Qa Leads</a></li>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto text-end">
                        <h3 class="m-0"> </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
   


</div>


<br>
<div class="container mt-4">
    <a href="{{ route('lead.index') }}" class="btn btn-primary">
        <i class="bi bi-search"></i> Search Leads
    </a>
</div>


<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{ __('Updated Leads') }}</h4>
                <div class="table-responsive">

                    <!-- ⚡ PERFORMANCE: Removed datatable class - using Laravel pagination instead for better performance -->
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Lead ID</th>

                                <th>Dialer Id</th>
                                <th>Recording</th>

                                <th>QA Date</th>

                                <th>QA Status and Comments</th>
                                <!-- <th>Count</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($avatarLeads as $avatarLead)
                            <tr>
                                <td>{{ $avatarLead->id }}</td>
                                <td>{{ $avatarLead->lead_id }}</td>

                                <td>{{ $avatarLead->dialer_id }}</td>
                                <td>
                                    <audio controls>
                                        @php
                                            // Handle both full URLs and relative paths
                                            $audioSrc = $avatarLead->recording_link ?? '';
                                            if (!empty($audioSrc)) {
                                                // If it's a relative path starting with /storage/, convert to full URL
                                                if (str_starts_with($audioSrc, '/storage/')) {
                                                    $audioSrc = asset($audioSrc);
                                                }
                                                // If it's already a full URL (http:// or https://), use as-is
                                            }
                                        @endphp
                                        @if(!empty($audioSrc))
                                            <source src="{{ $audioSrc }}" type="audio/mpeg">
                                        @endif
                                        Your browser does not support the audio element.
                                    </audio>
                                </td>


                                <td>{{ $avatarLead->updated_at }}</td>

                                <td>
                                    <form action="{{ route('avatarLeads.update', $avatarLead->id) }}" method="POST" class="d-flex">
                                        @csrf
                                        @method('PUT')
                                        <div class="form-group mr-4">
                                            <select name="QAstatus" id="QAstatus" class="form-control" style="padding-right:50px;">
                                                <option value="approved" {{ $avatarLead->QAstatus == 'approved' ? 'selected' : '' }}>Approved</option>
                                                <option value="rejected" {{ $avatarLead->QAstatus == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                                <option value="pending" {{ $avatarLead->QAstatus == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="on review" {{ $avatarLead->QAstatus == 'on review' ? 'selected' : '' }}>On Review</option>
                                                <option value="no recording" {{ $avatarLead->QAstatus == 'no recording' ? 'selected' : '' }}>No Recording</option>
                                            </select>
                                        </div>
                                        <div class="form-group mr-4">
                                            <input type="text" name="Qacomments" value="{{ $avatarLead->Qacomments }}" id="Qacomments" class="form-control" style="padding-right:50px;">
                                        </div>

                                        <button type="submit" class="btn btn-primary" style="margin-left: 60px;">Update</button>
                                    </form>

                                 

                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- ⚡ PERFORMANCE: Add pagination links -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $avatarLeads->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>






<!-- import starts here -->

<!-- <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Voice Qa Leads - Excel Import') }}</div>

                <div class="card-body">
                    <form action="{{ route('import.excel') }}" method="POST" enctype="multipart/form-data" class="file-upload">
                        @csrf
                        <div class="form-group">
                            <label for="file">{{ __('Select Excel File to Import') }}:</label>
                            <input type="file" id="file" name="file" accept=".xlsx, .xls" class="form-control-file">
                        </div>
                        <button type="submit" class="btn btn-primary">{{ __('Import Excel') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>




    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Avatar Qa Leads - Excel Import') }}</div>

                <div class="card-body">
                    <form action="{{ route('import.avatar-excel') }}" method="POST" enctype="multipart/form-data" class="file-upload">
                        @csrf
                        <div class="form-group">
                            <label for="file">{{ __('Select Excel File to Import') }}:</label>
                            <input type="file" id="file" name="file" accept=".xlsx, .xls" class="form-control-file">
                        </div>
                        <button type="submit" class="btn btn-primary">{{ __('Import Excel') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> -->

<!-- import ends here -->




@endsection