@forelse($closedCalls as $closedCall)
<tr>
    <td>{{ $closedCall->id }}</td>
    <td>
        @php
            // Convert UTC to Pakistan time (PKT - UTC+5)
            $pakistanTime = $closedCall->created_at->setTimezone('America/Los_Angeles');
        @endphp
        <div class="timestamp-info">
            <strong>{{ $pakistanTime->format('M d, Y') }}</strong><br>
            <small class="text-muted">{{ $pakistanTime->format('h:i A') }}</small><br>
            <small class="text-info">PST</small>
        </div>
    </td>

    <td>{{ $closedCall->customer_full_name }}</td>
    <td>
        {{ $closedCall->address }}<br>
        {{ $closedCall->city }}, {{ $closedCall->state }} {{ $closedCall->zip_code }}
    </td>
    <td>
        <strong>Age:</strong> {{ $closedCall->age }}<br>
        <strong>DOB:</strong> {{ $closedCall->dob ? $closedCall->dob->format('F j, Y') : 'N/A' }}<br>
        <strong>Smoker:</strong> {{ $closedCall->smoker }}<br>
        <strong>Gender:</strong> {{ $closedCall->gender }}<br>
        <strong>Marital Status:</strong> {{ $closedCall->martial_status }}<br>

        @can(abilities: 'show phone')
        <strong>Phone:</strong> {{ $closedCall->phone_number }}
        @endcan
    </td>
    <td>
        <strong>Height:</strong> {{ $closedCall->height }}<br>
        <strong>Weight:</strong> {{ $closedCall->weight }}<br>
        <strong>SSN:</strong> {{ $closedCall->social_security }}
    </td>
    <td>
        <strong>Health:</strong> {{ $closedCall->health_condition }}<br>
        <strong>Medications:</strong> {{ $closedCall->medication }}<br>
        <strong>Hospital:</strong> {{ $closedCall->hospital_name }}<br>
        <strong>Physician:</strong> {{ $closedCall->physician_name }}
    </td>
    <td>
        <strong>Premium:</strong> ${{ $closedCall->monthly_premium }}<br>
        <strong>Carrier:</strong> {{ $closedCall->carrier }}<br>
        <strong>Plan:</strong> {{ $closedCall->coverage_plan }}<br>
        <strong>Eligibility:</strong> {{ $closedCall->customer_eligibility }}
    </td>
    <td>
        <strong>Name:</strong> {{ $closedCall->beneficiary }}<br>
        <strong>Relation:</strong> {{ $closedCall->beneficiary_relation }}<br>
        @can(abilities: 'show phone')
        <strong>Phone:</strong> {{ $closedCall->beneficiary_phone }}<br>
        <strong>Alternative Phone:</strong> {{ $closedCall->beneficiary_phone }}<br>
        @endcan
        <strong>DOB:</strong> {{ $closedCall->beneficiary_dob ? $closedCall->beneficiary_dob->format('F j, Y') : 'N/A' }}
    </td>
    <td>
        <strong>Initial:</strong> {{ $closedCall->initial_draft_date ? $closedCall->initial_draft_date->format('F j, Y') : 'N/A' }}<br>
        <strong>Future:</strong> {{ $closedCall->future_draft_date ? $closedCall->future_draft_date->format('F j, Y') : 'N/A' }}
    </td>
    <td>{{ $closedCall->remarks }}</td>
    <td>{{ $closedCall->closer->name }}</td>
    <td>{{ $closedCall->juniorcloser->name ?? $closedCall->junior_closer_name }}</td>
    <td>{{ $closedCall->center_name }}</td>
    <td>{{ $closedCall->sale_made_by }}</td>
    <td>
        <span class="badge 
            @if($closedCall->status == 'approved') bg-success 
            @elseif($closedCall->status == 'pending') bg-warning
            @elseif($closedCall->status == 'rejected') bg-danger 
            @endif">
            {{ ucfirst($closedCall->status) }}
        </span>
    </td>
    <td>
        <a href="{{ route('outsource.show', $closedCall->id) }}" class="btn btn-primary btn-sm">View</a>
    </td>
</tr>
@empty
<tr>
    <td colspan="16" class="text-center">No records found.</td>
</tr>
@endforelse

<style>
.timestamp-info {
    min-width: 100px;
    text-align: center;
}

.timestamp-info strong {
    font-size: 0.9em;
    color: #2c3e50;
}

.timestamp-info small {
    font-size: 0.75em;
    display: block;
    margin-top: 2px;
}

.text-info {
    color: #17a2b8 !important;
    font-weight: bold;
}
</style>