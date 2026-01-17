@extends('layouts.app')

@section('title', 'Kunjungan Aktif')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Warning for visits without badge -->
        @if($visitsWithoutBadge && $visitsWithoutBadge->count() > 0)
        <div class="alert alert-danger mb-4">
            <h6><i class="bi bi-exclamation-triangle me-2"></i> Peringatan: Kunjungan Tanpa Badge</h6>
            <p class="mb-2">Terdapat {{ $visitsWithoutBadge->count() }} kunjungan aktif tanpa badge assignment:</p>
            <ul class="mb-0">
                @foreach($visitsWithoutBadge as $visit)
                <li>
                    {{ $visit->visitor->full_name }} - {{ $visit->division->division_name ?? 'Tidak ada divisi' }}
                    <a href="{{ route('security.checkin') }}?search={{ $visit->visitor->identity_number }}" 
                       class="btn btn-sm btn-outline-danger ms-2">
                        <i class="bi bi-tag"></i> Assign Badge
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
        @endif
        
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-person-badge me-2"></i> Kunjungan Aktif
                    </h5>
                    <div class="badge bg-warning">
                        <i class="bi bi-people"></i> {{ $activeVisits->count() }} Visitor
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($activeVisits->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Visitor</th>
                                <th>Institusi</th>
                                <th>Divisi Tujuan</th>
                                <th>Badge</th>
                                <th>Check-in</th>
                                <th>Durasi</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activeVisits as $visit)
                            @php
                                $duration = $visit->badgeAssignment && $visit->badgeAssignment->assigned_at 
                                    ? $visit->badgeAssignment->assigned_at->diff(now())
                                    : null;
                            @endphp
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $visit->visitor->full_name }}</div>
                                    <small class="text-muted">{{ $visit->visitor->identity_number }}</small>
                                </td>
                                <td>{{ $visit->visitor->institution }}</td>
                                <td>{{ $visit->division->division_name ?? '-' }}</td>
                                <td>
                                    @if($visit->badgeAssignment && $visit->badgeAssignment->badge)
                                    <span class="badge bg-info">
                                        {{ $visit->badgeAssignment->badge->badge_code }}
                                    </span>
                                    @else
                                    <span class="badge bg-danger">TIDAK ADA</span>
                                    @endif
                                </td>
                                <td>
                                    @if($visit->badgeAssignment && $visit->badgeAssignment->assigned_at)
                                    <div>{{ $visit->badgeAssignment->assigned_at->format('H:i') }}</div>
                                    <small class="text-muted">oleh {{ $visit->badgeAssignment->assigner->name ?? '-' }}</small>
                                    @else
                                    <span class="text-danger">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($duration)
                                    <span class="badge bg-{{ $duration->h >= 2 ? 'danger' : ($duration->h >= 1 ? 'warning' : 'success') }}">
                                        {{ $duration->format('%hh %im') }}
                                    </span>
                                    @else
                                    <span class="badge bg-secondary">-</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ Str::limit($visit->purpose, 50) }}</small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        @if($visit->badgeAssignment)
                                        <a href="{{ route('security.checkout') }}?search={{ $visit->visitor->identity_number }}" 
                                           class="btn btn-warning" title="Check-out">
                                            <i class="bi bi-box-arrow-right"></i>
                                        </a>
                                        @endif
                                        <a href="tel:{{ $visit->visitor->phone_number }}" 
                                           class="btn btn-outline-info" title="Telepon">
                                            <i class="bi bi-telephone"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="bi bi-check-circle display-4 text-success"></i>
                    <h5 class="text-success mt-3">Tidak ada kunjungan aktif</h5>
                    <p class="text-muted">Semua visitor telah check-out dengan baik</p>
                    <a href="{{ route('security.checkin') }}" class="btn btn-primary mt-2">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Lihat Antrian Check-in
                    </a>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Statistics Card -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">Statistik Hari Ini</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="display-6 text-primary">{{ $todayStats['total'] ?? 0 }}</div>
                        <small class="text-muted">Total Kunjungan</small>
                    </div>
                    <div class="col-md-3">
                        <div class="display-6 text-warning">{{ $todayStats['active'] ?? 0 }}</div>
                        <small class="text-muted">Sedang Berkunjung</small>
                    </div>
                    <div class="col-md-3">
                        <div class="display-6 text-success">{{ $todayStats['completed'] ?? 0 }}</div>
                        <small class="text-muted">Selesai</small>
                    </div>
                    <div class="col-md-3">
                        <div class="display-6 text-info">{{ $todayStats['avg_duration'] ?? '0m' }}</div>
                        <small class="text-muted">Rata-rata Durasi</small>
                    </div>
                </div>
                
                @if($todayStats['without_badge'] > 0)
                <div class="alert alert-warning mt-3">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Terdapat {{ $todayStats['without_badge'] }} kunjungan aktif tanpa badge assignment.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection