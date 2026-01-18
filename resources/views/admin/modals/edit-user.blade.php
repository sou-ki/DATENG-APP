<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editUserForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="user_id" id="editUserId">
                    
                    <div class="mb-3">
                        <label class="form-label required">Nama Lengkap</label>
                        <input type="text" name="name" id="editUserName" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label required">Email</label>
                        <input type="email" name="email" id="editUserEmail" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="password" class="form-control" minlength="8">
                        <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label required">Role</label>
                        <select name="role" class="form-select" required id="editUserRoleSelect">
                            <option value="internal">Internal</option>
                            <option value="security">Security</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    
                    <div class="mb-3" id="editDivisionField">
                        <label class="form-label">Divisi</label>
                        <select name="division_id" class="form-select" id="editUserDivision">
                            <option value="">-- Pilih Divisi --</option>
                            @foreach($divisions as $division)
                            <option value="{{ $division->id }}">{{ $division->division_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>