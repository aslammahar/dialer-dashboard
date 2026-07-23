@extends('layouts.admin')

@section('page-title')
    {{ __('Department Support Setup') }}
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card mt-2">
                <div class="card-body">

                    {{-- Success Message --}}
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    {{-- Form --}}
                    <form
                        action="{{ isset($editSupport) ? route('department_support.update', $editSupport->id) : route('department_support.store') }}"
                        method="POST">
                        @csrf
                        @if(isset($editSupport))
                            @method('PUT')
                        @endif

                        <div class="row align-items-end">

                            {{-- Title --}}

                            <div class="col-md-3">
                                <label class="form-label">{{ __('Title') }}</label>
                                <select name="title" class="form-control" required>
                                    <option value="">Select Title</option>
                                    <optgroup label="📋 HR Support">
                                        <option>🏖️ Leave Request / Leave Issue</option>
                                        <option>💰 Salary or Payment Issue</option>
                                        <option>⏰ Attendance Issue</option>
                                        <option>👤 Profile / Personal Information Update</option>
                                        <option>📅 Shift Timing or Schedule Issue</option>
                                        <option>🧾 Payslip or Deduction Inquiry</option>
                                        <option>🚫 Disciplinary / Warning Clarification</option>
                                        <option>🪪 ID Card or Access Issue</option>
                                        <option>🏢 Workplace / Environment Complaint</option>
                                        <option>🎓 Training & Development Request</option>
                                        <option>🧳 Travel / Expense Claim</option>
                                        <option>🧑‍💻 Technical or Portal Access Issue (HRMS Login etc.)</option>
                                    </optgroup>

                                    <optgroup label="💻 IT Support">
                                        <option>🖥️ System / Computer Not Working</option>
                                        <option>🌐 Internet / Network Connectivity Issue</option>
                                        <option>🔑 Login / Password Reset Request</option>
                                        <option>🧩 Software Installation / Update Request</option>
                                        <option>🖨️ Printer / Scanner Issue</option>
                                        <option>📨 Email Access / Outlook Issue</option>
                                        <option>🛠️ Hardware Maintenance / Replacement</option>
                                        <option>🔒 System Security / Antivirus Issue</option>
                                        <option>💾 Data Backup / Recovery Request</option>
                                        <option>🧑‍💻 ERP / HRMS / Internal Portal Issue</option>
                                        <option>🖲️ Peripheral Device Not Working (Mouse, Keyboard, etc.)</option>
                                        <option>📱 Mobile / Tablet Configuration Issue</option>
                                        <option>🕒 Slow System / Performance Issue</option>
                                        <option>🔐 Access Rights / Permission Issue</option>
                                        <option>🧠 Technical Guidance / Support Needed</option>
                                    </optgroup>

                                    <optgroup label="🧾 QA Support">
                                        <option>⚠️ Product Quality Issue / Defect Report</option>
                                        <option>🧪 Testing Environment / Server Issue</option>
                                        <option>🧰 Test Case / Test Data Request</option>
                                        <option>🐞 Bug Tracking / Reporting Assistance</option>
                                        <option>🧠 Process Deviation / SOP Violation Report</option>
                                        <option>📊 Quality Audit Observation / Finding</option>
                                        <option>🧴 Material / Component Quality Concern</option>
                                        <option>🔍 Inspection Report Query / Correction Request</option>
                                        <option>📋 Documentation / Record Verification Issue</option>
                                        <option>🏭 Production Line Quality Issue</option>
                                        <option>💬 Customer Complaint Follow-up</option>
                                        <option>🧾 Non-Conformance Report (NCR) Submission</option>
                                        <option>🧱 Rework / Re-inspection Request</option>
                                        <option>🧭 QA Policy or Standard Clarification</option>
                                        <option>📈 Continuous Improvement Suggestion / CAPA Request</option>
                                    </optgroup>
                                </select>

                            </div>

                            {{-- Subject --}}
                            <div class="col-md-3">
                                <label class="form-label">{{ __('Subject') }}</label>
                                <input type="text" name="subject" class="form-control" placeholder="Enter subject"
                                    value="{{ $editSupport->subject ?? '' }}" required>
                            </div>

                            {{-- Description --}}
                            <div class="col-md-4">
                                <label class="form-label">{{ __('Description') }}</label>
                                <textarea name="description" class="form-control" rows="2"
                                    placeholder="Enter description">{{ $editSupport->description ?? '' }}</textarea>
                            </div>
                            {{-- Role --}}
                            <div class="col-md-3">
                                <label class="form-label">{{ __('Role') }}</label>
                                <select name="role_id" id="role" class="form-control" required>
                                    <option value="">{{ __('Select Role') }}</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ isset($editSupport) && $editSupport->role_id == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Users --}}
                            <div class="col-md-3">
                                <label class="form-label">{{ __('Users') }}</label>

                                <select name="user_id[]" id="users" class="form-control" multiple required>
                                    <option value="">{{ __('Select Role first') }}</option>
                                </select>
                            </div>

                            {{-- Submit --}}
                            <div class="col-md-2 mt-3">
                                <button type="submit" class="btn btn-primary w-100">
                                    {{ isset($editSupport) ? __('Update') : __('Save') }}
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>

            {{-- Table --}}
            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="mb-3">{{ __('Existing Department Supports') }}</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Subject</th>
                                <th>Description</th>
                                <th>Role</th>
                                <th>Users</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($supports as $support)
                                <tr>
                                    <td>{{ $support->title }}</td>
                                    <td>{{ $support->subject ?? '—' }}</td>
                                    <td>{{ Str::limit($support->description, 50) ?? '—' }}</td>
                                    <td>{{ $support->role->name ?? 'N/A' }}</td>
                                    {{-- <td>
                                        @php
                                        // Ensure user_id is always an array
                                        $userIds = is_array($support->user_id) ? $support->user_id :
                                        json_decode($support->user_id, true);
                                        if (!is_array($userIds)) {
                                        $userIds = [$userIds];
                                        }
                                        @endphp
                                        @foreach($userIds as $uid)
                                        @php $user = \App\Models\User::find($uid); @endphp
                                        <span class="badge bg-info">{{ $user->name ?? 'N/A' }}</span>
                                        @endforeach
                                    </td> --}}

                                    <td>
                                        @foreach($support->users as $user)
                                            <span class="badge bg-info">{{ $user->name }}</span>
                                        @endforeach
                                    </td>

                                    <td>
                                        <a href="{{ route('department_support.edit', $support->id) }}"
                                            class="btn btn-sm btn-info">Edit</a>
                                        <form action="{{ route('department_support.destroy', $support->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No records found</td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- AJAX --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function () {
            let selectedUsers = @json(isset($editSupport) ? $editSupport->user_id : []);

            function loadUsers(roleId) {
                if (roleId) {
                    $.ajax({
                        url: "/get-support/" + roleId,
                        type: "GET",
                        success: function (users) {
                            $('#users').empty();
                            if (users.length > 0) {
                                $.each(users, function (i, user) {
                                    let selected = selectedUsers.includes(user.id) ? 'selected' : '';
                                    $('#users').append('<option value="' + user.id + '" ' + selected + '>' + user.name + '</option>');
                                });
                            } else {
                                $('#users').append('<option value="">No users found</option>');
                            }
                        }
                    });
                } else {
                    $('#users').empty().append('<option value="">Select Role first</option>');
                }
            }

            $('#role').on('change', function () {
                loadUsers($(this).val());
            });

            // If editing existing support
            @if(isset($editSupport))
                loadUsers({{ $editSupport->role_id }});
            @endif
        });
    </script>
@endsection