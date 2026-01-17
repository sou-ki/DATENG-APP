@extends('layouts.app')

@section('title', 'Dashboard Internal')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-4">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard Internal
                </h5>
                
                <div class="row">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Terdaftar</h6>
                                        <h2 class="mb-0">{{ $counts['registered'] ?? 0 }}</h2>
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
                                        <h6 class="card-title">Sedang Berkunjung</h6>
                                        <h2 class="mb-0">{{ $counts['checked_in'] ?? 0 }}</h2>
                                    </div>
                                    <i class="bi bi-person-check display-6 opacity-50"></i>
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
                                        <h2 class="mb-0">{{ $counts['checked_out'] ?? 0 }}</h2>
                                    </div>
                                    <i class="bi bi-check-circle display-6 opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                @if(count($recentVisits ?? []) > 0)
                <div class="mt-4">
                    <h6 class="mb-3">Kunjungan Terbaru</h6>
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Visitor</th>
                                    <th>Tujuan</th>
                                    <th>Jenis</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentVisits as $visit)
                                <tr>
                                    <td>{{ $visit->visit_date->format('d/m/Y') }}</td>
                                    <td>{{ $visit->visitor->full_name }}</td>
                                    <td>{{ Str::limit($visit->purpose, 30) }}</td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $visit->visit_type_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ match($visit->status) {
                                            'registered' => 'primary',
                                            'checked_in' => 'warning',
                                            'checked_out' => 'success',
                                            'rejected' => 'danger',
                                            default => 'secondary'
                                        } }}">
                                            {{ $visit->status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @else
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Belum ada data kunjungan. Mulai dengan membuat kunjungan baru.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection