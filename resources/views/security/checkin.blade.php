@extends('layouts.app')

@section('title', 'Check-in Visitor')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Check-in Visitor</h5>
                    <span class="badge bg-primary">
                        <i class="bi bi-people"></i> {{ $pendingVisits->count() }} Menunggu
                    </span>
                </div>
            </div>
            <div class="card-body">
                <!-- Search Form -->
                <form method="GET" action="{{ route('security.checkin') }}" class="mb-4">
                    <div class="input-group">
                        <input type="text" 
                               name="search" 
                               class="form-control" 
                               placeholder="Cari nama / KTP visitor..." 
                               value="{{ $search ?? '' }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-search"></i> Cari
                        </button>
                        @if($search)
                        <a href="{{ route('security.checkin') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x"></i>
                        </a>
                        @endif
                    </div>
                </form>
                
                @if($search && $searchResults->count() > 0)
                <!-- Search Results -->
                <div class="mb-4">
                    <h6 class="text-muted mb-3">Hasil Pencarian:</h6>
                    <div class="list-group">
                        @foreach($searchResults as $visit)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">{{ $visit->visitor->full_name }}</h6>
                                    <small class="text-muted">
                                        {{ $visit->visitor->identity_number }} | {{ $visit->visitor->institution }}
                                    </small>
                                    <div class="mt-2">
                                        <small><strong>Tujuan:</strong> {{ $visit->division->division_name }}</small><br>
                                        <small><strong>Waktu:</strong> {{ $visit->start_time }}</small>
                                    </div>
                                </div>
                                <div>
                                    <button type="button" 
                                            class="btn btn-sm btn-primary"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#checkinModal"
                                            data-visit-id="{{ $visit->id }}"
                                            data-visitor-name="{{ $visit->visitor->full_name }}">
                                        Check-in
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                
                <!-- Pending Visits List -->
                <h6 class="text-muted mb-3">Antrian Hari Ini:</h6>
                @if($pendingVisits->count() > 0)
                <div class="list-group">
                    @foreach($pendingVisits as $visit)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">{{ $visit->visitor->full_name }}</h6>
                                <small class="text-muted">
                                    {{ $visit->visitor->identity_number }} | {{ $visit->visitor->institution }}
                                </small>
                                <div class="mt-2">
                                    <small><strong>Tujuan:</strong> {{ $visit->division->division_name }}</small><br>
                                    <small><strong>Waktu:</strong> {{ $visit->start_time }}</small>
                                    <span class="badge bg-info ms-2">{{ $visit->visit_type_label }}</span>
                                </div>
                            </div>
                            <div>
                                <button type="button" 
                                        class="btn btn-sm btn-primary"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#checkinModal"
                                        data-visit-id="{{ $visit->id }}"
                                        data-visitor-name="{{ $visit->visitor->full_name }}">
                                    Check-in
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4">
                    <i class="bi bi-check-circle display-4 text-muted"></i>
                    <p class="text-muted mt-3">Tidak ada visitor yang menunggu check-in</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Status Badge</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($availableBadges as $badge)
                    <div class="col-md-6 mb-3">
                        <div class="card border-success">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $badge->badge_code }}</h6>
                                        <small class="text-muted">{{ $badge->access_area }}</small>
                                    </div>
                                    <span class="badge bg-success">Tersedia</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                @if($availableBadges->count() === 0)
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Tidak ada badge tersedia. Harap tunggu check-out visitor terlebih dahulu.
                </div>
                @endif
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Petunjuk Check-in</h5>
            </div>
            <div class="card-body">
                <ol class="mb-0">
                    <li>Verifikasi identitas visitor (KTP/SIM)</li>
                    <li>Cari nama visitor di daftar antrian</li>
                    <li>Pilih badge yang sesuai dengan area kunjungan</li>
                    <li>Serahkan badge kepada visitor</li>
                    <li>Catat jika ada keterangan khusus</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Check-in Modal -->
<div class="modal fade" id="checkinModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="checkinForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Check-in Visitor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <p>Visitor: <strong id="modalVisitorName"></strong></p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label required">Pilih Badge</label>
                        <select name="badge_id" class="form-select" required>
                            <option value="">-- Pilih Badge --</option>
                            @foreach($availableBadges as $badge)
                            <option value="{{ $badge->id }}">
                                {{ $badge->badge_code }} - {{ $badge->access_area }}
                            </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Pastikan badge sesuai area kunjungan</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-control" rows="2" 
                                  placeholder="Catatan khusus (opsional)..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Proses Check-in</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkinModal = document.getElementById('checkinModal');
    
    checkinModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const visitId = button.getAttribute('data-visit-id');
        const visitorName = button.getAttribute('data-visitor-name');
        
        // Update modal content
        document.getElementById('modalVisitorName').textContent = visitorName;
        
        // Update form action
        const form = document.getElementById('checkinForm');
        form.action = `/security/checkin/${visitId}`;
    });
});
</script>
@endpush