<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" integrity="sha512-jnSuA4Ss2PkkikSOLtYs8BlYIeeIK1h99ty4YfvRPAlzr377vr3CXDb7sb7eEEBYjDtcYj+AjBH3FLv5uSJuXg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <title>QA Section</title>

    <style>
        body {
            overflow-x: hidden;
            margin: 0;
            padding: 0;
        }

        .custom-div {
            margin: 0 20px;
        }

        .div1 {
            height: calc(100vh - 120px);
            width: calc(105vw - 150px);
            overflow: scroll;
        }

        .div1 table {
            border-spacing: 0;
        }

        .div1 th {
            border-left: none;
            border-right: 1px solid #bbbbbb;
            padding: 5px;
            width: 80px;
            min-width: 80px;
            position: sticky;
            top: 0;
            background: #727272;
            color: #e0e0e0;
            font-weight: Medium;
        }

        .div1 td {
            border-left: none;
            border-right: 1px solid #bbbbbb;
            border-bottom: 1px solid #bbbbbb;
            padding: 5px;
            width: 80px;
            min-width: 80px;
        }

        .div1 th:nth-child(1),
        .div1 td:nth-child(1) {
            position: sticky;
            left: 0;
            width: 50px;
            min-width: 50px;
        }

        .div1 th:nth-child(2),
        .div1 td:nth-child(2) {
            position: sticky;
            left: 57px;
            width: 50px;
            min-width: 50px;
        }

        .div1 th:nth-child(3),
        .div1 td:nth-child(3) {
            position: sticky;
            left: 125px;
            width: 50px;
            min-width: 50px;
        }

        .div1 th:nth-child(4),
        .div1 td:nth-child(4) {
            position: sticky;
            left: 215px;
            width: 50px;
            min-width: 50px;
        }

        .div1 th:nth-child(5),
        .div1 td:nth-child(5) {
            position: sticky;
            left: 328px;
            width: 50px;
            min-width: 50px;
        }

        .div1 th:nth-child(6),
        .div1 td:nth-child(6) {
            position: sticky;
            left: 405px;
            width: 50px;
            min-width: 50px;
        }

        .div1 td:nth-child(1),
        .div1 td:nth-child(2),
        .div1 td:nth-child(3),
        .div1 td:nth-child(4),
        .div1 td:nth-child(5),
        .div1 td:nth-child(6) {
            background: #adcbd7;
        }

        .div1 th:nth-child(1),
        .div1 th:nth-child(2),
        .div1 th:nth-child(3),
        .div1 th:nth-child(4),
        .div1 th:nth-child(5),
        .div1 th:nth-child(6) {
            z-index: 2;
        }

        /* Table Styles */
        .table {
            width: 100%;
            border-spacing: 0;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .table thead th {
            text-align: left;
            padding: 10px;
            border: 1px solid #ddd;
        }

        .table tbody td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        .table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .table select {
            width: 100%;
            padding: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
            background-color: transparent;
            color: black;
            cursor: pointer;
        }

        .table textarea {
            width: 100%;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
            resize: vertical;
            height: 100px;
        }

        .table tbody tr:hover {
            background-color: #e0e0e0;
        }

        .table select[disabled] {
            color: #999;
        }

        .table select.status-approved {
            background-color: #66cc66;
        }

        .table select.status-rejected {
            background-color: #ff4d4d;
        }

        /* ★ NEW: Billable Duration number input */
        .editable-num {
            width: 75px;
            padding: 4px 6px;
            border-radius: 5px;
            border: 1px solid #ccc;
            text-align: right;
            font-size: 13px;
            color: black;
            background-color: #f8fafc;
        }

        .editable-num:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 2px rgba(37,99,235,.15);
            background: #fff;
        }
    </style>

</head>

<body>

    @section('page-title')
    {{ __('All Avatar Leads') }}
    @endsection

    <br>

    <div class="row">
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
                                    <small class="text-muted">{{ __('QA section') }} </small>
                                    <li><a href="qa-section" class="btn btn-sm btn-warning">QA section</a></li>
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

    <div class="custom-div">
        <h1 class="text-center py-3">Avatar Qa</h1>

        <div>
            <div class="div1">
                <table class="table datatable table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>UID</th>
                            <th>Agent Name</th>
                            <th>Lead ID</th>
                            <th>Dialer ID</th>
                            <th>Verifier</th>
                            <th>Recording</th>
                            <th>Recording Link</th>
                            <th>Greeting</th>
                            <th>Pitch</th>
                            <th>Age</th>
                            <th>Smoker</th>
                            <th>Health</th>
                            <th>Beneficiary</th>
                            <th>Account</th>
                            <th>Plan</th>
                            <th>Transfer Details</th>
                            <th>Xfer Consent</th>
                            <th>Rebuttals</th>
                            <th>NOS Of Rebuttals</th>
                            <th>No of Refusals</th>
                            {{-- ★ NEW COLUMN --}}
                            <th>Billable Dur.</th>
                            <th>QA Status</th>
                            <th>Biling Status</th>
                            <th>Comments</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($avatarCalls as $avatarCall)
                        @php
                            $statusColor = $avatarCall->QAstatus === 'approved'     ? '#66cc66' :
                                          ($avatarCall->QAstatus === 'rejected'     ? '#ff4d4d' :
                                          ($avatarCall->QAstatus === 'on review'    ? '#ffa31a' :
                                          ($avatarCall->QAstatus === 'no recording' ? '#d279d2' :
                                          ($avatarCall->QAstatus === 'xfers'        ? '#818cf8' : '#f2f2f2')))); // xfers = purple

                            $IsgreetingsColor         = $avatarCall->Isgreetings        === 'Yes' ? '#66cc66' : ($avatarCall->Isgreetings        === 'No' ? '#ff4d4d' : '#f2f2f2');
                            $Ispitch_call_about_color = $avatarCall->Ispitch_call_about === 'Yes' ? '#66cc66' : ($avatarCall->Ispitch_call_about === 'No' ? '#ff4d4d' : '#f2f2f2');
                            $IsageColor               = $avatarCall->Isage              === 'Yes' ? '#66cc66' : ($avatarCall->Isage              === 'No' ? '#ff4d4d' : '#f2f2f2');
                            $IssmokerColor            = $avatarCall->Issmoker           === 'Yes' ? '#66cc66' : ($avatarCall->Issmoker           === 'No' ? '#ff4d4d' : '#f2f2f2');
                            $Ishealth1Color           = $avatarCall->Ishealth1          === 'Yes' ? '#66cc66' : ($avatarCall->Ishealth1          === 'No' ? '#ff4d4d' : '#f2f2f2');
                            $IsbeneficiaryColor       = $avatarCall->Isbeneficiary      === 'Yes' ? '#66cc66' : ($avatarCall->Isbeneficiary      === 'No' ? '#ff4d4d' : '#f2f2f2');
                            $IsaccountColor           = $avatarCall->Isaccount          === 'Yes' ? '#66cc66' : ($avatarCall->Isaccount          === 'No' ? '#ff4d4d' : '#f2f2f2');
                            $IsplanColor              = $avatarCall->Isplan             === 'Yes' ? '#66cc66' : ($avatarCall->Isplan             === 'No' ? '#ff4d4d' : '#f2f2f2');
                            $Istransfer_details_color = $avatarCall->Istransfer_details === 'Yes' ? '#66cc66' : ($avatarCall->Istransfer_details === 'No' ? '#ff4d4d' : '#f2f2f2');
                            $Isxfer_consent_color     = $avatarCall->Isxfer_consent     === 'Yes' ? '#66cc66' : ($avatarCall->Isxfer_consent     === 'No' ? '#ff4d4d' : '#f2f2f2');
                            $rebuttalsColor           = $avatarCall->rebuttals          === 'Yes' ? '#66cc66' : ($avatarCall->rebuttals          === 'No' ? '#ff4d4d' : '#f2f2f2');

                            $audioSrc = $avatarCall->recording_link ?? '';
                            if (!empty($audioSrc) && str_starts_with($audioSrc, '/storage/')) {
                                $audioSrc = asset($audioSrc);
                            }
                        @endphp
                        <tr>
                            <td>{{ $avatarCall->id }}</td>
                            <td>{{ $avatarCall->agent_name }}</td>
                            <td>{{ $avatarCall->lead_id }}</td>
                            <td>{{ $avatarCall->dialer_id }}</td>
                            <td>{{ $avatarCall->verifier }}</td>

                            <td>
                                <audio controls preload="auto">
                                    @if(!empty($audioSrc))
                                        <source src="{{ $audioSrc }}" type="audio/mpeg">
                                    @endif
                                </audio>
                            </td>

                            <td><a href="{{ $avatarCall->recording }}" target="_blank">{{ $avatarCall->recording }}</a></td>

                            <td><select style="background-color: {{ $IsgreetingsColor }}; color: black;" class="editable" data-name="Isgreetings" data-pk="{{ $avatarCall->id }}">
                                    <option value="Yes"  {{ $avatarCall->Isgreetings == 'Yes'  ? 'selected' : '' }}>Yes</option>
                                    <option value="No"   {{ $avatarCall->Isgreetings == 'No'   ? 'selected' : '' }}>No</option>
                                    <option value="null" {{ $avatarCall->Isgreetings === null   ? 'selected' : '' }}>Select</option>
                                </select></td>

                            <td><select style="background-color: {{ $Ispitch_call_about_color }}; color: black;" class="editable" data-name="Ispitch_call_about" data-pk="{{ $avatarCall->id }}">
                                    <option value="Yes"  {{ $avatarCall->Ispitch_call_about == 'Yes'  ? 'selected' : '' }}>Yes</option>
                                    <option value="No"   {{ $avatarCall->Ispitch_call_about == 'No'   ? 'selected' : '' }}>No</option>
                                    <option value="null" {{ $avatarCall->Ispitch_call_about === null   ? 'selected' : '' }}>Select</option>
                                </select></td>

                            <td><select style="background-color: {{ $IsageColor }}; color: black;" class="editable" data-name="Isage" data-pk="{{ $avatarCall->id }}">
                                    <option value="Yes"  {{ $avatarCall->Isage == 'Yes'  ? 'selected' : '' }}>Yes</option>
                                    <option value="No"   {{ $avatarCall->Isage == 'No'   ? 'selected' : '' }}>No</option>
                                    <option value="null" {{ $avatarCall->Isage === null   ? 'selected' : '' }}>Select</option>
                                </select></td>

                            <td><select style="background-color: {{ $IssmokerColor }}; color: black;" class="editable" data-name="Issmoker" data-pk="{{ $avatarCall->id }}">
                                    <option value="Yes"  {{ $avatarCall->Issmoker == 'Yes'  ? 'selected' : '' }}>Yes</option>
                                    <option value="No"   {{ $avatarCall->Issmoker == 'No'   ? 'selected' : '' }}>No</option>
                                    <option value="null" {{ $avatarCall->Issmoker === null   ? 'selected' : '' }}>Select</option>
                                </select></td>

                            <td><select style="background-color: {{ $Ishealth1Color }}; color: black;" class="editable" data-name="Ishealth1" data-pk="{{ $avatarCall->id }}">
                                    <option value="Yes"  {{ $avatarCall->Ishealth1 == 'Yes'  ? 'selected' : '' }}>Yes</option>
                                    <option value="No"   {{ $avatarCall->Ishealth1 == 'No'   ? 'selected' : '' }}>No</option>
                                    <option value="null" {{ $avatarCall->Ishealth1 === null   ? 'selected' : '' }}>Select</option>
                                </select></td>

                            <td><select style="background-color: {{ $IsbeneficiaryColor }}; color: black;" class="editable" data-name="Isbeneficiary" data-pk="{{ $avatarCall->id }}">
                                    <option value="Yes"  {{ $avatarCall->Isbeneficiary == 'Yes'  ? 'selected' : '' }}>Yes</option>
                                    <option value="No"   {{ $avatarCall->Isbeneficiary == 'No'   ? 'selected' : '' }}>No</option>
                                    <option value="null" {{ $avatarCall->Isbeneficiary === null   ? 'selected' : '' }}>Select</option>
                                </select></td>

                            <td><select style="background-color: {{ $IsaccountColor }}; color: black;" class="editable" data-name="Isaccount" data-pk="{{ $avatarCall->id }}">
                                    <option value="Yes"  {{ $avatarCall->Isaccount == 'Yes'  ? 'selected' : '' }}>Yes</option>
                                    <option value="No"   {{ $avatarCall->Isaccount == 'No'   ? 'selected' : '' }}>No</option>
                                    <option value="null" {{ $avatarCall->Isaccount === null   ? 'selected' : '' }}>Select</option>
                                </select></td>

                            <td><select style="background-color: {{ $IsplanColor }}; color: black;" class="editable" data-name="Isplan" data-pk="{{ $avatarCall->id }}">
                                    <option value="Yes"  {{ $avatarCall->Isplan == 'Yes'  ? 'selected' : '' }}>Yes</option>
                                    <option value="No"   {{ $avatarCall->Isplan == 'No'   ? 'selected' : '' }}>No</option>
                                    <option value="null" {{ $avatarCall->Isplan === null   ? 'selected' : '' }}>Select</option>
                                </select></td>

                            <td><select style="background-color: {{ $Istransfer_details_color }}; color: black;" class="editable" data-name="Istransfer_details" data-pk="{{ $avatarCall->id }}">
                                    <option value="Yes"  {{ $avatarCall->Istransfer_details == 'Yes'  ? 'selected' : '' }}>Yes</option>
                                    <option value="No"   {{ $avatarCall->Istransfer_details == 'No'   ? 'selected' : '' }}>No</option>
                                    <option value="null" {{ $avatarCall->Istransfer_details === null   ? 'selected' : '' }}>Select</option>
                                </select></td>

                            <td><select style="background-color: {{ $Isxfer_consent_color }}; color: black;" class="editable" data-name="Isxfer_consent" data-pk="{{ $avatarCall->id }}">
                                    <option value="Yes"  {{ $avatarCall->Isxfer_consent == 'Yes'  ? 'selected' : '' }}>Yes</option>
                                    <option value="No"   {{ $avatarCall->Isxfer_consent == 'No'   ? 'selected' : '' }}>No</option>
                                    <option value="null" {{ $avatarCall->Isxfer_consent === null   ? 'selected' : '' }}>Select</option>
                                </select></td>

                            <td><select style="background-color: {{ $rebuttalsColor }}; color: black;" class="editable" data-name="rebuttals" data-pk="{{ $avatarCall->id }}">
                                    <option value="Yes"  {{ $avatarCall->rebuttals == 'Yes'  ? 'selected' : '' }}>Yes</option>
                                    <option value="No"   {{ $avatarCall->rebuttals == 'No'   ? 'selected' : '' }}>No</option>
                                    <option value="null" {{ $avatarCall->rebuttals === null   ? 'selected' : '' }}>Select</option>
                                </select></td>

                            <td><select class="editable" data-name="use_of_rebuttals" data-pk="{{ $avatarCall->id }}">
                                    <option value="0"    {{ $avatarCall->use_of_rebuttals == '0'    ? 'selected' : '' }}>0</option>
                                    <option value="1"    {{ $avatarCall->use_of_rebuttals == '1'    ? 'selected' : '' }}>1</option>
                                    <option value="2"    {{ $avatarCall->use_of_rebuttals == '2'    ? 'selected' : '' }}>2</option>
                                    <option value="more" {{ $avatarCall->use_of_rebuttals == 'more' ? 'selected' : '' }}>More</option>
                                    <option value="null" {{ $avatarCall->use_of_rebuttals === null  ? 'selected' : '' }}>Select</option>
                                </select></td>

                            <td><select class="editable" data-name="no_of_refusals" data-pk="{{ $avatarCall->id }}">
                                    <option value="0"    {{ $avatarCall->no_of_refusals == '0'    ? 'selected' : '' }}>0</option>
                                    <option value="1"    {{ $avatarCall->no_of_refusals == '1'    ? 'selected' : '' }}>1</option>
                                    <option value="2"    {{ $avatarCall->no_of_refusals == '2'    ? 'selected' : '' }}>2</option>
                                    <option value="more" {{ $avatarCall->no_of_refusals == 'more' ? 'selected' : '' }}>More</option>
                                    <option value="null" {{ $avatarCall->no_of_refusals === null  ? 'selected' : '' }}>Select</option>
                                </select></td>

                            {{-- ★ NEW: Billable Duration --}}
                            <td>
                                <input type="number"
                                       class="editable-num"
                                       data-name="billable_duration"
                                       data-pk="{{ $avatarCall->id }}"
                                       value="{{ $avatarCall->billable_duration ?? '' }}"
                                       min="0"
                                       placeholder="0">
                            </td>

                            {{-- QA Status — old options + ★ NEW Xfers --}}
                            <td><select style="background-color: {{ $statusColor }}; color: black;" class="editable" data-name="QAstatus" data-pk="{{ $avatarCall->id }}">
                                    <option value="approved"     {{ $avatarCall->QAstatus == 'approved'     ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected"     {{ $avatarCall->QAstatus == 'rejected'     ? 'selected' : '' }}>Rejected</option>
                                    <option value="pending"      {{ $avatarCall->QAstatus === 'pending'     ? 'selected' : '' }}>Pending</option>
                                    <option value="on review"    {{ $avatarCall->QAstatus === 'on review'   ? 'selected' : '' }}>On Review</option>
                                    <option value="no recording" {{ $avatarCall->QAstatus === 'no recording'? 'selected' : '' }}>No Recording</option>
                                    {{-- ★ NEW --}}
                                    <option value="xfers"        {{ $avatarCall->QAstatus === 'xfers'       ? 'selected' : '' }}>Xfers</option>
                                </select>
                            </td>

                            <td><select style="color: black;" class="editable" data-name="billing_status" data-pk="{{ $avatarCall->id }}">
                                    <option value="Yes"  {{ $avatarCall->billing_status == 'Yes'  ? 'selected' : '' }}>Yes</option>
                                    <option value="No"   {{ $avatarCall->billing_status == 'No'   ? 'selected' : '' }}>No</option>
                                    <option value="null" {{ $avatarCall->billing_status === null   ? 'selected' : '' }}>Select</option>
                                </select></td>

                            <td><textarea class="editable" data-name="Qacomments" data-pk="{{ $avatarCall->id }}">{{ $avatarCall->Qacomments }}</textarea></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    {{-- ── SELECT: exact same JS as old code + xfers colour added ── --}}
    <script>
        $(document).ready(function() {
            $('.editable').change(function() {
                var value = $(this).val();
                var pk    = $(this).data('pk');
                var name  = $(this).data('name');
                var page  = $('.datatable').DataTable().page.info().page + 1;
                var entriesPerPage = $('.datatable').DataTable().page.info().length;

                // Change background color based on selected value
                if (value === 'approved') {
                    $(this).css({ 'background-color': '#66cc66', 'color': 'black' });
                } else if (value === 'rejected') {
                    $(this).css({ 'background-color': '#ff4d4d', 'color': 'black' });
                } else if (value === 'on review') {
                    $(this).css({ 'background-color': '#ffa31a', 'color': 'black' });
                } else if (value === 'no recording') {
                    $(this).css({ 'background-color': '#d279d2', 'color': 'black' });
                } else if (value === 'xfers') {                          // ★ NEW
                    $(this).css({ 'background-color': '#818cf8', 'color': 'black' });
                } else if (value === 'Yes') {
                    $(this).css({ 'background-color': '#66cc66', 'color': 'black' });
                } else if (value === 'No') {
                    $(this).css({ 'background-color': '#ff4d4d', 'color': 'black' });
                } else {
                    $(this).css({ 'background-color': '#f2f2f2', 'color': 'black' });
                }

                $.ajax({
                    url: "/category/update",
                    method: "POST",
                    data: {
                        pk: pk,
                        name: name,
                        value: value,
                        page: page,
                        entriesPerPage: entriesPerPage,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        alert('Failed to update data. Please check your connection.');
                    }
                });
            });
        });
    </script>

    {{-- ★ NEW: Billable Duration — save on blur or Enter --}}
    <script>
        $(document).ready(function() {
            function saveBillable($el) {
                $.ajax({
                    url: "/category/update",
                    method: "POST",
                    data: {
                        pk:    $el.data('pk'),
                        name:  $el.data('name'),
                        value: $el.val(),
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        alert('Failed to save billable duration. Please check your connection.');
                    }
                });
            }

            $(document).on('blur', '.editable-num', function() {
                saveBillable($(this));
            });

            $(document).on('keydown', '.editable-num', function(e) {
                if (e.key === 'Enter') $(this).blur();
            });
        });
    </script>

    {{-- DataTable init — exact same as old code --}}
    <script>
        $(document).ready(function() {
            $('.datatable').DataTable();
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

</body>

</html>