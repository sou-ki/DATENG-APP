<div class="modal fade" id="createUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah User Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label required">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label required">Password</label>
                        <input type="password" name="password" class="form-control" required minlength="8">
                        <small class="text-muted">Minimal 8 karakter</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label required">Role</label>
                        <select name="role" class="form-select" required id="userRoleSelect">
                            <option value="">-- Pilih Role --</option>
                            <option value="internal">Internal</option>
                            <option value="security">Security</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    
                    <div class="mb-3" id="divisionField" style="display: none;">
                        <label class="form-label required">Divisi</label>
                        <select name="division_id" class="form-select">
                            <option value="">-- Pilih Divisi --</option>
                            @foreach($divisions as $division)
                            <option value="{{ $division->id }}">{{ $division->division_name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Hanya untuk role Internal</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan User</button>
                </div>
            </form>
        </div>
    </div>
</div>