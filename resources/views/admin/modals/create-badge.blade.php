<div class="modal fade" id="createBadgeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.badges.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Badge Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Kode Badge</label>
                        <input type="text" name="badge_code" class="form-control" required
                               placeholder="Contoh: BG-001">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label required">Area Akses</label>
                        <input type="text" name="access_area" class="form-control" required
                               placeholder="Contoh: Gedung Utama Lantai 1-3">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label required">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="available">Tersedia</option>
                            <option value="in_use">Digunakan</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Badge</button>
                </div>
            </form>
        </div>
    </div>
</div>