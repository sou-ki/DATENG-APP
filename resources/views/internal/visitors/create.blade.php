@extends('layouts.app')

@section('title', 'Tambah Visitor Baru')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('internal.visitors.index') }}">Data Visitor</a></li>
<li class="breadcrumb-item active">Tambah Baru</li>
@endsection

@section('actions')
<a href="{{ route('internal.visitors.index') }}" class="btn btn-secondary">
    <i class="bi bi-arrow-left me-2"></i> Kembali
</a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-person-plus me-2"></i> Form Tambah Visitor Baru
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('internal.visitors.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label required">Nama Lengkap</label>
                                <input type="text" name="full_name" class="form-control" 
                                       value="{{ old('full_name') }}" required
                                       placeholder="Nama lengkap sesuai KTP">
                                @error('full_name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label required">Nomor KTP</label>
                                <input type="text" name="identity_number" class="form-control" 
                                       value="{{ old('identity_number') }}" required
                                       placeholder="16 digit nomor KTP">
                                @error('identity_number')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Pastikan nomor KTP valid dan belum terdaftar</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label required">Institusi/Perusahaan</label>
                                <input type="text" name="institution" class="form-control" 
                                       value="{{ old('institution') }}" required
                                       placeholder="Nama perusahaan/instansi">
                                @error('institution')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label required">Nomor Telepon</label>
                                <input type="text" name="phone_number" class="form-control" 
                                       value="{{ old('phone_number') }}" required
                                       placeholder="08xxxxxxxxxx">
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
                                       value="{{ old('email') }}"
                                       placeholder="email@perusahaan.com">
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea name="address" class="form-control" rows="1"
                                          placeholder="Alamat lengkap (opsional)">{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('internal.visitors.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-2"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i> Simpan Visitor
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
                        <li>Pastikan data visitor akurat dan valid</li>
                        <li>Nomor KTP harus unik untuk setiap visitor</li>
                        <li>Visitor yang sama dapat berkunjung berkali-kali</li>
                        <li>Data visitor dapat diedit nanti jika diperlukan</li>
                    </ul>
                </div>
                
                <div class="mt-3">
                    <h6>Manfaat Data Visitor:</h6>
                    <ul class="small text-muted">
                        <li>Mempercepat pembuatan kunjungan berikutnya</li>
                        <li>Tracking riwayat kunjungan</li>
                        <li>Analytics dan reporting</li>
                        <li>Pengelolaan data terpusat</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection