@extends('layouts.app')

@section('title', 'Kelola User')

@section('content')
<div class="row">
    <div class="col-md-8">
        <!-- Add User Form (Collapsible) -->
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Tambah User Baru</h5>
                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#addUserForm">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </div>
            </div>
            <div class="collapse" id="addUserForm">
                <div class="card-body">
                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required">Nama</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required">Email</label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required">Password</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required">Konfirmasi Password</label>
                                    <input type="password" name="password_confirmation" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required">Role</label>
                                    <select name="role" class="form-select" required>
                                        <option value="internal">Internal</option>
                                        <option value="security">Security</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Divisi (jika Internal)</label>
                                    <select name="division_id" class="form-select">
                                        <option value="">-- Pilih Divisi --</option>
                                        @foreach($divisions as $division)
                                        <option value="{{ $division->id }}">{{ $division->division_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan User</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- User List with Search -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daftar User</h5>
                    
                    <div class="d-flex">
                        <form method="GET" action="{{ route('admin.users') }}" class="me-2">
                            <div class="input-group input-group-sm">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Cari..." value="{{ $search ?? '' }}">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </form>
                        
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('admin.users') }}" 
                               class="btn btn-outline-secondary {{ !$role ? 'active' : '' }}">All</a>
                            <a href="{{ route('admin.users', ['role' => 'internal']) }}" 
                               class="btn btn-outline-info {{ $role == 'internal' ? 'active' : '' }}">Internal</a>
                            <a href="{{ route('admin.users', ['role' => 'security']) }}" 
                               class="btn btn-outline-warning {{ $role == 'security' ? 'active' : '' }}">Security</a>
                            <a href="{{ route('admin.users', ['role' => 'admin']) }}" 
                               class="btn btn-outline-danger {{ $role == 'admin' ? 'active' : '' }}">Admin</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($users->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Divisi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'security' ? 'warning' : 'info') }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td>{{ $user->division->division_name ?? '-' }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <!-- Edit Button (triggers modal) -->
                                        <button type="button" class="btn btn-outline-warning btn-sm" 
                                                data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        
                                        <!-- Delete Button -->
                                        @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                              onsubmit="return confirm('Hapus user ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                    
                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('admin.users.update', $user) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit User</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Nama</label>
                                                            <input type="text" name="name" class="form-control" 
                                                                   value="{{ $user->name }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Email</label>
                                                            <input type="email" name="email" class="form-control" 
                                                                   value="{{ $user->email }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Password Baru (kosongkan jika tidak diubah)</label>
                                                            <input type="password" name="password" class="form-control">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Konfirmasi Password</label>
                                                            <input type="password" name="password_confirmation" class="form-control">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Role</label>
                                                            <select name="role" class="form-select" required>
                                                                <option value="internal" {{ $user->role == 'internal' ? 'selected' : '' }}>Internal</option>
                                                                <option value="security" {{ $user->role == 'security' ? 'selected' : '' }}>Security</option>
                                                                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Divisi</label>
                                                            <select name="division_id" class="form-select">
                                                                <option value="">-- Pilih Divisi --</option>
                                                                @foreach($divisions as $division)
                                                                <option value="{{ $division->id }}" 
                                                                        {{ $user->division_id == $division->id ? 'selected' : '' }}>
                                                                    {{ $division->division_name }}
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    {{ $users->links() }}
                </div>
                @else
                <div class="text-center py-4">
                    <p class="text-muted">Tidak ada user</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Statistics -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Statistik User</h6>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between">
                        <span>Total User:</span>
                        <span class="fw-bold">{{ \App\Models\User::count() }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between">
                        <span>Internal:</span>
                        <span class="fw-bold text-info">{{ \App\Models\User::where('role', 'internal')->count() }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between">
                        <span>Security:</span>
                        <span class="fw-bold text-warning">{{ \App\Models\User::where('role', 'security')->count() }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between">
                        <span>Admin:</span>
                        <span class="fw-bold text-danger">{{ \App\Models\User::where('role', 'admin')->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tips -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Tips</h6>
            </div>
            <div class="card-body">
                <ul class="small text-muted mb-0">
                    <li>Admin bisa mengatur semua user</li>
                    <li>Internal harus memiliki divisi</li>
                    <li>Security dan Admin tidak butuh divisi</li>
                    <li>Password minimal 8 karakter</li>
                    <li>User dengan riwayat kunjungan tidak bisa dihapus</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection