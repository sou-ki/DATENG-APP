@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-4">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard Administrator
                </h5>
                
                <div class="row">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Total Users</h6>
                                        <h2 class="mb-0">{{ $counts['users'] ?? 0 }}</h2>
                                    </div>
                                    <i class="bi bi-people display-6 opacity-50"></i>
                                </div>
                                <div class="mt-2">
                                    <small>
                                        {{ $counts['internal'] ?? 0 }} Internal | 
                                        {{ $counts['security'] ?? 0 }} Security | 
                                        {{ $counts['admin'] ?? 0 }} Admin
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card bg-info text-white mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Kunjungan (30 hari)</h6>
                                        <h2 class="mb-0">{{ $counts['visits_30d'] ?? 0 }}</h2>
                                    </div>
                                    <i class="bi bi-calendar-check display-6 opacity-50"></i>
                                </div>
                                <div class="mt-2">
                                    <small>
                                        {{ $counts['visits_today'] ?? 0 }} hari ini
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card bg-success text-white mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Total Visitor</h6>
                                        <h2 class="mb-0">{{ $counts['visitors'] ?? 0 }}</h2>
                                    </div>
                                    <i class="bi bi-person-badge display-6 opacity-50"></i>
                                </div>
                                <div class="mt-2">
                                    <small>Master data pengunjung</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card bg-warning text-white mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Badge</h6>
                                        <h2 class="mb-0">{{ $counts['badges'] ?? 0 }}</h2>
                                    </div>
                                    <i class="bi bi-tags display-6 opacity-50"></i>
                                </div>
                                <div class="mt-2">
                                    <small>
                                        {{ $counts['badges_available'] ?? 0 }} tersedia
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="bi bi-bar-chart me-2"></i> Statistik Kunjungan (7 hari terakhir)
                                </h6>
                            </div>
                            <div class="card-body">
                                @if(count($visitStats ?? []) > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Terdaftar</th>
                                                <th>Check-in</th>
                                                <th>Check-out</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($visitStats as $stat)
                                            <tr>
                                                <td>{{ $stat['date']->format('d/m') }}</td>
                                                <td>{{ $stat['registered'] }}</td>
                                                <td>{{ $stat['checked_in'] }}</td>
                                                <td>{{ $stat['checked_out'] }}</td>
                                                <td class="fw-bold">{{ $stat['total'] }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="text-center py-3">
                                    <i class="bi bi-bar-chart display-4 text-muted"></i>
                                    <p class="text-muted mt-2">Belum ada data statistik</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="bi bi-activity me-2"></i> Aktivitas Sistem
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item px-0 d-flex justify-content-between">
                                        <span>Database Size:</span>
                                        <span class="fw-bold">{{ $systemInfo['db_size'] ?? '0 MB' }}</span>
                                    </div>
                                    <div class="list-group-item px-0 d-flex justify-content-between">
                                        <span>Storage Free:</span>
                                        <span class="fw-bold">{{ $systemInfo['storage_free'] ?? '0%' }}</span>
                                    </div>
                                    <div class="list-group-item px-0 d-flex justify-content-between">
                                        <span>Log Entries:</span>
                                        <span class="fw-bold">{{ $systemInfo['log_count'] ?? 0 }}</span>
                                    </div>
                                    <div class="list-group-item px-0 d-flex justify-content-between">
                                        <span>Uptime:</span>
                                        <span class="fw-bold">{{ $systemInfo['uptime'] ?? 'N/A' }}</span>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <a href="{{ route('admin.system.status') }}" class="btn btn-outline-primary btn-sm w-100">
                                        <i class="bi bi-gear me-2"></i> System Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="bi bi-clock-history me-2"></i> Audit Log Terbaru
                                </h6>
                            </div>
                            <div class="card-body">
                                @if(count($recentLogs ?? []) > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Waktu</th>
                                                <th>User</th>
                                                <th>Aksi</th>
                                                <th>Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentLogs as $log)
                                            <tr>
                                                <td>{{ $log->timestamp->format('d/m H:i') }}</td>
                                                <td>
                                                    {{ $log->performer->name ?? 'System' }}
                                                    <small class="d-block text-muted">{{ $log->performer->role ?? '' }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $log->action === 'check_in' ? 'success' : ($log->action === 'check_out' ? 'warning' : 'info') }}">
                                                        {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                                    </span>
                                                </td>
                                                <td>{{ Str::limit($log->notes, 50) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="text-center py-3">
                                    <i class="bi bi-clipboard-check display-4 text-muted"></i>
                                    <p class="text-muted mt-2">Belum ada audit log</p>
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