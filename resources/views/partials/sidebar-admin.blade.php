<!-- resources/views/partials/sidebar-admin.blade.php -->
<ul class="nav flex-column">
    <li class="nav-item mb-2">
        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
           href="{{ route('admin.dashboard') }}">
            <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>
    </li>
    
    <li class="nav-item mb-2">
        <a class="nav-link" href="#users" onclick="switchToTab('users-tab')">
            <i class="bi bi-people me-2"></i> User Management
        </a>
    </li>
    
    <li class="nav-item mb-2">
        <a class="nav-link" href="#badges" onclick="switchToTab('badges-tab')">
            <i class="bi bi-tag me-2"></i> ID Badge
            @php
                // Count badges marked as in_use but not assigned to any active visit request
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
    
    <li class="nav-item mb-2">
        <a class="nav-link" href="#visitors" onclick="switchToTab('visitors-tab')">
            <i class="bi bi-person-lines-fill me-2"></i> Visitor (Info)
        </a>
    </li>
    
</ul>

@if(auth()->check())
    <div class="mt-5 pt-4 border-top">
        <small class="text-muted">Admin Status:</small>
        <div class="d-flex justify-content-between mt-2">
            <span>Total Users:</span>
            <span class="fw-bold text-primary">
                {{ \App\Models\User::count() }}
            </span>
        </div>
        <div class="d-flex justify-content-between mt-1">
            <span>Visits Today:</span>
            <span class="fw-bold text-success">
                {{ \App\Models\VisitRequest::whereDate('visit_date', today())->count() }}
            </span>
        </div>
        <div class="d-flex justify-content-between mt-1">
            <span>Badge Available:</span>
            <span class="fw-bold {{ \App\Models\Badge::where('status', 'available')->count() > 2 ? 'text-success' : 'text-danger' }}">
                {{ \App\Models\Badge::where('status', 'available')->count() }}
            </span>
        </div>
    </div>
@endif

<script>
function switchToTab(tabId) {
    const tabElement = document.getElementById(tabId);
    if (tabElement) {
        const tab = new bootstrap.Tab(tabElement);
        tab.show();
        
        // Scroll to the tab content
        setTimeout(() => {
            document.getElementById('adminTabs').scrollIntoView({ behavior: 'smooth' });
        }, 100);
    }
}
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