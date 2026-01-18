@extends('layouts.app')

@section('title', 'Edit Kunjungan')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('internal.visit-requests.index') }}">Daftar Kunjungan</a></li>
<li class="breadcrumb-item"><a href="{{ route('internal.visit-requests.show', $visitRequest) }}">Detail</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('actions')
<a href="{{ route('internal.visit-requests.show', $visitRequest) }}" class="btn btn-secondary">
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
                        <i class="bi bi-pencil-square me-2"></i> Edit Kunjungan
                    </h5>
                    <span class="badge bg-primary">{{ $visitRequest->status_label }}</span>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('internal.visit-requests.update', $visitRequest) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <!-- Visitor Information (Readonly) -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2 mb-3">Data Visitor</h6>
                        <div class="visitor-info card bg-light p-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted">Nama:</small>
                                    <div class="fw-bold">{{ $visitRequest->visitor->full_name }}</div>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">No. KTP:</small>
                                    <div>{{ $visitRequest->visitor->identity_number }}</div>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">Institusi:</small>
                                    <div>{{ $visitRequest->visitor->institution }}</div>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted mt-2">Data visitor tidak dapat diubah. Untuk mengubah data visitor, edit di halaman profil visitor.</small>
                    </div>
                    
                    <!-- Visit Details -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2 mb-3">Detail Kunjungan</h6>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label required">Divisi Tujuan</label>
                                    <select name="division_id" class="form-select" required>
                                        <option value="">-- Pilih Divisi --</option>
                                        @foreach($divisions as $division)
                                        <option value="{{ $division->id }}" 
                                                {{ old('division_id', $visitRequest->division_id) == $division->id ? 'selected' : '' }}>
                                            {{ $division->division_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('division_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label required">Jenis Kunjungan</label>
                                    <select name="visit_type" class="form-select" required>
                                        <option value="">-- Pilih Jenis --</option>
                                        <option value="kunjungan" {{ old('visit_type', $visitRequest->visit_type) == 'kunjungan' ? 'selected' : '' }}>
                                            Kunjungan
                                        </option>
                                        <option value="antar_barang" {{ old('visit_type', $visitRequest->visit_type) == 'antar_barang' ? 'selected' : '' }}>
                                            Antar Barang
                                        </option>
                                        <option value="ambil_barang" {{ old('visit_type', $visitRequest->visit_type) == 'ambil_barang' ? 'selected' : '' }}>
                                            Ambil Barang
                                        </option>
                                        <option value="inspeksi" {{ old('visit_type', $visitRequest->visit_type) == 'inspeksi' ? 'selected' : '' }}>
                                            Inspeksi
                                        </option>
                                        <option value="lainnya" {{ old('visit_type', $visitRequest->visit_type) == 'lainnya' ? 'selected' : '' }}>
                                            Lainnya
                                        </option>
                                    </select>
                                    @error('visit_type')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label required">Tujuan Kunjungan</label>
                            <textarea name="purpose" class="form-control" rows="3" required>{{ old('purpose', $visitRequest->purpose) }}</textarea>
                            @error('purpose')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="form-label required">Tanggal Kunjungan</label>
                                    <input type="date" name="visit_date" class="form-control" 
                                           value="{{ old('visit_date', $visitRequest->visit_date->format('Y-m-d')) }}" 
                                           min="{{ date('Y-m-d') }}" required>
                                    @error('visit_date')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="form-label required">Waktu Mulai</label>
                                    <input type="time" name="start_time" class="form-control" 
                                           value="{{ old('start_time', $visitRequest->start_time) }}" required>
                                    @error('start_time')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="form-label">Waktu Selesai (Estimasi)</label>
                                    <input type="time" name="end_time" class="form-control" 
                                           value="{{ old('end_time', $visitRequest->end_time) }}">
                                    @error('end_time')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Opsional</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Additional Info -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2 mb-3">Informasi Tambahan</h6>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Surat / Dokumen Pendukung</label>
                            @if($visitRequest->letter_path)
                            <div class="mb-2">
                                <span class="badge bg-info">
                                    <i class="bi bi-file-earmark"></i> Dokumen sudah ada
                                </span>
                                <a href="{{ Storage::url($visitRequest->letter_path) }}" target="_blank" 
                                   class="btn btn-sm btn-outline-info ms-2">
                                    Lihat Dokumen
                                </a>
                            </div>
                            <small class="text-muted">Upload baru untuk mengganti dokumen yang ada</small>
                            @endif
                            <input type="file" name="letter" class="form-control mt-2" accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted">Format: PDF, JPG, PNG (Maks: 2MB)</small>
                            @error('letter')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Perhatian:</strong> Kunjungan hanya dapat diedit selama status masih "Terdaftar".
                        Setelah visitor check-in, data tidak dapat diubah.
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('internal.visit-requests.show', $visitRequest) }}" class="btn btn-secondary">
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
                <h6 class="mb-0">
                    <i class="bi bi-info-circle me-2"></i> Informasi Kunjungan
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted">Dibuat Oleh:</small>
                    <div class="fw-bold">{{ $visitRequest->creator->name }}</div>
                </div>
                
                <div class="mb-3">
                    <small class="text-muted">Tanggal Dibuat:</small>
                    <div>{{ $visitRequest->created_at->format('d F Y H:i') }}</div>
                </div>
                
                <div class="mb-3">
                    <small class="text-muted">Terakhir Diperbarui:</small>
                    <div>{{ $visitRequest->updated_at->format('d F Y H:i') }}</div>
                </div>
                
                <div class="alert alert-info mt-4">
                    <h6>Status Saat Ini:</h6>
                    <div class="fw-bold text-center my-2">
                        <span class="badge bg-primary fs-6">{{ $visitRequest->status_label }}</span>
                    </div>
                    <p class="small mb-0">
                        @if($visitRequest->status === 'registered')
                        Visitor belum check-in. Data masih dapat diubah.
                        @else
                        Kunjungan sudah diproses. Data tidak dapat diubah.
                        @endif
                    </p>
                </div>
                
                <div class="mt-3">
                    <a href="{{ route('internal.visitors.show', $visitRequest->visitor) }}" 
                       class="btn btn-outline-primary btn-sm w-100">
                        <i class="bi bi-person me-2"></i> Lihat Profil Visitor
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection