@extends('layouts.app')

@section('title', 'Kelola Badge')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Total Badge</h6>
                                <h2 class="mb-0">{{ $stats['total'] }}</h2>
                            </div>
                            <i class="bi bi-tags display-6 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Tersedia</h6>
                                <h2 class="mb-0">{{ $stats['available'] }}</h2>
                            </div>
                            <i class="bi bi-check-circle display-6 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Digunakan</h6>
                                <h2 class="mb-0">{{ $stats['in_use'] }}</h2>
                            </div>
                            <i class="bi bi-person-badge display-6 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Badge List -->
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daftar Badge</h5>
                    
                    <!-- Search and Filter -->
                    <div class="d-flex" style="gap: 10px;">
                        <form method="GET" action="{{ route('security.badges') }}" class="d-flex">
                            <div class="input-group" style="width: 250px;">
                                <input type="text" 
                                       name="search" 
                                       class="form-control" 
                                       placeholder="Cari kode/area..." 
                                       value="{{ $search ?? '' }}">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </form>
                        
                        <div class="btn-group">
                            <a href="{{ route('security.badges') }}" 
                               class="btn btn-outline-secondary {{ !$status ? 'active' : '' }}">
                                Semua
                            </a>
                            <a href="{{ route('security.badges', ['status' => 'available']) }}" 
                               class="btn btn-outline-success {{ $status == 'available' ? 'active' : '' }}">
                                Tersedia
                            </a>
                            <a href="{{ route('security.badges', ['status' => 'in_use']) }}" 
                               class="btn btn-outline-warning {{ $status == 'in_use' ? 'active' : '' }}">
                                Digunakan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($badges->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Kode Badge</th>
                                <th>Area Akses</th>
                                <th>Status</th>
                                <th>Penggunaan Terakhir</th>
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
                                <td>
                                    <div class="fw-bold">{{ $badge->badge_code }}</div>
                                    <small class="text-muted">ID: {{ $badge->id }}</small>
                                </td>
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
                                    <div>
                                        {{ $lastAssignment->assigned_at->format('d/m/Y H:i') }}
                                    </div>
                                    <small class="text-muted">
                                        @if($lastAssignment->visitRequest)
                                            {{ $lastAssignment->visitRequest->visitor->full_name }}
                                        @endif
                                    </small>
                                    @else
                                    <span class="text-muted">Belum pernah digunakan</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        {{-- HAPUS TOMBOL FORCE RETURN --}}
                                        {{-- @if($badge->status === 'in_use')
                                        <button type="button" 
                                                class="btn btn-outline-warning"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#forceReturnModal"
                                                data-badge-id="{{ $badge->id }}"
                                                data-badge-code="{{ $badge->badge_code }}">
                                            <i class="bi bi-arrow-return-left"></i>
                                        </button>
                                        @endif --}}
                                        
                                        <button type="button" 
                                                class="btn btn-outline-danger"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#reportIssueModal"
                                                data-badge-id="{{ $badge->id }}"
                                                data-badge-code="{{ $badge->badge_code }}">
                                            <i class="bi bi-exclamation-triangle"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    {{ $badges->links() }}
                </div>
                @else
                <div class="text-center py-5">
                    <i class="bi bi-tags display-4 text-muted"></i>
                    <p class="text-muted mt-3">Tidak ada badge ditemukan</p>
                    @if($search || $status)
                    <a href="{{ route('security.badges') }}" class="btn btn-outline-primary">
                        Tampilkan Semua
                    </a>
                    @endif
                </div>
                @endif
            </div>
        </div>
        
        <!-- Currently Assigned Badges -->
        @if($assignedBadges->count() > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Badge Sedang Digunakan</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Badge</th>
                                <th>Visitor</th>
                                <th>Divisi</th>
                                <th>Check-in</th>
                                <th>Durasi</th>
                                <th>Petugas</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assignedBadges as $assignment)
                            @php
                                $duration = $assignment->assigned_at->diff(now());
                            @endphp
                            <tr>
                                <td>
                                    <span class="badge bg-info">{{ $assignment->badge->badge_code }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $assignment->visitRequest->visitor->full_name }}</div>
                                    <small class="text-muted">{{ $assignment->visitRequest->visitor->institution }}</small>
                                </td>
                                <td>{{ $assignment->visitRequest->division->division_name }}</td>
                                <td>
                                    {{ $assignment->assigned_at->format('H:i') }}
                                    <div class="text-muted small">{{ $assignment->assigned_at->format('d/m') }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $duration->h >= 2 ? 'danger' : ($duration->h >= 1 ? 'warning' : 'success') }}">
                                        {{ $duration->format('%hh %im') }}
                                    </span>
                                </td>
                                <td>
                                    <small>{{ $assignment->assigner->name ?? '-' }}</small>
                                </td>
                                <td>
                                    <a href="{{ route('security.checkout') }}?search={{ $assignment->visitRequest->visitor->identity_number }}" 
                                       class="btn btn-sm btn-warning">
                                        <i class="bi bi-box-arrow-right"></i> Check-out
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Recent Activities -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Aktivitas Terkini</h5>
            </div>
            <div class="card-body">
                @if($recentActivities->count() > 0)
                <div class="timeline">
                    @foreach($recentActivities as $activity)
                    <div class="timeline-item mb-3">
                        <div class="d-flex">
                            <div class="timeline-marker">
                                @if($activity->returned_at)
                                <i class="bi bi-arrow-left-circle text-success"></i>
                                @else
                                <i class="bi bi-arrow-right-circle text-warning"></i>
                                @endif
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <h6 class="mb-1">
                                        Badge: <span class="badge bg-info">{{ $activity->badge->badge_code }}</span>
                                    </h6>
                                    <small class="text-muted">
                                        {{ $activity->assigned_at->format('d/m H:i') }}
                                    </small>
                                </div>
                                <p class="mb-1">
                                    @if($activity->returned_at)
                                    Dikembalikan oleh {{ $activity->visitRequest->visitor->full_name }}
                                    @else
                                    Dipinjam oleh {{ $activity->visitRequest->visitor->full_name }}
                                    @endif
                                </p>
                                <small class="text-muted">
                                    @if($activity->returned_at)
                                    Durasi: {{ $activity->assigned_at->diff($activity->returned_at)->format('%hh %im') }}
                                    @endif
                                    • Oleh: {{ $activity->assigner->name ?? 'System' }}
                                </small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4">
                    <i class="bi bi-clock-history display-4 text-muted"></i>
                    <p class="text-muted mt-3">Belum ada aktivitas badge</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- HAPUS FORCE RETURN MODAL -->
{{-- <div class="modal fade" id="forceReturnModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pengembalian Paksa Badge</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Perhatian!</strong> Tindakan ini akan:
                    <ul class="mb-0 mt-2">
                        <li>Mengembalikan badge secara paksa</li>
                        <li>Mengubah status kunjungan menjadi check-out</li>
                        <li>Mencatat aktivitas ini di log system</li>
                    </ul>
                </div>
                <p>Badge: <strong id="forceBadgeCode"></strong></p>
                <p class="text-danger">Gunakan hanya jika visitor lupa mengembalikan badge atau dalam keadaan darurat.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="forceReturnForm" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger">Ya, Kembalikan Paksa</button>
                </form>
            </div>
        </div>
    </div>
</div> --}}

