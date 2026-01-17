{{-- resources/views/security/checkin.blade.php --}}
@extends('layouts.app')

@section('title', 'Check-in Visitor')

@push('styles')
<style>
    .search-box {
        position: relative;
    }
    .search-results {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #ddd;
        border-radius: 0 0 4px 4px;
        max-height: 300px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
    }
    .search-item {
        padding: 10px;
        border-bottom: 1px solid #eee;
        cursor: pointer;
    }
    .search-item:hover {
        background: #f8f9fa;
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Check-in Visitor</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('security.checkout') }}" class="btn btn-outline-secondary">
            <i class="bi bi-box-arrow-right"></i> Ke Check-out
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Cari Kunjungan</h5>
            </div>
            <div class="card-body">
                <div class="search-box">
                    <input type="text" 
                           id="searchVisitor" 
                           class="form-control form-control-lg" 
                           placeholder="Scan ID/Nama/Institusi..."
                           autofocus>
                    <div id="searchResults" class="search-results"></div>
                </div>
                
                <div id="visitorDetails" class="mt-4" style="display: none;">
                    <h5>Detail Kunjungan</h5>
                    <table class="table table-sm">
                        <tr><th>Nama:</th><td id="detailName"></td></tr>
                        <tr><th>Institusi:</th><td id="detailInstitution"></td></tr>
                        <tr><th>KTP:</th><td id="detailKtp"></td></tr>
                        <tr><th>Tujuan:</th><td id="detailPurpose"></td></tr>
                        <tr><th>Bertemu:</th><td id="detailDivision"></td></tr>
                    </table>
                    
                    <form id="checkinForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Pilih Badge</label>
                            <select name="badge_id" class="form-select" required>
                                <option value="">-- Pilih Badge --</option>
                                @foreach($availableBadges as $badge)
                                <option value="{{ $badge->id }}">
                                    {{ $badge->badge_code }} - {{ $badge->access_area }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Catatan (opsional)</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-check-circle"></i> PROSES CHECK-IN
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Antrian Check-in Hari Ini</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Nama</th>
                                <th>Institusi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="queueList">
                            <!-- AJAX akan mengisi ini -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// AJAX untuk live search
document.getElementById('searchVisitor').addEventListener('input', function(e) {
    const query = e.target.value;
    if (query.length < 2) {
        document.getElementById('searchResults').style.display = 'none';
        return;
    }
    
    fetch(`/api/security/search-visits?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            const results = document.getElementById('searchResults');
            results.innerHTML = '';
            
            if (data.length === 0) {
                results.innerHTML = '<div class="search-item text-muted">Tidak ditemukan</div>';
            } else {
                data.forEach(visit => {
                    const div = document.createElement('div');
                    div.className = 'search-item';
                    div.innerHTML = `
                        <strong>${visit.visitor.full_name}</strong><br>
                        <small>${visit.visitor.institution} - ${visit.purpose}</small>
                    `;
                    div.onclick = () => selectVisit(visit);
                    results.appendChild(div);
                });
            }
            results.style.display = 'block';
        });
});

function selectVisit(visit) {
    // Isi detail
    document.getElementById('detailName').textContent = visit.visitor.full_name;
    document.getElementById('detailInstitution').textContent = visit.visitor.institution;
    document.getElementById('detailKtp').textContent = visit.visitor.identity_number;
    document.getElementById('detailPurpose').textContent = visit.purpose;
    document.getElementById('detailDivision').textContent = visit.division.division_name;
    
    // Set form action
    document.getElementById('checkinForm').action = `/security/checkin/${visit.id}`;
    
    // Tampilkan form
    document.getElementById('visitorDetails').style.display = 'block';
    document.getElementById('searchResults').style.display = 'none';
    
    // Fokus ke select badge
    document.querySelector('select[name="badge_id"]').focus();
}
</script>
@endpush
@endsection