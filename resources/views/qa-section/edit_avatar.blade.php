@extends('layouts.admin')

@section('content')

<!-- custome css -->
<style>
    .totals-row {
        background-color: #f0f0f0;
        /* Change to your desired background color */

    }

    .custom-table th {
        font-size: 1.9em;
        /* Slightly larger for header */
        text-align: center;
        font-weight: bold;
    }

    .custom-table td {
        font-size: 1.2em;
        text-align: center;
        font-weight: bold;
    }

    .totals-row td {
        font-weight: bold;
        color: #ff0000;
        /* You can specify any color you want using hexadecimal, RGB, or color names */
    }


    .totals-row th {
        font-weight: bold;
    }
</style>
<!-- custome css ends here -->

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
                                <small class="text-muted">{{ __('Agent Stats') }} </small>
                                <li><a href="avatar-leads_qa-stats" class="btn btn-sm btn-success">Agent Stats</a></li>
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
                            <div class="theme-avtar bg-info">
                                <i class="ti ti-cast"></i>
                            </div>
                            <div class="ms-3">
                                <small class="text-muted">{{ __('All Leads') }} </small>
                                <li><a href="shrink-leads" class="btn btn-sm btn-info">Shrinkage Leads</a></li>
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
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mb-/3 mb-sm-0">
                        <div class="d-flex align-items-center">
                            <div class="theme-avtar bg-info">
                                <i class="ti ti-cast"></i>
                            </div>
                            <div class="ms-3">
                                <small class="text-muted">{{ __('Upload Recordings') }} </small>
                                <li><a href="no-rec-leads" class="btn btn-sm btn-info">Upload Recordings</a></li>
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


    <!-- all the qa users with their total leads  -->
    <div class="container">
        <div class="row justify-content-center mt-4">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header" style="text-align: center; font-weight: bold;">
                        QA Users and Their Lead Status
                    </div>

                    <div class="card-body">
                        <table class="custom-table table table-bordered table-striped">
                            <thead>
                                <tr class="totals-row">
                                    <th style="font-size: 1.2em;">User Ids</th>
                                    <th style="font-size: 1.2em;">QA User</th>
                                    <th style="font-size: 1.2em;">Pending Leads</th>
                                    <th style="font-size: 1.2em;">Approved Leads</th>
                                    <th style="font-size: 1.2em;">Rejected Leads</th>
                                    <th style="font-size: 1.2em;">On Review</th>
                                    <th style="font-size: 1.2em;">No Recordings</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($qaLeadsStatus as $qaLeadStatus)
                                <tr>
                                    <td>{{ $qaLeadStatus['user']->id }}</td>
                                    <td>{{ $qaLeadStatus['user']->name }}</td>
                                    <td>{{ $qaLeadStatus['pending'] }}</td>
                                    <td>{{ $qaLeadStatus['approved'] }}</td>
                                    <td>{{ $qaLeadStatus['rejected'] }}</td>
                                    <td>{{ $qaLeadStatus['on review'] }}</td>
                                    <td>{{ $qaLeadStatus['no recording'] }}</td>
                                </tr>
                                @endforeach
                                <tr class="totals-row">
                                    <td><strong></strong></td>
                                    <td style="color: #ff0000;"><strong>Total:</strong></td>
                                    <td style="color: #ff0000;"><strong>{{ $totals['pending'] }}</strong></td>
                                    <td style="color: #ff0000;"><strong>{{ $totals['approved'] }}</strong></td>
                                    <td style="color: #ff0000;"><strong>{{ $totals['rejected'] }}</strong></td>
                                    <td style="color: #ff0000;"><strong>{{ $totals['on review'] }}</strong></td>
                                    <td style="color: #ff0000;"><strong>{{ $totals['no recording'] }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card mt-4">
                    <div class="card-header">ID Range</div>
                    <div class="card-body">
                        <div><strong>Starting ID:</strong> {{ $idRange['start'] }}</div>
                        <div><strong>Ending ID:</strong> {{ $idRange['end'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- all the qa users with their total leads ends here -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Assign Avatar Leads</div>
                    <div class="card-body">
                        <div>
                            <h6>ID Range</h6>
                        </div>
                        <div><strong>Starting ID:</strong> {{ $idRange['start'] }}</div>
                        <div><strong>Ending ID:</strong> {{ $idRange['end'] }}</div>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('update.qaperson') }}">
                            @csrf
                            <div class="form-group">
                                <label for="qaperson_id">Select QA Person:</label>
                                <select name="qaperson_id" id="qaperson_id" class="form-control" required>
                                    <!-- Populate dropdown with QA users -->
                                    <!-- Example: <option value="1">QA Person 1</option> -->
                                    @foreach($qaPersons as $qaPerson)
                                    <option value="{{ $qaPerson->id }}">{{ $qaPerson->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <hr class="my-3">
                            <h6 class="text-muted">Assign by ID range</h6>
                            <div class="form-group">
                                <label for="start_id">Enter Starting ID:</label>
                                <input type="number" name="start_id" id="start_id" class="form-control" value="{{ $idRange['start'] ?? '' }}" placeholder="Optional if using team">
                            </div>
                            <div class="form-group">
                                <label for="end_id">Enter Ending ID:</label>
                                <input type="number" name="end_id" id="end_id" class="form-control" value="{{ $idRange['end'] ?? '' }}" placeholder="Optional if using team">
                            </div>
                            <hr class="my-3">
                            <h6 class="text-muted">Or assign by team</h6>
                            <div class="form-group">
                                <label for="team_id">Select Team (Pending + Unassigned):</label>
                                <select name="team_id" id="team_id" class="form-control">
                                    <option value="">— Select team (optional) —</option>
                                    @php
                                        $teamsList = isset($teams) ? $teams : collect();
                                    @endphp
                                    @foreach($teamsList as $team)
                                        <option value="{{ $team->id }}">
                                            {{ $team->name }} ({{ (int) ($team->pending_unassigned_count ?? 0) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="lead_count">Total Leads to Assign (when using team):</label>
                                <input type="number" name="lead_count" id="lead_count" class="form-control" min="1" value="50" placeholder="Used only when team is selected">
                            </div>
                            <button type="submit" class="btn btn-primary">Assign Leads</button>
                        </form>
                    </div>
                </div>
            </div>



            <!-- re  Assign leads starts here  -->
           <div class="row justify-content-center mt-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Reassign Leads</div>
            <div class="card-body">
                <form method="POST" action="{{ route('reassign.leads') }}">
                    @csrf

                    {{-- Step 1: Who to take leads FROM --}}
                    <div class="form-group mb-3">
                        <label for="remove_qaperson_id"><strong>1. Remove leads from:</strong></label>
                        <select name="remove_qaperson_id" id="remove_qaperson_id" class="form-control" required>
                            <option value="">— Select person —</option>
                            @foreach($qaPersons as $qaPerson)
                                <option value="{{ $qaPerson->id }}">{{ $qaPerson->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Step 2: Filter by that person's team (loads via AJAX) --}}
                    {{-- NOTE: id is "reassign_team_id" — avoids conflict with assign form's "team_id" --}}
                    <div class="form-group mb-3">
                        <label for="reassign_team_id">
                            <strong>2. Filter by team:</strong>
                            <small class="text-muted">(optional — leave blank to take from all teams)</small>
                        </label>
                        <select name="reassign_team_id" id="reassign_team_id" class="form-control" disabled>
                            <option value="">— Select person first —</option>
                        </select>
                        <small id="team-loading" class="text-muted d-none">Loading teams...</small>
                    </div>

                    {{-- Step 3: How many leads --}}
                    <div class="form-group mb-3">
                        <label for="reassign_lead_count"><strong>3. Number of leads to reassign:</strong></label>
                        <input type="number" name="lead_count" id="reassign_lead_count"
                               class="form-control" required min="1" placeholder="e.g. 50">
                        <small id="team-max" class="text-muted d-none"></small>
                    </div>

                    {{-- Step 4: Who to assign TO --}}
                    <div class="form-group mb-3">
                        <label for="assign_qaperson_id"><strong>4. Assign to:</strong></label>
                        <select name="assign_qaperson_id" id="assign_qaperson_id" class="form-control" required>
                            <option value="">— Select person —</option>
                            @foreach($qaPersons as $qaPerson)
                                <option value="{{ $qaPerson->id }}">{{ $qaPerson->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Reassign Leads</button>
                </form>
            </div>
        </div>
    </div>
</div>


            <!-- re  Assign leads ends here  -->

        </div>




    </div>


    <script>
document.getElementById('remove_qaperson_id').addEventListener('change', function () {
    const personId = this.value;
    // Use reassign_team_id — NOT team_id (that belongs to the assign form above)
    const teamSel  = document.getElementById('reassign_team_id');
    const loading  = document.getElementById('team-loading');
    const teamMax  = document.getElementById('team-max');

    // Reset state
    teamSel.innerHTML = '<option value="">— All teams —</option>';
    teamSel.disabled  = true;
    teamMax.classList.add('d-none');

    if (!personId) return;

    loading.classList.remove('d-none');

    fetch(`/qa/person-teams?qaperson_id=${personId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(r => r.json())
    .then(teams => {
        loading.classList.add('d-none');

        if (!teams.length) {
            teamSel.innerHTML = '<option value="">No pending teams for this person</option>';
            teamSel.disabled  = true;
            return;
        }

        teamSel.innerHTML = '<option value="">— All teams —</option>';
        teams.forEach(t => {
            const opt       = document.createElement('option');
            opt.value       = t.team_id;
            opt.textContent = `${t.team_name} (${t.count} pending)`;
            opt.dataset.count = t.count;
            teamSel.appendChild(opt);
        });
        teamSel.disabled = false;
    })
    .catch(() => {
        loading.classList.add('d-none');
        teamSel.innerHTML = '<option value="">Error loading teams — try again</option>';
    });
});

// Show max available hint when team is selected
document.getElementById('reassign_team_id').addEventListener('change', function () {
    const teamMax  = document.getElementById('team-max');
    const selected = this.options[this.selectedIndex];
    if (selected && selected.dataset.count) {
        teamMax.textContent = `Max available: ${selected.dataset.count} leads`;
        teamMax.classList.remove('d-none');
    } else {
        teamMax.classList.add('d-none');
    }
});
</script>




    @endsection