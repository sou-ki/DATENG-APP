@extends('layouts.app')

@section('title', 'Check-out Visitor')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Check-out Visitor</h5>
                    <span class="badge bg-warning">
                        <i class="bi bi-person-badge"></i> {{ $activeVisits->count() }} Aktif
                    </span>
                </div>
            </div>
            <div class="card-body">
                <!-- Search Form -->
                <form method="GET" action="{{ route('security.checkout') }}" class="mb-4">
                    <div class="input-group">
                        <input type="text" 
                               name="search" 
                               class="form-control" 
                               placeholder="Cari nama / KTP / kode badge..." 
                               value="{{ $search ?? '' }}">
                        <button class="btn btn-warning" type="submit">
                            <i class="bi bi-search"></i> Cari
                        </button>
                        @if($search)
                        <a href="{{ route('security.checkout') }}" class="btn btn-outline-secondary">
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
                        @if($visit->badgeAssignment)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">{{ $visit->visitor->full_name }}</h6>
                                    <small class="text-muted">
                                        {{ $visit->visitor->identity_number }} | {{ $visit->visitor->institution }}
                                    </small>
                                    <div class="mt-2">
                                        <small><strong>Badge:</strong> 
                                            <span class="badge bg-info">
                                                {{ $visit->badgeAssignment->badge->badge_code ?? '-' }}
                                            </span>
                                        </small><br>
                                        <small><strong>Check-in:</strong> 
                                            {{ $visit->badgeAssignment->assigned_at ? $visit->badgeAssignment->assigned_at->format('H:i') : '-' }}
                                        </small>
                                    </div>
                                </div>
                                <div>
                                    <button type="button" 
                                            class="btn btn-sm btn-warning"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#checkoutModal"
                                            data-visit-id="{{ $visit->id }}"
                                            data-visitor-name="{{ $visit->visitor->full_name }}"
                                            data-badge-code="{{ $visit->badgeAssignment->badge->badge_code ?? '-' }}">
                                        Check-out
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>
                @endif
                
                <!-- Active Visits List -->
                <h6 class="text-muted mb-3">Kunjungan Aktif (dengan badge):</h6>
                @if($activeVisits->count() > 0)
                <div class="list-group">
                    @foreach($activeVisits as $visit)
                    @if($visit->badgeAssignment)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">{{ $visit->visitor->full_name }}</h6>
                                <small class="text-muted">
                                    {{ $visit->visitor->identity_number }} | {{ $visit->visitor->institution }}
                                </small>
                                <div class="mt-2">
                                    <div class="d-flex gap-3">
                                        <small><strong>Badge:</strong> 
                                            <span class="badge bg-info">
                                                {{ $visit->badgeAssignment->badge->badge_code ?? 'Tidak ada' }}
                                            </span>
                                        </small>
                                        <small><strong>Check-in:</strong> 
                                            {{ $visit->badgeAssignment->assigned_at ? $visit->badgeAssignment->assigned_at->format('H:i') : '-' }}
                                        </small>
                                        <small><strong>Durasi:</strong> 
                                            @if($visit->badgeAssignment->assigned_at)
                                                {{ $visit->badgeAssignment->assigned_at->diffForHumans(now(), true) }}
                                            @else
                                                -
                                            @endif
                                        </small>
                                    </div>
                                    <small><strong>Tujuan:</strong> {{ $visit->division->division_name ?? '-' }}</small>
                                </div>
                            </div>
                            <div>
                                <button type="button" 
                                        class="btn btn-sm btn-warning"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#checkoutModal"
                                        data-visit-id="{{ $visit->id }}"
                                        data-visitor-name="{{ $visit->visitor->full_name }}"
                                        data-badge-code="{{ $visit->badgeAssignment->badge->badge_code ?? '-' }}">
                                    Check-out
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>
                @else
                <div class="text-center py-4">
                    <i class="bi bi-check-circle display-4 text-muted"></i>
                    <p class="text-muted mt-3">Tidak ada kunjungan aktif</p>
                    <p class="text-muted small">Semua visitor telah check-out atau tidak memiliki badge assignment</p>
                </div>
                @endif
                
                <!-- Check for visits without badge assignment -->
                @php
                    $visitsWithoutBadge = \App\Models\VisitRequest::with('visitor')
                        ->where('status', 'checked_in')
                        ->doesntHave('badgeAssignment')
                        ->get();
                @endphp
                
                @if($visitsWithoutBadge->count() > 0)
                <div class="alert alert-danger mt-4">
                    <h6><i class="bi bi-exclamation-triangle me-2"></i> Kunjungan Tanpa Badge</h6>
                    <p class="mb-2">Beberapa visitor check-in tanpa badge assignment:</p>
                    <ul class="mb-0">
                        @foreach($visitsWithoutBadge as $visit)
                        <li>
                            {{ $visit->visitor->full_name }} - {{ $visit->division->division_name }}
                            <a href="{{ route('security.checkin') }}?search={{ $visit->visitor->identity_number }}" 
                               class="btn btn-sm btn-outline-danger ms-2">
                                <i class="bi bi-tag"></i> Assign Badge
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Badge Status -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Status Badge</h5>
            </div>
            <div class="card-body">
                @php
                    $totalBadges = \App\Models\Badge::count();
                    $availableBadges = \App\Models\Badge::where('status', 'available')->count();
                    $inUseBadges = \App\Models\Badge::where('status', 'in_use')->count();
                @endphp
                
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Total Badge:</span>
                        <span class="fw-bold">{{ $totalBadges }}</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: {{ $totalBadges > 0 ? ($availableBadges/$totalBadges)*100 : 0 }}%"></div>
                        <div class="progress-bar bg-warning" style="width: {{ $totalBadges > 0 ? ($inUseBadges/$totalBadges)*100 : 0 }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <small class="text-success">
                            <i class="bi bi-circle-fill"></i> Tersedia: {{ $availableBadges }}
                        </small>
                        <small class="text-warning">
                            <i class="bi bi-circle-fill"></i> Digunakan: {{ $inUseBadges }}
                        </small>
                    </div>
                </div>
                
                <!-- Recent Returns -->
                <h6 class="border-bottom pb-2 mb-3">Pengembalian Terakhir</h6>
                @php
                    $recentReturns = \App\Models\BadgeAssignment::with(['badge', 'visitRequest.visitor'])
                        ->whereNotNull('returned_at')
                        ->orderBy('returned_at', 'desc')
                        ->limit(5)
                        ->get();
                @endphp
                
                @if($recentReturns->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($recentReturns as $return)
                    <div class="list-group-item px-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="fw-bold">{{ $return->badge->badge_code }}</small><br>
                                <small class="text-muted">{{ $return->visitRequest->visitor->full_name }}</small>
                            </div>
                            <small class="text-muted">{{ $return->returned_at->format('H:i') }}</small>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-3">
                    <i class="bi bi-clock-history display-4 text-muted"></i>
                    <p class="text-muted mt-2">Belum ada pengembalian</p>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Checkout Guidelines -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">Petunjuk Check-out</h6>
            </div>
            <div class="card-body">
                <ol class="mb-0">
                    <li>Verifikasi identitas visitor</li>
                    <li>Periksa kondisi badge (apakah rusak/hilang)</li>
                    <li>Scan/input kode badge</li>
                    <li>Konfirmasi waktu check-out</li>
                    <li>Catat kondisi khusus jika ada</li>
                </ol>
                
                <div class="alert alert-warning mt-3">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <small>Pastikan visitor mengembalikan badge sebelum meninggalkan area.</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Check-out Modal -->
<div class="modal fade" id="checkoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="checkoutForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Check-out Visitor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <p>Visitor: <strong id="modalVisitorName"></strong></p>
                        <p>Badge: <span class="badge bg-info" id="modalBadgeCode"></span></p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Pemeriksaan Kondisi Badge</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="condition_check" 
                                   id="conditionGood" value="Baik" checked>
                            <label class="form-check-label" for="conditionGood">
                                Baik (Tidak rusak)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="condition_check" 
                                   id="conditionDamaged" value="Rusak ringan">
                            <label class="form-check-label" for="conditionDamaged">
                                Rusak Ringan
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="condition_check" 
                                   id="conditionLost" value="Hilang">
                            <label class="form-check-label" for="conditionLost">
                                Hilang
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-control" rows="2" 
                                  placeholder="Catatan khusus (opsional)..."></textarea>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Waktu check-out akan direkam secara otomatis.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Proses Check-out</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkoutModal = document.getElementById('checkoutModal');
    
    checkoutModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const visitId = button.getAttribute('data-visit-id');
        const visitorName = button.getAttribute('data-visitor-name');
        const badgeCode = button.getAttribute('data-badge-code');
        
        // Update modal content
        document.getElementById('modalVisitorName').textContent = visitorName;
        document.getElementById('modalBadgeCode').textContent = badgeCode;
        
        // Update form action
        const form = document.getElementById('checkoutForm');
        form.action = `/security/checkout/${visitId}`;
    });
});
</script>
@endpush