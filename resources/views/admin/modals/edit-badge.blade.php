<div class="modal fade" id="editBadgeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editBadgeForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Badge</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="badge_id" id="editBadgeId">
                    
                    <div class="mb-3">
                        <label class="form-label required">Kode Badge</label>
                        <input type="text" name="badge_code" id="editBadgeCode" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label required">Area Akses</label>
                        <input type="text" name="access_area" id="editBadgeArea" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label required">Status</label>
                        <select name="status" id="editBadgeStatus" class="form-select" required>
                            <option value="available">Tersedia</option>
                            <option value="in_use">Digunakan</option>
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