<!-- Report Issue Modal -->
<div class="modal fade" id="reportIssueModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="reportIssueForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Laporkan Masalah Badge</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Badge: <strong id="issueBadgeCode"></strong></p>
                    
                    <div class="mb-3">
                        <label class="form-label required">Jenis Masalah</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="issue_type" 
                                   id="issueLost" value="lost" checked>
                            <label class="form-check-label" for="issueLost">
                                Hilang
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="issue_type" 
                                   id="issueDamaged" value="damaged">
                            <label class="form-check-label" for="issueDamaged">
                                Rusak
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label required">Nama Pelapor</label>
                        <input type="text" name="reporter_name" class="form-control" 
                               value="{{ auth()->user()->name }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label required">Deskripsi Masalah</label>
                        <textarea name="description" class="form-control" rows="3" 
                                  placeholder="Jelaskan kondisi badge atau kejadian kehilangan..." required></textarea>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Badge akan ditandai sebagai tidak tersedia sampai masalah diselesaikan.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Laporkan Masalah</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Resolve Issue Modal -->
<div class="modal fade" id="resolveIssueModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="resolveIssueForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Selesaikan Masalah Badge</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Badge: <strong id="resolveBadgeCode"></strong></p>
                    
                    <div class="mb-3">
                        <label class="form-label required">Tindakan Penyelesaian</label>
                        <textarea name="resolution" class="form-control" rows="3" 
                                  placeholder="Jelaskan bagaimana masalah diselesaikan (ditemukan/diperbaiki/diganti)..." required></textarea>
                    </div>
                    
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle me-2"></i>
                        Badge akan kembali tersedia untuk digunakan.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Selesaikan Masalah</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 20px;
    }
    .timeline-item {
        position: relative;
        padding-left: 20px;
    }
    .timeline-marker {
        position: absolute;
        left: 0;
        top: 0;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }
    .timeline-item:not(:last-child):after {
        content: '';
        position: absolute;
        left: 20px;
        top: 40px;
        bottom: -20px;
        width: 2px;
        background-color: #dee2e6;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Report Issue Modal
    const reportIssueModal = document.getElementById('reportIssueModal');
    if (reportIssueModal) {
        reportIssueModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const badgeId = button.getAttribute('data-badge-id');
            const badgeCode = button.getAttribute('data-badge-code');
            
            document.getElementById('issueBadgeCode').textContent = badgeCode;
            
            const form = document.getElementById('reportIssueForm');
            if (form) {
                form.action = `/security/badges/${badgeId}/report-issue`;
            }
        });
    }
    
    // Resolve Issue Modal (manual trigger)
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-resolve-issue')) {
            e.preventDefault();
            
            const badgeId = e.target.getAttribute('data-badge-id');
            const badgeCode = e.target.getAttribute('data-badge-code');
            
            document.getElementById('resolveBadgeCode').textContent = badgeCode;
            
            const form = document.getElementById('resolveIssueForm');
            if (form) {
                form.action = `/security/badges/${badgeId}/resolve-issue`;
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('resolveIssueModal'));
                modal.show();
            }
        }
    });
    
    // HAPUS BAGIAN INI (yang menampilkan alert masalah otomatis)
    // Bagian JavaScript yang mengecek badge dengan masalah dihapus seluruhnya
    
});
</script>
@endpush