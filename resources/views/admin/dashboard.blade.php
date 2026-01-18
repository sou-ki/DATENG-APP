<!-- resources/views/admin/dashboard.blade.php -->
@extends('layouts.app')

@section('title', 'Admin Panel')

@push('styles')
<style>
    .tab-content {
        border: 1px solid #dee2e6;
        border-top: none;
        border-radius: 0 0 0.375rem 0.375rem;
        padding: 1.5rem;
        background: #fff;
    }
    .nav-tabs .nav-link {
        border-top-left-radius: 0.375rem;
        border-top-right-radius: 0.375rem;
        font-weight: 500;
    }
    .stats-card {
        transition: transform 0.2s;
    }
    .stats-card:hover {
        transform: translateY(-2px);
    }
    .table-actions {
        white-space: nowrap;
    }
    .form-ajax {
        display: none;
    }
</style>
@endpush

@section('content')
<div class="row mb-4">
    <!-- Statistics Cards -->
    <div class="col-md-3">
        <div class="card stats-card bg-primary text-white mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Total Users</h6>
                        <h2 class="mb-0">{{ $stats['users']['total'] }}</h2>
                    </div>
                    <i class="bi bi-people display-6 opacity-50"></i>
                </div>
                <div class="mt-2 small opacity-75">
                    {{ $stats['users']['internal'] }} Internal | 
                    {{ $stats['users']['security'] }} Security | 
                    {{ $stats['users']['admin'] }} Admin
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card stats-card bg-info text-white mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Total Visitors</h6>
                        <h2 class="mb-0">{{ $stats['visitors'] }}</h2>
                    </div>
                    <i class="bi bi-person-badge display-6 opacity-50"></i>
                </div>
                <div class="mt-2 small opacity-75">
                    Data master pengunjung
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card stats-card bg-success text-white mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Total Badges</h6>
                        <h2 class="mb-0">{{ $stats['badges']['total'] }}</h2>
                    </div>
                    <i class="bi bi-tags display-6 opacity-50"></i>
                </div>
                <div class="mt-2 small opacity-75">
                    {{ $stats['badges']['available'] }} Tersedia | 
                    {{ $stats['badges']['in_use'] }} Digunakan
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card stats-card bg-warning text-white mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Kunjungan Hari Ini</h6>
                        <h2 class="mb-0">{{ $stats['visits_today'] }}</h2>
                    </div>
                    <i class="bi bi-calendar-check display-6 opacity-50"></i>
                </div>
                <div class="mt-2 small opacity-75">
                    Total kunjungan hari ini
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs Navigation -->
<ul class="nav nav-tabs" id="adminTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button">
            <i class="bi bi-people me-2"></i> User Management
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="divisions-tab" data-bs-toggle="tab" data-bs-target="#divisions" type="button">
            <i class="bi bi-building me-2"></i> Division Management
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="badges-tab" data-bs-toggle="tab" data-bs-target="#badges" type="button">
            <i class="bi bi-tags me-2"></i> ID Badge
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="visitors-tab" data-bs-toggle="tab" data-bs-target="#visitors" type="button">
            <i class="bi bi-person-lines-fill me-2"></i> Visitor (Info)
        </button>
    </li>
</ul>

<!-- Tab Content -->
<div class="tab-content" id="adminTabsContent">
    
    <!-- Users Tab -->
    <div class="tab-pane fade show active" id="users" role="tabpanel">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0">User Management</h5>
            <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#createUserModal">
                <i class="bi bi-person-plus me-2"></i> Tambah User Baru
            </button>
        </div>
        
        @if($users->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Divisi</th>
                        <th>Status</th>
                        <th>Terdaftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td class="fw-bold">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'security' ? 'warning' : 'info') }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td>
                            @if($user->division)
                                {{ $user->division->division_name }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($user->email_verified_at)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-warning">Pending</span>
                            @endif
                        </td>
                        <td>{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="table-actions">
                            <button class="btn btn-sm btn-outline-warning" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editUserModal"
                                    data-user-id="{{ $user->id }}"
                                    data-user-name="{{ $user->name }}"
                                    data-user-email="{{ $user->email }}"
                                    data-user-role="{{ $user->role }}"
                                    data-user-division="{{ $user->division_id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            @if($user->id !== auth()->id())
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" 
                                  class="d-inline" onsubmit="return confirm('Hapus user ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-people display-4 text-muted"></i>
            <p class="text-muted mt-3">Belum ada user terdaftar</p>
        </div>
        @endif
    </div>
    
    <!-- Divisions Tab -->
    <div class="tab-pane fade" id="divisions" role="tabpanel">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0">Division Management</h5>
            <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#createDivisionModal">
                <i class="bi bi-building-add me-2"></i> Tambah Divisi Baru
            </button>
        </div>
        
        @if($divisions->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama Divisi</th>
                        <th>Deskripsi</th>
                        <th>Jumlah User</th>
                        <th>Total Kunjungan</th>
                        <th>Terdaftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($divisions as $division)
                    @php
                        $userCount = $division->users()->count();
                        $visitCount = $division->visitRequests()->count();
                    @endphp
                    <tr>
                        <td class="fw-bold">{{ $division->division_name }}</td>
                        <td>{{ $division->description ?: '-' }}</td>
                        <td>
                            <span class="badge bg-info">{{ $userCount }}</span>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $visitCount }}</span>
                        </td>
                        <td>{{ $division->created_at->format('d/m/Y') }}</td>
                        <td class="table-actions">
                            <button class="btn btn-sm btn-outline-warning"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editDivisionModal"
                                    data-division-id="{{ $division->id }}"
                                    data-division-name="{{ $division->division_name }}"
                                    data-division-description="{{ $division->description }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            @if($userCount === 0 && $visitCount === 0)
                            <form action="{{ route('admin.divisions.destroy', $division) }}" method="POST"
                                  class="d-inline" onsubmit="return confirm('Hapus divisi ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @else
                            <span class="text-muted small">Tidak dapat dihapus</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-building display-4 text-muted"></i>
            <p class="text-muted mt-3">Belum ada divisi terdaftar</p>
        </div>
        @endif
    </div>
    
    <!-- Badges Tab -->
    <div class="tab-pane fade" id="badges" role="tabpanel">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0">ID Badge Management</h5>
            <button class="btn btn-success" type="button" data-bs-toggle="modal" data-bs-target="#createBadgeModal">
                <i class="bi bi-tag me-2"></i> Tambah Badge Baru
            </button>
        </div>
        
        @if($badges->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Kode Badge</th>
                        <th>Area Akses</th>
                        <th>Status</th>
                        <th>Terakhir Digunakan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($badges as $badge)
                    @php
                        $lastAssignment = $badge->badgeAssignments()
                            ->orderBy('assigned_at', 'desc')
                            ->first();
                    @endphp
                    <tr>
                        <td class="fw-bold">{{ $badge->badge_code }}</td>
                        <td>{{ $badge->access_area }}</td>
                        <td>
                            @if($badge->status === 'available')
                                <span class="badge bg-success">Tersedia</span>
                            @else
                                <span class="badge bg-warning">Digunakan</span>
                            @endif
                        </td>
                        <td>
                            @if($lastAssignment)
                                {{ $lastAssignment->assigned_at->format('d/m/Y H:i') }}
                                <br>
                                <small class="text-muted">
                                    @if($lastAssignment->visitRequest)
                                        {{ $lastAssignment->visitRequest->visitor->full_name }}
                                    @endif
                                </small>
                            @else
                                <span class="text-muted">Belum pernah digunakan</span>
                            @endif
                        </td>
                        <td class="table-actions">
                            <button class="btn btn-sm btn-outline-warning"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editBadgeModal"
                                    data-badge-id="{{ $badge->id }}"
                                    data-badge-code="{{ $badge->badge_code }}"
                                    data-badge-area="{{ $badge->access_area }}"
                                    data-badge-status="{{ $badge->status }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            @if(!$badge->badgeAssignments()->exists())
                            <form action="{{ route('admin.badges.destroy', $badge) }}" method="POST"
                                  class="d-inline" onsubmit="return confirm('Hapus badge ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-tags display-4 text-muted"></i>
            <p class="text-muted mt-3">Belum ada badge terdaftar</p>
        </div>
        @endif
    </div>
    
    <!-- Visitors Tab -->
    <div class="tab-pane fade" id="visitors" role="tabpanel">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0">Visitor Information</h5>
            <div class="text-muted">
                Total: {{ $visitors->count() }} visitors
            </div>
        </div>
        
        @if($visitors->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>KTP</th>
                        <th>Institusi</th>
                        <th>Telepon</th>
                        <th>Total Kunjungan</th>
                        <th>Terdaftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($visitors as $visitor)
                    <tr>
                        <td class="fw-bold">{{ $visitor->full_name }}</td>
                        <td class="font-monospace">{{ $visitor->identity_number }}</td>
                        <td>{{ $visitor->institution }}</td>
                        <td>{{ $visitor->phone_number }}</td>
                        <td>
                            <span class="badge bg-primary">{{ $visitor->visit_requests_count }}</span>
                        </td>
                        <td>{{ $visitor->created_at->format('d/m/Y') }}</td>
                        <td class="table-actions">
                            <button class="btn btn-sm btn-outline-info"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editVisitorModal"
                                    data-visitor-id="{{ $visitor->id }}"
                                    data-visitor-name="{{ $visitor->full_name }}"
                                    data-visitor-identity="{{ $visitor->identity_number }}"
                                    data-visitor-institution="{{ $visitor->institution }}"
                                    data-visitor-phone="{{ $visitor->phone_number }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            @if($visitor->visit_requests_count === 0)
                            <form action="{{ route('admin.visitors.destroy', $visitor) }}" method="POST"
                                  class="d-inline" onsubmit="return confirm('Hapus visitor ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @else
                            <span class="text-muted small">Tidak dapat dihapus</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            <div class="text-muted text-center">
                Menampilkan {{ $visitors->count() }} dari {{ $stats['visitors'] }} visitors
            </div>
        </div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-person-lines-fill display-4 text-muted"></i>
            <p class="text-muted mt-3">Belum ada visitor terdaftar</p>
        </div>
        @endif
    </div>
</div>

<!-- Include Modal Files -->
@include('admin.modals.create-user')
@include('admin.modals.edit-user')
@include('admin.modals.create-division')
@include('admin.modals.edit-division')
@include('admin.modals.create-badge')
@include('admin.modals.edit-badge')
@include('admin.modals.edit-visitor')
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tabs activation
    const triggerTabList = document.querySelectorAll('#adminTabs button');
    triggerTabList.forEach(triggerEl => {
        const tabTrigger = new bootstrap.Tab(triggerEl);
        triggerEl.addEventListener('click', event => {
            event.preventDefault();
            tabTrigger.show();
        });
    });
    
    // User role change handler
    const userRoleSelect = document.getElementById('userRoleSelect');
    const divisionField = document.getElementById('divisionField');
    
    if (userRoleSelect) {
        userRoleSelect.addEventListener('change', function() {
            if (this.value === 'internal') {
                divisionField.style.display = 'block';
                divisionField.querySelector('select').required = true;
            } else {
                divisionField.style.display = 'none';
                divisionField.querySelector('select').required = false;
                divisionField.querySelector('select').value = '';
            }
        });
    }
    
    // Edit user modal handler
    const editUserModal = document.getElementById('editUserModal');
    if (editUserModal) {
        editUserModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const userId = button.getAttribute('data-user-id');
            const userName = button.getAttribute('data-user-name');
            const userEmail = button.getAttribute('data-user-email');
            const userRole = button.getAttribute('data-user-role');
            const userDivision = button.getAttribute('data-user-division');
            
            document.getElementById('editUserId').value = userId;
            document.getElementById('editUserName').value = userName;
            document.getElementById('editUserEmail').value = userEmail;
            document.getElementById('editUserRoleSelect').value = userRole;
            document.getElementById('editUserDivision').value = userDivision;
            
            // Set form action
            document.getElementById('editUserForm').action = `/admin/users/${userId}`;
            
            // Show/hide division field
            const editDivisionField = document.getElementById('editDivisionField');
            if (userRole === 'internal') {
                editDivisionField.style.display = 'block';
                editDivisionField.querySelector('select').required = true;
            } else {
                editDivisionField.style.display = 'none';
                editDivisionField.querySelector('select').required = false;
            }
        });
    }
    
    // Edit user role change handler
    const editUserRoleSelect = document.getElementById('editUserRoleSelect');
    const editDivisionField = document.getElementById('editDivisionField');
    
    if (editUserRoleSelect) {
        editUserRoleSelect.addEventListener('change', function() {
            if (this.value === 'internal') {
                editDivisionField.style.display = 'block';
                editDivisionField.querySelector('select').required = true;
            } else {
                editDivisionField.style.display = 'none';
                editDivisionField.querySelector('select').required = false;
                editDivisionField.querySelector('select').value = '';
            }
        });
    }
    
    // Edit badge modal handler
    const editBadgeModal = document.getElementById('editBadgeModal');
    if (editBadgeModal) {
        editBadgeModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const badgeId = button.getAttribute('data-badge-id');
            const badgeCode = button.getAttribute('data-badge-code');
            const badgeArea = button.getAttribute('data-badge-area');
            const badgeStatus = button.getAttribute('data-badge-status');
            
            document.getElementById('editBadgeId').value = badgeId;
            document.getElementById('editBadgeCode').value = badgeCode;
            document.getElementById('editBadgeArea').value = badgeArea;
            document.getElementById('editBadgeStatus').value = badgeStatus;
            
            // Set form action
            document.getElementById('editBadgeForm').action = `/admin/badges/${badgeId}`;
        });
    }
    
    // Edit division modal handler
    const editDivisionModal = document.getElementById('editDivisionModal');
    if (editDivisionModal) {
        editDivisionModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const divisionId = button.getAttribute('data-division-id');
            const divisionName = button.getAttribute('data-division-name');
            const divisionDescription = button.getAttribute('data-division-description');
            
            document.getElementById('editDivisionId').value = divisionId;
            document.getElementById('editDivisionName').value = divisionName;
            document.getElementById('editDivisionDescription').value = divisionDescription;
            
            // Set form action
            document.getElementById('editDivisionForm').action = `/admin/divisions/${divisionId}`;
        });
    }
    
    // Edit visitor modal handler
    const editVisitorModal = document.getElementById('editVisitorModal');
    if (editVisitorModal) {
        editVisitorModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const visitorId = button.getAttribute('data-visitor-id');
            const visitorName = button.getAttribute('data-visitor-name');
            const visitorIdentity = button.getAttribute('data-visitor-identity');
            const visitorInstitution = button.getAttribute('data-visitor-institution');
            const visitorPhone = button.getAttribute('data-visitor-phone');
            
            document.getElementById('editVisitorId').value = visitorId;
            document.getElementById('editVisitorName').value = visitorName;
            document.getElementById('editVisitorIdentity').value = visitorIdentity;
            document.getElementById('editVisitorInstitution').value = visitorInstitution;
            document.getElementById('editVisitorPhone').value = visitorPhone;
            
            // Set form action
            document.getElementById('editVisitorForm').action = `/admin/visitors/${visitorId}`;
        });
    }
    
    // Auto-refresh page after modal submit
    const modals = ['createUserModal', 'editUserModal', 'createDivisionModal', 
                   'editDivisionModal', 'createBadgeModal', 'editBadgeModal', 'editVisitorModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.addEventListener('hidden.bs.modal', function() {
                // Check if form was submitted successfully
                setTimeout(() => {
                    if (window.location.href.includes('success=')) {
                        window.location.reload();
                    }
                }, 100);
            });
        }
    });
});
</script>
@endpush