<div class="modal fade" id="editVisitorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editVisitorForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Visitor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="visitor_id" id="editVisitorId">
                    
                    <div class="mb-3">
                        <label class="form-label required">Nama Lengkap</label>
                        <input type="text" name="full_name" id="editVisitorName" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label required">Nomor KTP</label>
                        <input type="text" name="identity_number" id="editVisitorIdentity" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label required">Institusi/Perusahaan</label>
                        <input type="text" name="institution" id="editVisitorInstitution" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label required">Nomor Telepon</label>
                        <input type="text" name="phone_number" id="editVisitorPhone" class="form-control" required>
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