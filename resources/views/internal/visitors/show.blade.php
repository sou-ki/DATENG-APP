@extends('layouts.app')

@section('title', 'Detail Visitor')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('internal.visitors.index') }}">Data Visitor</a></li>
<li class="breadcrumb-item active">{{ $visitor->full_name }}</li>
@endsection

@section('actions')
<div class="btn-group">
    <a href="{{ route('internal.visitors.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-2"></i> Kembali
    </a>
    <a href="{{ route('internal.visitors.edit', $visitor) }}" class="btn btn-warning">
        <i class="bi bi-pencil me-2"></i> Edit
    </a>
    <a href="{{ route('internal.visit-requests.create') }}?visitor_id={{ $visitor->id }}" 
       class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i> Buat Kunjungan
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Visitor Information Card -->
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Informasi Visitor</h5>
                    <span class="badge bg-info">
                        <i class="bi bi-calendar-check me-1"></i> {{ $visitor->visit_requests_count }} Kunjungan
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Nama Lengkap</label>
                        <div class="fw-bold fs-5">{{ $visitor->full_name }}</div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Nomor KTP</label>
                        <div class="font-monospace">{{ $visitor->identity_number }}</div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Institusi/Perusahaan</label>
                        <div class="fw-bold">{{ $visitor->institution }}</div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Nomor Telepon</label>
                        <div>
                            {{ $visitor->phone_number }}
                            @if($visitor->phone_number)
                            <a href="tel:{{ $visitor->phone_number }}" class="btn btn-sm btn-outline-info ms-2">
                                <i class="bi bi-telephone"></i>
                            </a>
                            @endif
                        </div>
                    </div>
                    
                    @if($visitor->email)
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Email</label>
                        <div>
                            {{ $visitor->email }}
                            @if($visitor->email)
                            <a href="mailto:{{ $visitor->email }}" class="btn btn-sm btn-outline-info ms-2">
                                <i class="bi bi-envelope"></i>
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif
                    
                    @if($visitor->address)
                    <div class="col-12 mb-3">
                        <label class="form-label text-muted">Alamat</label>
                        <div class="p-2 bg-light rounded">
                            {{ $visitor->address }}
                        </div>
                    </div>
                    @endif
                    
                    <div class="col-md-6">
                        <label class="form-label text-muted">Terdaftar Sejak</label>
                        <div>{{ $visitor->created_at->format('d F Y') }}</div>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label text-muted">Terakhir Diperbarui</label>
                        <div>{{ $visitor->updated_at->format('d F Y H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Visit Statistics -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Statistik Kunjungan</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="display-6 text-primary">{{ $visitor->visit_requests_count }}</div>
                        <small class="text-muted">Total Kunjungan</small>
                    </div>
                    <div class="col-md-3">
                        <div class="display-6 text-success">{{ $visitStats['checked_out'] ?? 0 }}</div>
                        <small class="text-muted">Selesai</small>
                    </div>
                    <div class="col-md-3">
                        <div class="display-6 text-warning">{{ $visitStats['checked_in'] ?? 0 }}</div>
                        <small class="text-muted">Sedang Berkunjung</small>
                    </div>
                    <div class="col-md-3">
                        <div class="display-6 text-info">{{ $visitStats['registered'] ?? 0 }}</div>
                        <small class="text-muted">Terdaftar</small>
                    </div>
                </div>
                
                @if($visitor->visit_requests_count > 0)
                <div class="progress mt-3" style="height: 20px;">
                    @php
                        $total = $visitor->visit_requests_count;
                        $completed = $visitStats['checked_out'] ?? 0;
                        $active = $visitStats['checked_in'] ?? 0;
                        $registered = $visitStats['registered'] ?? 0;
                    @endphp
                    <div class="progress-bar bg-success" style="width: {{ ($completed/$total)*100 }}%">
                        {{ $completed }}
                    </div>
                    <div class="progress-bar bg-warning" style="width: {{ ($active/$total)*100 }}%">
                        {{ $active }}
                    </div>
                    <div class="progress-bar bg-primary" style="width: {{ ($registered/$total)*100 }}%">
                        {{ $registered }}
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-2 small">
                    <span class="text-success">✓ Selesai</span>
                    <span class="text-warning">↻ Aktif</span>
                    <span class="text-primary">⏱ Terdaftar</span>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Recent Visits -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Riwayat Kunjungan Terbaru</h5>
            </div>
            <div class="card-body">
                @if($recentVisits->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Divisi Tujuan</th>
                                <th>Tujuan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentVisits as $visit)
                            <tr>
                                <td>
                                    <div>{{ $visit->visit_date->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ $visit->start_time }}</small>
                                </td>
                                <td>{{ $visit->division->division_name }}</td>
                                <td>{{ Str::limit($visit->purpose, 40) }}</td>
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
                                    <a href="{{ route('internal.visit-requests.show', $visit) }}" 
                                       class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($visitor->visit_requests_count > 10)
                <div class="text-center mt-3">
                    <a href="{{ route('internal.visit-requests.index') }}?search={{ $visitor->identity_number }}" 
                       class="btn btn-outline-primary btn-sm">
                        Lihat Semua Kunjungan
                    </a>
                </div>
                @endif
                @else
                <div class="text-center py-4">
                    <i class="bi bi-calendar-x display-4 text-muted"></i>
                    <p class="text-muted mt-3">Belum ada riwayat kunjungan</p>
                    <a href="{{ route('internal.visit-requests.create') }}?visitor_id={{ $visitor->id }}" 
                       class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i> Buat Kunjungan Pertama
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Aksi Cepat</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-primary" onclick="copyToClipboard('{{ $visitor->identity_number }}')">
                        <i class="bi bi-copy me-2"></i> Salin No. KTP
                    </button>
                    
                    @if($visitor->phone_number)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $visitor->phone_number) }}?text=Halo%20{{ urlencode($visitor->full_name) }}%2C%20kami%20dari%20perusahaan..." 
                       target="_blank" class="btn btn-outline-success">
                        <i class="bi bi-whatsapp me-2"></i> WhatsApp
                    </a>
                    @endif
                    
                    @if($visitor->email)
                    <a href="mailto:{{ $visitor->email }}?subject=Konfirmasi%20Kunjungan" 
                       class="btn btn-outline-info">
                        <i class="bi bi-envelope me-2"></i> Email
                    </a>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Delete Option (if no visits) -->
        @if($visitor->visit_requests_count === 0)
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0">Hapus Data</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Perhatian!</strong> Tindakan ini tidak dapat dibatalkan.
                </div>
                <form action="{{ route('internal.visitors.destroy', $visitor) }}" method="POST" 
                      onsubmit="return confirm('Yakin ingin menghapus visitor ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="bi bi-trash me-2"></i> Hapus Visitor
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Nomor KTP berhasil disalin: ' + text);
    }, function(err) {
        alert('Gagal menyalin: ', err);
    });
}
</script>
@endpush