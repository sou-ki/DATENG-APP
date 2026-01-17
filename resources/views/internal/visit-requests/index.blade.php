@extends('layouts.app')

@section('title', 'Daftar Kunjungan')

@section('actions')
<div class="btn-group">
    <a href="{{ route('internal.visit-requests.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i> Buat Baru
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daftar Kunjungan</h5>
                    
                    <div class="btn-group">
                        <a href="{{ route('internal.visit-requests.index') }}" 
                           class="btn btn-outline-secondary {{ !$status ? 'active' : '' }}">
                            Semua
                        </a>
                        <a href="{{ route('internal.visit-requests.index', ['status' => 'registered']) }}" 
                           class="btn btn-outline-primary {{ $status == 'registered' ? 'active' : '' }}">
                            Terdaftar
                        </a>
                        <a href="{{ route('internal.visit-requests.index', ['status' => 'checked_in']) }}" 
                           class="btn btn-outline-warning {{ $status == 'checked_in' ? 'active' : '' }}">
                            Sedang Berkunjung
                        </a>
                        <a href="{{ route('internal.visit-requests.index', ['status' => 'checked_out']) }}" 
                           class="btn btn-outline-success {{ $status == 'checked_out' ? 'active' : '' }}">
                            Selesai
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($visitRequests->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Visitor</th>
                                <th>Divisi Tujuan</th>
                                <th>Tujuan</th>
                                <th>Jenis</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($visitRequests as $visit)
                            <tr>
                                <td>
                                    <div>{{ $visit->visit_date->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ $visit->start_time }}</small>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $visit->visitor->full_name }}</div>
                                    <small class="text-muted">{{ $visit->visitor->institution }}</small>
                                </td>
                                <td>{{ $visit->division->division_name }}</td>
                                <td>{{ Str::limit($visit->purpose, 40) }}</td>
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
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('internal.visit-requests.show', $visit) }}" 
                                           class="btn btn-outline-info" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($visit->status === 'registered')
                                        <a href="{{ route('internal.visit-requests.edit', $visit) }}" 
                                           class="btn btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    {{ $visitRequests->links() }}
                </div>
                @else
                <div class="text-center py-5">
                    <i class="bi bi-calendar-x display-4 text-muted"></i>
                    <h5 class="text-muted mt-3">Belum ada kunjungan</h5>
                    <p class="text-muted">Mulai dengan membuat kunjungan baru</p>
                    <a href="{{ route('internal.visit-requests.create') }}" class="btn btn-primary mt-2">
                        <i class="bi bi-plus-circle me-2"></i> Buat Kunjungan Baru
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection