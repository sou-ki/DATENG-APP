<ul class="nav flex-column">
    <li class="nav-item mb-2">
        <a class="nav-link" href="{{ route('internal.dashboard') }}">
            <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>
    </li>
    
    <li class="nav-item mb-2">
        <a class="nav-link" href="{{ route('internal.visit-requests.create') }}">
            <i class="bi bi-plus-circle me-2"></i> Buat Kunjungan
        </a>
    </li>
    
    <li class="nav-item mb-2">
        <a class="nav-link" href="{{ route('internal.visit-requests.index') }}">
            <i class="bi bi-list-check me-2"></i> Semua Kunjungan
        </a>
    </li>
    
    <li class="nav-item mb-2">
        <a class="nav-link" href="{{ route('internal.visit-requests.index', ['status' => 'registered']) }}">
            <i class="bi bi-clock me-2"></i> Menunggu
            @php
                $pendingCount = auth()->check() ? 
                    \App\Models\VisitRequest::where('created_by', auth()->id())
                        ->where('status', 'registered')
                        ->count() : 0;
            @endphp
            @if($pendingCount > 0)
                <span class="badge bg-danger float-end">{{ $pendingCount }}</span>
            @endif
        </a>
    </li>
    
    <li class="nav-item mb-2">
        <a class="nav-link" href="{{ route('internal.visit-requests.index', ['status' => 'checked_in']) }}">
            <i class="bi bi-person-check me-2"></i> Sedang Berkunjung
            @php
                $activeCount = auth()->check() ? 
                    \App\Models\VisitRequest::where('created_by', auth()->id())
                        ->where('status', 'checked_in')
                        ->count() : 0;
            @endphp
            @if($activeCount > 0)
                <span class="badge bg-warning float-end">{{ $activeCount }}</span>
            @endif
        </a>
    </li>
    
    <li class="nav-item mb-2">
        <a class="nav-link" href="{{ route('internal.visitors.index') }}">
            <i class="bi bi-people me-2"></i> Data Visitor
        </a>
    </li>
</ul>

@if(auth()->check() && auth()->user()->division)
    <div class="mt-5 pt-4 border-top">
        <small class="text-muted">Divisi Anda:</small>
        <div class="fw-bold">{{ auth()->user()->division->division_name }}</div>
    </div>
@endif

</script>

<style>
.nav-link.active {
    background-color: rgba(13, 110, 253, 0.1);
    color: #0d6efd !important;
    border-left: 3px solid #0d6efd;
    font-weight: 500;
}

.nav-link {
    padding: 0.5rem 1rem;
    color: #495057;
    transition: all 0.2s;
    border-left: 3px solid transparent;
}

.nav-link:hover {
    background-color: rgba(0, 0, 0, 0.05);
    border-left-color: #adb5bd;
}

.badge {
    font-size: 0.65rem;
    padding: 0.25em 0.5em;
}
</style>