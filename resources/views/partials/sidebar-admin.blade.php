<ul class="nav flex-column">
    <li class="nav-item mb-2">
        <a class="nav-link" href="{{ route('admin.dashboard') }}">
            <i class="bi bi-speedometer2 me-2"></i> Dashboard Admin
        </a>
    </li>
    
    <li class="nav-item mb-2">
        <span class="nav-link fw-bold text-uppercase small text-muted">
            <i class="bi bi-people me-1"></i> Manajemen User
        </span>
    </li>
    
    <li class="nav-item mb-2">
        <a class="nav-link" href="{{ route('admin.users.index') }}">
            <i class="bi bi-person-plus me-2"></i> Semua User
        </a>
    </li>
    
    <li class="nav-item mb-2">
        <a class="nav-link" href="{{ route('admin.users.create') }}">
            <i class="bi bi-plus-circle me-2"></i> Tambah User Baru
        </a>
    </li>
    
    <li class="nav-item mb-2">
        <span class="nav-link fw-bold text-uppercase small text-muted">
            <i class="bi bi-building me-1"></i> Master Data
        </span>
    </li>
    
    <li class="nav-item mb-2">
        <a class="nav-link" href="{{ route('admin.divisions.index') }}">
            <i class="bi bi-diagram-3 me-2"></i> Divisi
        </a>
    </li>
    
    <li class="nav-item mb-2">
        <a class="nav-link" href="{{ route('admin.badges.index') }}">
            <i class="bi bi-tags me-2"></i> Badges
        </a>
    </li>
    
    <li class="nav-item mb-2">
        <span class="nav-link fw-bold text-uppercase small text-muted">
            <i class="bi bi-bar-chart me-1"></i> Analytics
        </span>
    </li>
    
    <li class="nav-item mb-2">
        <a class="nav-link" href="{{ route('admin.reports.visits') }}">
            <i class="bi bi-calendar-check me-2"></i> Laporan Kunjungan
        </a>
    </li>
    
    <li class="nav-item mb-2">
        <a class="nav-link" href="{{ route('admin.audit.logs') }}">
            <i class="bi bi-clipboard-check me-2"></i> Audit Logs
        </a>
    </li>
    
    <li class="nav-item mb-2">
        <a class="nav-link" href="{{ route('admin.system.status') }}">
            <i class="bi bi-gear me-2"></i> System Status
        </a>
    </li>
</ul>