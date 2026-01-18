@extends('layouts.app')

@section('title', 'Edit Data Visitor')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('internal.visitors.index') }}">Data Visitor</a></li>
<li class="breadcrumb-item"><a href="{{ route('internal.visitors.show', $visitor) }}">{{ $visitor->full_name }}</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('actions')
<a href="{{ route('internal.visitors.show', $visitor) }}" class="btn btn-secondary">
    <i class="bi bi-arrow-left me-2"></i> Kembali
</a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-person-gear me-2"></i> Edit Data Visitor
                    </h5>
                    <span class="badge bg-info">
                        {{ $visitor->visit_requests_count ?? 0 }} Kunjungan
                    </span>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('internal.visitors.update', $visitor) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label required">Nama Lengkap</label>
                                <input type="text" name="full_name" class="form-control" 
                                       value="{{ old('full_name', $visitor->full_name) }}" required>
                                @error('full_name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label required">Nomor KTP</label>
                                <input type="text" name="identity_number" class="form-control" 
                                       value="{{ old('identity_number', $visitor->identity_number) }}" required>
                                @error('identity_number')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Tidak dapat mengubah ke nomor KTP yang sudah terdaftar</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label required">Institusi/Perusahaan</label>
                                <input type="text" name="institution" class="form-control" 
                                       value="{{ old('institution', $visitor->institution) }}" required>
                                @error('institution')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label required">Nomor Telepon</label>
                                <input type="text" name="phone_number" class="form-control" 
                                       value="{{ old('phone_number', $visitor->phone_number) }}" required>
                                @error('phone_number')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" 
                                       value="{{ old('email', $visitor->email) }}">
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea name="address" class="form-control" rows="1">{{ old('address', $visitor->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning mt-4">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Perhatian:</strong> Perubahan data visitor akan mempengaruhi semua riwayat kunjungan terkait.
                    </div>
                    
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('internal.visitors.show', $visitor) }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-2"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Informasi Visitor</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted">Terdaftar Sejak:</small>
                    <div class="fw-bold">{{ $visitor->created_at->format('d F Y') }}</div>
                </div>
                
                <div class="mb-3">
                    <small class="text-muted">Total Kunjungan:</small>
                    <div class="fw-bold">{{ $visitor->visit_requests_count ?? 0 }}</div>
                </div>
                
                <div class="mb-3">
                    <small class="text-muted">Kunjungan Terakhir:</small>
                    <div>
                        @if($visitor->visitRequests->count() > 0)
                            @php
                                $latestVisit = $visitor->visitRequests()
                                    ->orderBy('visit_date', 'desc')
                                    ->first();
                            @endphp
                            {{ $latestVisit->visit_date->format('d/m/Y') }}
                            <span class="badge bg-{{ $latestVisit->status === 'checked_in' ? 'warning' : 'success' }}">
                                {{ $latestVisit->status_label }}
                            </span>
                        @else
                            <span class="text-muted">Belum ada kunjungan</span>
                        @endif
                    </div>
                </div>
                
                @if($visitor->visitRequests->count() > 0)
                <div class="alert alert-info mt-3">
                    <h6>Riwayat Status Kunjungan:</h6>
                    @php
                        $statusCounts = $visitor->visitRequests()
                            ->selectRaw('status, COUNT(*) as count')
                            ->groupBy('status')
                            ->get()
                            ->pluck('count', 'status');
                    @endphp
                    <div class="d-flex justify-content-between small">
                        <span class="text-success">✓ Selesai: {{ $statusCounts['checked_out'] ?? 0 }}</span>
                        <span class="text-warning">↻ Aktif: {{ $statusCounts['checked_in'] ?? 0 }}</span>
                        <span class="text-primary">⏱ Menunggu: {{ $statusCounts['registered'] ?? 0 }}</span>
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">Aksi Cepat</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('internal.visit-requests.create') }}?visitor_id={{ $visitor->id }}" 
                       class="btn btn-success">
                        <i class="bi bi-plus-circle me-2"></i> Buat Kunjungan Baru
                    </a>
                    
                    @if($visitor->visitRequests()->whereIn('status', ['registered', 'checked_in'])->exists())
                    <a href="{{ route('internal.visit-requests.index', ['status' => 'registered']) }}" 
                       class="btn btn-outline-primary">
                        <i class="bi bi-clock me-2"></i> Lihat Kunjungan Aktif
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection