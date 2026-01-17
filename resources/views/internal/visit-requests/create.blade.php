@extends('layouts.app')

@section('title', 'Buat Kunjungan Baru')

@push('styles')
<style>
    .visitor-search-results {
        max-height: 200px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        display: none;
        position: absolute;
        width: 100%;
        background: white;
        z-index: 1000;
    }
    .visitor-item {
        padding: 8px 12px;
        cursor: pointer;
        border-bottom: 1px solid #eee;
    }
    .visitor-item:hover {
        background-color: #f8f9fa;
    }
    .visitor-item.active {
        background-color: #e7f1ff;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-plus-circle me-2"></i> Form Kunjungan Baru
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('internal.visit-requests.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Visitor Selection -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2 mb-3">Data Visitor</h6>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label required">Cari Visitor</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="visitorSearch"
                                           placeholder="Ketik nama atau nomor KTP...">
                                    <div class="visitor-search-results" id="visitorResults"></div>
                                    <small class="text-muted">Cari visitor yang sudah terdaftar</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label required">Pilih Visitor</label>
                                    <select name="visitor_id" id="visitorSelect" class="form-select" required>
                                        <option value="">-- Pilih Visitor --</option>
                                        @foreach($visitors as $visitor)
                                        <option value="{{ $visitor->id }}" 
                                                data-ktp="{{ $visitor->identity_number }}"
                                                data-institution="{{ $visitor->institution }}">
                                            {{ $visitor->full_name }} ({{ $visitor->identity_number }})
                                        </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Atau pilih dari daftar</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="visitor-info card bg-light p-3 mt-3" id="visitorInfo" style="display: none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted">Nama:</small>
                                    <div id="selectedVisitorName" class="fw-bold"></div>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">No. KTP:</small>
                                    <div id="selectedVisitorKtp"></div>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">Institusi:</small>
                                    <div id="selectedVisitorInstitution"></div>
                                </div>
                            </div>
                        </div>
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
                                                {{ old('division_id') == $division->id ? 'selected' : '' }}>
                                            {{ $division->division_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label required">Jenis Kunjungan</label>
                                    <select name="visit_type" class="form-select" required>
                                        <option value="">-- Pilih Jenis --</option>
                                        <option value="kunjungan" {{ old('visit_type') == 'kunjungan' ? 'selected' : '' }}>
                                            Kunjungan
                                        </option>
                                        <option value="antar_barang" {{ old('visit_type') == 'antar_barang' ? 'selected' : '' }}>
                                            Antar Barang
                                        </option>
                                        <option value="ambil_barang" {{ old('visit_type') == 'ambil_barang' ? 'selected' : '' }}>
                                            Ambil Barang
                                        </option>
                                        <option value="inspeksi" {{ old('visit_type') == 'inspeksi' ? 'selected' : '' }}>
                                            Inspeksi
                                        </option>
                                        <option value="lainnya" {{ old('visit_type') == 'lainnya' ? 'selected' : '' }}>
                                            Lainnya
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label required">Tujuan Kunjungan</label>
                            <textarea name="purpose" class="form-control" rows="3" 
                                      placeholder="Jelaskan tujuan kunjungan..." required>{{ old('purpose') }}</textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="form-label required">Tanggal Kunjungan</label>
                                    <input type="date" name="visit_date" class="form-control" 
                                           value="{{ old('visit_date', date('Y-m-d')) }}" 
                                           min="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="form-label required">Waktu Mulai</label>
                                    <input type="time" name="start_time" class="form-control" 
                                           value="{{ old('start_time', '09:00') }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="form-label">Waktu Selesai (Estimasi)</label>
                                    <input type="time" name="end_time" class="form-control" 
                                           value="{{ old('end_time', '10:00') }}">
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
                            <input type="file" name="letter" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted">Format: PDF, JPG, PNG (Maks: 2MB)</small>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('internal.visit-requests.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i> Simpan Kunjungan
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
                    <i class="bi bi-info-circle me-2"></i> Informasi
                </h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6><i class="bi bi-lightbulb me-2"></i> Tips:</h6>
                    <ul class="mb-0">
                        <li>Pastikan data visitor sudah benar</li>
                        <li>Pilih divisi tujuan dengan tepat</li>
                        <li>Jelaskan tujuan kunjungan dengan jelas</li>
                        <li>Visitor akan dapat check-in di pos security</li>
                    </ul>
                </div>
                
                <div class="mt-3">
                    <h6>Status Flow:</h6>
                    <div class="d-flex align-items-center mb-2">
                        <div class="badge bg-primary rounded-circle p-2 me-2">1</div>
                        <span>Terdaftar (Created)</span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <div class="badge bg-secondary rounded-circle p-2 me-2">2</div>
                        <span>Check-in (Security)</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="badge bg-secondary rounded-circle p-2 me-2">3</div>
                        <span>Check-out (Selesai)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('visitorSearch');
    const resultsDiv = document.getElementById('visitorResults');
    const visitorSelect = document.getElementById('visitorSelect');
    const visitorInfo = document.getElementById('visitorInfo');
    
    // Visitor search functionality
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        if (query.length < 2) {
            resultsDiv.style.display = 'none';
            return;
        }
        
        fetch(`/api/visitors/search?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                resultsDiv.innerHTML = '';
                
                if (data.length === 0) {
                    resultsDiv.innerHTML = '<div class="visitor-item text-muted p-3">Tidak ditemukan</div>';
                } else {
                    data.forEach(visitor => {
                        const item = document.createElement('div');
                        item.className = 'visitor-item';
                        item.innerHTML = `
                            <div class="fw-bold">${visitor.full_name}</div>
                            <small class="text-muted">${visitor.identity_number} - ${visitor.institution}</small>
                        `;
                        item.onclick = () => selectVisitor(visitor);
                        resultsDiv.appendChild(item);
                    });
                }
                
                resultsDiv.style.display = 'block';
            });
    });
    
    function selectVisitor(visitor) {
        // Set select value
        visitorSelect.value = visitor.id;
        
        // Show visitor info
        document.getElementById('selectedVisitorName').textContent = visitor.full_name;
        document.getElementById('selectedVisitorKtp').textContent = visitor.identity_number;
        document.getElementById('selectedVisitorInstitution').textContent = visitor.institution;
        visitorInfo.style.display = 'block';
        
        // Hide results
        resultsDiv.style.display = 'none';
        searchInput.value = '';
    }
    
    // Handle select change
    visitorSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (selectedOption.value) {
            document.getElementById('selectedVisitorName').textContent = selectedOption.text.split(' (')[0];
            document.getElementById('selectedVisitorKtp').textContent = selectedOption.dataset.ktp;
            document.getElementById('selectedVisitorInstitution').textContent = selectedOption.dataset.institution;
            visitorInfo.style.display = 'block';
        } else {
            visitorInfo.style.display = 'none';
        }
    });
    
    // Close results when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
            resultsDiv.style.display = 'none';
        }
    });
});
</script>
@endpush