<ul class="nav flex-column">
    <li class="nav-item mb-2">
        <a class="nav-link" href="{{ route('security.dashboard') }}">
            <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>
    </li>
    
    <li class="nav-item mb-2">
        <a class="nav-link" href="{{ route('security.checkin') }}">
            <i class="bi bi-box-arrow-in-right me-2"></i> Check-in
            @php
                $pendingToday = auth()->check() ? 
                    \App\Models\VisitRequest::where('status', 'registered')
                        ->whereDate('visit_date', today())
                        ->count() : 0;
            @endphp
            @if($pendingToday > 0)
                <span class="badge bg-danger float-end">{{ $pendingToday }}</span>
            @endif
        </a>
    </li>
    
    <li class="nav-item mb-2">
        <a class="nav-link" href="{{ route('security.checkout') }}">
            <i class="bi bi-box-arrow-right me-2"></i> Check-out
            @php
                $activeCount = auth()->check() ? 
                    \App\Models\VisitRequest::where('status', 'checked_in')
                        ->whereHas('badgeAssignment')
                        ->count() : 0;
            @endphp
            @if($activeCount > 0)
                <span class="badge bg-warning float-end">{{ $activeCount }}</span>
            @endif
        </a>
    </li>
    
    <li class="nav-item mb-2">
        <a class="nav-link" href="{{ route('security.active-visits') }}">
            <i class="bi bi-person-badge me-2"></i> Kunjungan Aktif
        </a>
    </li>
    
    <li class="nav-item mb-2">
        <a class="nav-link" href="{{ route('security.badges') }}">
            <i class="bi bi-tag me-2"></i> Kelola Badge
            @php
                $badgeIssues = auth()->check() ? 
                    \App\Models\Badge::where('status', 'in_use')
                        ->whereDoesntHave('badgeAssignments', function($q) {
                            $q->whereNull('returned_at');
                        })
                        ->count() : 0;
            @endphp
            @if($badgeIssues > 0)
                <span class="badge bg-danger float-end">{{ $badgeIssues }}</span>
            @endif
        </a>
    </li>
</ul>

@if(auth()->check())
    <div class="mt-5 pt-4 border-top">
        <small class="text-muted">Status Pos:</small>
        <div class="d-flex justify-content-between">
            <span>Badge Tersedia:</span>
            @php
                $availableBadges = auth()->check() ? 
                    \App\Models\Badge::where('status', 'available')->count() : 0;
            @endphp
            <span class="fw-bold {{ $availableBadges > 2 ? 'text-success' : 'text-danger' }}">
                {{ $availableBadges }}
            </span>
        </div>
    </div>
@endif