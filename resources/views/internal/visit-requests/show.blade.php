@extends('layouts.app')

@section('title', 'Detail Kunjungan')

@section('breadcrumb')
<li class="breadcrumb-item active">Detail Kunjungan</li>
@endsection

@section('actions')
<div class="btn-group">
    <a href="{{ route('internal.visit-requests.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-2"></i> Kembali
    </a>
    
    @if($visitRequest->status === 'registered')
    <a href="{{ route('internal.visit-requests.edit', $visitRequest) }}" class="btn btn-warning">
        <i class="bi bi-pencil me-2"></i> Edit
    </a>
    
    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
        <i class="bi bi-x-circle me-2"></i> Batalkan
    </button>
    @endif
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detail Kunjungan</h5>
                    <span class="badge bg-{{ match($visitRequest->status) {
                        'registered' => 'primary',
                        'checked_in' => 'warning',
                        'checked_out' => 'success',
                        'rejected' => 'danger',
                        default => 'secondary'
                    } }} fs-6">
                        {{ $visitRequest->status_label }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Visitor</label>
                        <div class="fw-bold">{{ $visitRequest->visitor->full_name }}</div>
                        <small class="text-muted">{{ $visitRequest->visitor->identity_number }}</small>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Institusi</label>
                        <div class="fw-bold">{{ $visitRequest->visitor->institution }}</div>
                        <small class="text-muted">{{ $visitRequest->visitor->phone_number }}</small>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Divisi Tujuan</label>
                        <div class="fw-bold">{{ $visitRequest->division->division_name }}</div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Jenis Kunjungan</label>
                        <div>
                            <span class="badge bg-info">{{ $visitRequest->visit_type_label }}</span>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label text-muted">Tanggal</label>
                        <div class="fw-bold">{{ $visitRequest->visit_date->format('d F Y') }}</div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label text-muted">Waktu Mulai</label>
                        <div class="fw-bold">{{ date('H:i', strtotime($visitRequest->start_time)) }}</div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label text-muted">Waktu Selesai</label>
                        <div class="fw-bold">
                            {{ $visitRequest->end_time ? date('H:i', strtotime($visitRequest->end_time)) : '-' }}
                        </div>
                    </div>
                    
                    <div class="col-12 mb-3">
                        <label class="form-label text-muted">Tujuan Kunjungan</label>
                        <div class="p-3 bg-light rounded">
                            {{ $visitRequest->purpose }}
                        </div>
                    </div>
                    
                    @if($visitRequest->letter_path)
                    <div class="col-12 mb-3">
                        <label class="form-label text-muted">Surat/Dokumen</label>
                        <div>
                            <a href="{{ Storage::url($visitRequest->letter_path) }}" 
                               target="_blank" 
                               class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-file-earmark me-2"></i> Lihat Dokumen
                            </a>
                        </div>
                    </div>
                    @endif
                    
                    <div class="col-12">
                        <label class="form-label text-muted">Dibuat Oleh</label>
                        <div class="d-flex align-items-center">
                            <div class="user-avatar me-2">
                                {{ strtoupper(substr($visitRequest->creator->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-bold">{{ $visitRequest->creator->name }}</div>
                                <small class="text-muted">{{ $visitRequest->created_at->format('d/m/Y H:i') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Badge Assignment Info -->
        @if($visitRequest->badgeAssignment)
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Informasi Badge</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label text-muted">Kode Badge</label>
                        <div class="fw-bold">{{ $visitRequest->badgeAssignment->badge->badge_code }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted">Area Akses</label>
                        <div>{{ $visitRequest->badgeAssignment->badge->access_area }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted">Check-in</label>
                        <div>{{ $visitRequest->badgeAssignment->assigned_at->format('d/m/Y H:i') }}</div>
                        <small class="text-muted">Oleh: {{ $visitRequest->badgeAssignment->assigner->name ?? '-' }}</small>
                    </div>
                    @if($visitRequest->badgeAssignment->returned_at)
                    <div class="col-md-6">
                        <label class="form-label text-muted">Check-out</label>
                        <div>{{ $visitRequest->badgeAssignment->returned_at->format('d/m/Y H:i') }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
        
        <!-- Visit Logs -->
        @if($visitRequest->visitLogs->count() > 0)
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Log Aktivitas</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @foreach($visitRequest->visitLogs->sortBy('timestamp') as $log)
                    <div class="timeline-item mb-3">
                        <div class="d-flex">
                            <div class="timeline-marker">
                                <i class="bi bi-{{ $log->action === 'check_in' ? 'arrow-right-circle' : ($log->action === 'check_out' ? 'arrow-left-circle' : 'x-circle') }} 
                                    text-{{ $log->action === 'check_in' ? 'success' : ($log->action === 'check_out' ? 'warning' : 'danger') }}"></i>
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <h6 class="mb-1">{{ $log->action_label }}</h6>
                                    <small class="text-muted">{{ $log->timestamp->format('d/m/Y H:i') }}</small>
                                </div>
                                <p class="mb-1">{{ $log->notes }}</p>
                                <small class="text-muted">
                                    Oleh: {{ $log->performer->name ?? 'System' }}
                                </small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
    
    <div class="col-lg-4">
        <!-- Status Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Status Kunjungan</h6>
            </div>
            <div class="card-body">
                <div class="status-flow">
                    @php
                        $statuses = [
                            'registered' => ['icon' => 'clipboard-check', 'label' => 'Terdaftar', 'color' => 'primary'],
                            'checked_in' => ['icon' => 'person-check', 'label' => 'Check-in', 'color' => 'warning'],
                            'checked_out' => ['icon' => 'check-circle', 'label' => 'Selesai', 'color' => 'success'],
                            'rejected' => ['icon' => 'x-circle', 'label' => 'Ditolak', 'color' => 'danger']
                        ];
                        
                        $currentStatus = $visitRequest->status;
                    @endphp
                    
                    @foreach($statuses as $status => $info)
                    <div class="status-step d-flex align-items-center mb-3">
                        <div class="status-icon">
                            <div class="icon-circle bg-{{ $info['color'] }} {{ $status === $currentStatus ? 'active' : '' }}">
                                <i class="bi bi-{{ $info['icon'] }}"></i>
                            </div>
                        </div>
                        <div class="status-details ms-3">
                            <div class="fw-bold">{{ $info['label'] }}</div>
                            @if($status === 'registered')
                                <small class="text-muted">Visitor terdaftar menunggu check-in</small>
                            @elseif($status === 'checked_in')
                                <small class="text-muted">Visitor sedang berkunjung</small>
                            @elseif($status === 'checked_out')
                                <small class="text-muted">Kunjungan selesai</small>
                            @else
                                <small class="text-muted">Kunjungan ditolak/dibatalkan</small>
                            @endif
                        </div>
                        @if($status === $currentStatus)
                        <div class="ms-auto">
                            <span class="badge bg-{{ $info['color'] }}">Status Saat Ini</span>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Aksi Cepat</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="javascript:window.print()" class="btn btn-outline-secondary">
                        <i class="bi bi-printer me-2"></i> Cetak Detail
                    </a>
                    
                    @if($visitRequest->status === 'registered')
                    <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#reminderModal">
                        <i class="bi bi-bell me-2"></i> Kirim Reminder
                    </button>
                    @endif
                    
                    @if(in_array($visitRequest->status, ['registered', 'checked_in']))
                    <a href="tel:{{ $visitRequest->visitor->phone_number }}" class="btn btn-outline-info">
                        <i class="bi bi-telephone me-2"></i> Hubungi Visitor
                    </a>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Statistics -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Statistik Visitor</h6>
            </div>
            <div class="card-body">
                @php
                    $visitorStats = $visitRequest->visitor->visitRequests()
                        ->selectRaw('status, COUNT(*) as count')
                        ->groupBy('status')
                        ->get()
                        ->pluck('count', 'status');
                @endphp
                
                <div class="mb-3">
                    <small class="text-muted">Total Kunjungan:</small>
                    <div class="fw-bold">{{ $visitorStats->sum() }}</div>
                </div>
                
                <div class="row">
                    <div class="col-6">
                        <small class="text-muted">Selesai:</small>
                        <div class="fw-bold text-success">{{ $visitorStats['checked_out'] ?? 0 }}</div>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Aktif:</small>
                        <div class="fw-bold text-warning">{{ $visitorStats['checked_in'] ?? 0 }}</div>
                    </div>
                </div>
                
                <div class="mt-3">
                    <a href="{{ route('internal.visitors.show', $visitRequest->visitor) }}" class="btn btn-sm btn-outline-primary w-100">
                        Lihat Profil Lengkap
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('internal.visit-requests.cancel', $visitRequest) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Batalkan Kunjungan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Kunjungan ini akan dibatalkan dan tidak dapat dikembalikan.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label required">Alasan Pembatalan</label>
                        <textarea name="cancellation_reason" class="form-control" rows="3" 
                                  placeholder="Berikan alasan pembatalan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Ya, Batalkan Kunjungan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reminder Modal -->
<div class="modal fade" id="reminderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kirim Reminder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Fitur pengiriman reminder akan tersedia segera.
                </div>
                <p>Reminder akan dikirim ke:</p>
                <ul>
                    <li>Email: {{ $visitRequest->visitor->email ?? 'Tidak ada email' }}</li>
                    <li>WhatsApp: {{ $visitRequest->visitor->phone_number }}</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" disabled>
                    <i class="bi bi-send me-2"></i> Kirim Reminder
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .icon-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.2rem;
        transition: all 0.3s;
    }
    .icon-circle.active {
        transform: scale(1.1);
        box-shadow: 0 0 0 4px rgba(var(--bs-primary-rgb), 0.2);
    }
    .status-step {
        position: relative;
        padding-left: 20px;
    }
    .status-step:not(:last-child):before {
        content: '';
        position: absolute;
        left: 25px;
        top: 50px;
        bottom: -20px;
        width: 2px;
        background-color: #dee2e6;
    }
    .timeline-marker {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .timeline-item {
        position: relative;
        padding-left: 20px;
    }
    .timeline-item:before {
        content: '';
        position: absolute;
        left: 20px;
        top: 40px;
        bottom: -20px;
        width: 2px;
        background-color: #dee2e6;
    }
    .timeline-item:last-child:before {
        display: none;
    }
</style>
@endpush