<!-- Quick links -->
@if (\Auth::user()->type == 'voice')
<div class="row g-3 mb-4">
    @include('dashboard.partials.quick-link-card', [
        'href' => route('voice-section'),
        'title' => __('Voice Section'),
        'description' => __('Open voice workspace'),
        'icon' => 'ti ti-microphone',
        'color' => 'primary',
    ])
</div>
@elseif(\Auth::user()->type == 'Team Lead' || \Auth::user()->type == 'Director' || \Auth::user()->type == 'Dialer Support')
<div class="row g-3 mb-4">
    @include('dashboard.partials.quick-link-card', [
        'href' => route('search.index'),
        'title' => __('Search Stats'),
        'description' => __('Search agent stats'),
        'icon' => 'ti ti-search',
        'color' => 'primary',
    ])
    @if (in_array(\Auth::user()->type, ['Team Lead', 'Director'], true))
    @include('dashboard.partials.quick-link-card', [
        'href' => route('avatar-section'),
        'title' => __('Avatar Section'),
        'description' => __('Open avatar workspace'),
        'icon' => 'ti ti-user-circle',
        'color' => 'warning',
    ])
    @endif
    @include('dashboard.partials.quick-link-card', [
        'href' => route('team.index'),
        'title' => __('Teams Management'),
        'description' => __('Manage teams and agents'),
        'icon' => 'ti ti-users-group',
        'color' => 'secondary',
    ])
    @include('dashboard.partials.quick-link-card', [
        'href' => route('userscred.edit'),
        'title' => __('Update Dialer Ids'),
        'description' => __('Update user dialer credentials'),
        'icon' => 'ti ti-id',
        'color' => 'secondary',
    ])
    @include('dashboard.partials.quick-link-card', [
        'href' => route('attendance'),
        'title' => __('Attendance'),
        'description' => __('View and manage attendance'),
        'icon' => 'ti ti-calendar',
        'color' => 'danger',
    ])
    @include('dashboard.partials.quick-link-card', [
        'href' => route('employee.salary-slips'),
        'title' => __('Salary Slip'),
        'description' => __('View monthly salary slips'),
        'icon' => 'ti ti-receipt',
        'color' => 'success',
    ])
</div>
@elseif(\Auth::user()->type == 'Project Manager')
<div class="row g-3 mb-4">
    @include('dashboard.partials.quick-link-card', [
        'href' => route('closed_calls.index'),
        'title' => __('Manage Policies'),
        'description' => __('Closed calls and policies'),
        'icon' => 'ti ti-file-description',
        'color' => 'primary',
    ])
</div>
@endif

@if (in_array(auth()->user()->type, ['Voice', 'Avatar', 'vendor'], true))
<div class="row g-3 mb-4">
    @if (auth()->user()->type == 'Voice')
        @include('dashboard.partials.quick-link-card', [
            'href' => route('voice-section'),
            'title' => __('Voice Section'),
            'description' => __('Open voice workspace'),
            'icon' => 'ti ti-microphone',
            'color' => 'warning',
        ])
    @elseif (auth()->user()->type == 'Avatar')
        @include('dashboard.partials.quick-link-card', [
            'href' => route('avatar-section'),
            'title' => __('Avatar Section'),
            'description' => __('Open avatar workspace'),
            'icon' => 'ti ti-user-circle',
            'color' => 'warning',
        ])
    @elseif (auth()->user()->type == 'vendor')
        @include('dashboard.partials.quick-link-card', [
            'href' => route('getAuthVendorUserReport'),
            'title' => __('Vendor Reports'),
            'description' => __('View vendor reports'),
            'icon' => 'ti ti-building-store',
            'color' => 'warning',
        ])
    @endif
</div>
@endif

@if (
    \Auth::user()->type == 'QA' ||
    \Auth::user()->type == 'QA Manager' ||
    \Auth::user()->type == 'Director' ||
    \Auth::user()->type == 'Project Manager' ||
    \Auth::user()->type == 'Dialer Support')
<div class="row g-3 mb-4">
    @include('dashboard.partials.quick-link-card', [
        'href' => route('qa-section'),
        'title' => __('QA Section'),
        'description' => __('Quality assurance workspace'),
        'icon' => 'ti ti-checkbox',
        'color' => 'success',
    ])
    @include('dashboard.partials.quick-link-card', [
        'href' => url('/no-rec-leads'),
        'title' => __('Upload Recordings'),
        'description' => __('Leads without recordings'),
        'icon' => 'ti ti-upload',
        'color' => 'info',
    ])
    @include('dashboard.partials.quick-link-card', [
        'href' => route('dialer.stats'),
        'title' => __('Dialer Stats'),
        'description' => __('Dialer performance stats'),
        'icon' => 'ti ti-chart-bar',
        'color' => 'primary',
    ])
    @include('dashboard.partials.quick-link-card', [
        'href' => route('avatarleads'),
        'title' => __('Shrinkage Leads'),
        'description' => __('Shrinkage lead management'),
        'icon' => 'ti ti-list',
        'color' => 'info',
    ])
</div>
@endif
