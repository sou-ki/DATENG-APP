@extends('layouts.app')

@section('title', 'Data Visitor')

@section('actions')
<div class="btn-group">
    <a href="{{ route('internal.visitors.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i> Tambah Visitor
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Data Visitor</h5>
                    
                    <!-- Search Form -->
                    <form method="GET" action="{{ route('internal.visitors.index') }}" class="d-flex" style="width: 300px;">
                        <div class="input-group">
                            <input type="text" 
                                   name="search" 
                                   class="form-control" 
                                   placeholder="Cari nama / KTP / institusi..." 
                                   value="{{ $search ?? '' }}">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                            @if($search)
                            <a href="{{ route('internal.visitors.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x"></i>
                            </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-body">
                @if($visitors->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>No. KTP</th>
                                <th>Institusi</th>
                                <th>Telepon</th>
                                <th>Total Kunjungan</th>
                                <th>Terakhir Berkunjung</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($visitors as $visitor)
                            @php
                                $latestVisit = $visitor->visitRequests()
                                    ->orderBy('visit_date', 'desc')
                                    ->first();
                            @endphp
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $visitor->full_name }}</div>
                                    @if($visitor->email)
                                    <small class="text-muted">{{ $visitor->email }}</small>
                                    @endif
                                </td>
                                <td>{{ $visitor->identity_number }}</td>
                                <td>{{ $visitor->institution }}</td>
                                <td>{{ $visitor->phone_number }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ $visitor->visit_requests_count }}</span>
                                </td>
                                <td>
                                    @if($latestVisit)
                                    <div>{{ $latestVisit->visit_date->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ $latestVisit->status_label }}</small>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('internal.visitors.show', $visitor) }}" 
                                           class="btn btn-outline-info" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('internal.visitors.edit', $visitor) }}" 
                                           class="btn btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="{{ route('internal.visit-requests.create') }}?visitor_id={{ $visitor->id }}" 
                                           class="btn btn-outline-success" title="Buat Kunjungan">
                                            <i class="bi bi-plus-lg"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    {{ $visitors->links() }}
                </div>
                @else
                <div class="text-center py-5">
                    @if($search)
                    <i class="bi bi-search display-4 text-muted"></i>
                    <h5 class="text-muted mt-3">Tidak ditemukan</h5>
                    <p class="text-muted">Tidak ada visitor yang sesuai dengan pencarian "{{ $search }}"</p>
                    <a href="{{ route('internal.visitors.index') }}" class="btn btn-outline-primary mt-2">
                        Tampilkan Semua
                    </a>
                    @else
                    <i class="bi bi-people display-4 text-muted"></i>
                    <h5 class="text-muted mt-3">Belum ada data visitor</h5>
                    <p class="text-muted">Mulai dengan menambahkan visitor baru</p>
                    <a href="{{ route('internal.visitors.create') }}" class="btn btn-primary mt-2">
                        <i class="bi bi-plus-circle me-2"></i> Tambah Visitor
                    </a>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection