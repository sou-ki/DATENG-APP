{{-- resources/views/internal/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard Internal')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard Internal</h1>
    <a href="{{ route('internal.visit-requests.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Buat Kunjungan Baru
    </a>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="card text-white bg-primary mb-3">
            <div class="card-body">
                <h5 class="card-title">Registered</h5>
                <p class="card-text display-6">{{ $counts['registered'] }}</p>
                <small>Menunggu check-in</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning mb-3">
            <div class="card-body">
                <h5 class="card-title">On Site</h5>
                <p class="card-text display-6">{{ $counts['checked_in'] }}</p>
                <small>Sedang berkunjung</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success mb-3">
            <div class="card-body">
                <h5 class="card-title">Completed</h5>
                <p class="card-text display-6">{{ $counts['checked_out'] }}</p>
                <small>Selesai minggu ini</small>
            </div>
        </div>
    </div>
</div>

<h4 class="mb-3">Kunjungan Terbaru</h4>
<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Visitor</th>
                <th>Institusi</th>
                <th>Tujuan</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recentVisits as $visit)
            <tr>
                <td>{{ $visit->visit_date->format('d/m/Y') }}</td>
                <td>{{ $visit->visitor->full_name }}</td>
                <td>{{ $visit->visitor->institution }}</td>
                <td>{{ $visit->purpose }}</td>
                <td>
                    <span class="badge bg-{{ match($visit->status) {
                        'registered' => 'primary',
                        'checked_in' => 'warning',
                        'checked_out' => 'success',
                        'rejected' => 'danger'
                    } }}">
                        {{ ucfirst(str_replace('_', ' ', $visit->status)) }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('internal.visit-requests.show', $visit) }}" class="btn btn-sm btn-outline-info">
                        <i class="bi bi-eye"></i>
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection