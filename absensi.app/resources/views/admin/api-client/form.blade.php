<div class="col-sm-12">
    <label class="form-label" for="name">Nama Klien / Aplikasi <span class="text-danger">*</span></label>
    <div class="input-group input-group-merge">
        <span class="input-group-text"><i class="ti ti-app-window"></i></span>
        <input type="text" id="name" name="name" class="form-control" placeholder="Contoh: Portal HRD / Dashboard External" required />
    </div>
</div>

<div class="col-sm-12">
    <label class="form-label" for="description">Keterangan / Catatan</label>
    <textarea id="description" name="description" class="form-control" rows="3" placeholder="Tujuan atau hak akses dari API Key ini..."></textarea>
</div>

<div class="col-sm-12">
    <label class="form-label d-block">Status Aktif</label>
    <div class="form-check form-switch mb-2">
        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
        <label class="form-check-label" for="is_active">Aktifkan API Client ini</label>
    </div>
</div>
