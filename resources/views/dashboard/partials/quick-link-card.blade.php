<div class="col-xl-3 col-lg-4 col-md-6">
    <a href="{{ $href }}" class="card dashboard-quick-link h-100 text-decoration-none">
        <div class="card-body d-flex align-items-center gap-3 py-3">
            <div class="theme-avtar bg-{{ $color ?? 'primary' }} flex-shrink-0">
                <i class="{{ $icon ?? 'ti ti-link' }}"></i>
            </div>
            <div class="flex-grow-1 min-w-0">
                <h6 class="mb-0 text-body">{{ $title }}</h6>
                @if (!empty($description))
                    <small class="text-muted d-block text-truncate">{{ $description }}</small>
                @endif
            </div>
            <i class="ti ti-chevron-right text-muted flex-shrink-0"></i>
        </div>
    </a>
</div>
