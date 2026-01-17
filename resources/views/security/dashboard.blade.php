@extends('layouts.app')

@section('title', 'Dashboard Security')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-4">
                    <i class="bi bi-shield-check me-2"></i> Dashboard Security
                </h5>
                
                <div class="row">
                    <div class="col-md-3">
                        <div class="card bg-info text-white mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Hari Ini</h6>
                                        <h2 class="mb-0">{{ $todayCounts['total'] ?? 0 }}</h2>
                                        <small>Total Kunjungan</small>
                                    </div>
                                    <i class="bi bi-calendar-day display-6 opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card bg-primary text-white mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Menunggu</h6>
                                        <h2 class="mb-0">{{ $todayCounts['registered'] ?? 0 }}</h2>
                                        <small>Belum Check-in</small>
                                    </div>
                                    <i class="bi bi-clock display-6 opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card bg-warning text-white mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Aktif</h6>
                                        <h2 class="mb-0">{{ $todayCounts['checked_in'] ?? 0 }}</h2>
                                        <small>Sedang Berkunjung</small>
                                    </div>
                                    <i class="bi bi-person-badge display-6 opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card bg-success text-white mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Selesai</h6>
                                        <h2 class="mb-0">{{ $todayCounts['checked_out'] ?? 0 }}</h2>
                                        <small>Sudah Check-out</small>
                                    </div>
                                    <i class="bi bi-check-circle display-6 opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="bi bi-card-checklist me-2"></i> Antrian Check-in Hari Ini
                                </h6>
                            </div>
                            <div class="card-body">
                                @if(isset($pendingVisits) && $pendingVisits->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Waktu</th>
                                                <th>Nama</th>
                                                <th>Institusi</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($pendingVisits as $visit)
                                            <tr>
                                                <td>{{ $visit->start_time }}</td>
                                                <td>{{ $visit->visitor->full_name ?? 'N/A' }}</td>
                                                <td>{{ $visit->visitor->institution ?? 'N/A' }}</td>
                                                <td>
                                                    <a href="{{ route('security.checkin') }}?search={{ $visit->visitor->identity_number ?? '' }}" 
                                                       class="btn btn-sm btn-primary">
                                                        <i class="bi bi-box-arrow-in-right"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="text-center py-3">
                                    <i class="bi bi-check2-circle display-4 text-muted"></i>
                                    <p class="text-muted mt-2">Tidak ada antrian check-in</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="bi bi-person-badge me-2"></i> Kunjungan Aktif
                                </h6>
                            </div>
                            <div class="card-body">
                                @if(isset($activeVisits) && $activeVisits->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Nama</th>
                                                <th>Check-in</th>
                                                <th>Badge</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($activeVisits as $visit)
                                            <tr>
                                                <td>{{ $visit->visitor->full_name ?? 'N/A' }}</td>
                                                <td>
                                                    @if($visit->badgeAssignment && $visit->badgeAssignment->assigned_at)
                                                        {{ \Carbon\Carbon::parse($visit->badgeAssignment->assigned_at)->format('H:i') }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($visit->badgeAssignment && $visit->badgeAssignment->badge)
                                                    <span class="badge bg-info">
                                                        {{ $visit->badgeAssignment->badge->badge_code }}
                                                    </span>
                                                    @else
                                                    <span class="badge bg-secondary">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('security.checkout') }}?search={{ $visit->visitor->identity_number ?? '' }}" 
                                                       class="btn btn-sm btn-warning">
                                                        <i class="bi bi-box-arrow-right"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="text-center py-3">
                                    <i class="bi bi-people display-4 text-muted"></i>
                                    <p class="text-muted mt-2">Tidak ada kunjungan aktif</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="bi bi-tag me-2"></i> Status Badge
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span>Total Badge:</span>
                                    <span class="fw-bold">{{ $badgeCounts['total'] ?? 0 }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span>Tersedia:</span>
                                    <span class="fw-bold text-success">{{ $badgeCounts['available'] ?? 0 }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Digunakan:</span>
                                    <span class="fw-bold text-warning">{{ $badgeCounts['in_use'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="bi bi-clock-history me-2"></i> Aktivitas Terakhir
                                </h6>
                            </div>
                            <div class="card-body">
                                @if(isset($recentLogs) && $recentLogs->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($recentLogs as $log)
                                    <div class="list-group-item px-0">
                                        <div class="d-flex w-100 justify-content-between">
                                            <small class="text-muted">
                                                {{ $log->visitRequest->visitor->full_name ?? 'N/A' }}
                                            </small>
                                            <small>{{ $log->timestamp->diffForHumans() }}</small>
                                        </div>
                                        <span class="badge bg-{{ $log->action === 'check_in' ? 'success' : 'warning' }}">
                                            {{ $log->action === 'check_in' ? 'Check-in' : 'Check-out' }}
                                        </span>
                                        <small class="d-block text-muted">
                                            {{ $log->performer->name ?? 'System' }}
                                        </small>
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <div class="text-center py-3">
                                    <i class="bi bi-activity display-4 text-muted"></i>
                                    <p class="text-muted mt-2">Belum ada aktivitas</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection