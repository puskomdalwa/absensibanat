<div class="accordion mb-4" id="importData">
    <div class="card accordion-item border-0 shadow-sm" style="border-radius: 16px; border: 1px solid rgba(251, 113, 133, 0.25) !important;">
        <h2 class="accordion-header" id="importDataHeader">
            <button type="button" class="accordion-button collapsed fw-semibold" data-bs-toggle="collapse"
                data-bs-target="#importDataTarget" aria-expanded="false" aria-controls="importDataTarget" style="border-radius: 16px;">
                <i class="ti ti-file-import me-2" style="color: #e11d48;"></i>
                <span>Import Data Presensi Mesin Fingerprint</span>
            </button>
        </h2>
        <div id="importDataTarget" class="accordion-collapse collapse" data-bs-parent="#importData" style="">
            <div class="accordion-body">
                <form action="{{ route('admin.absensi.import') }}" method="POST" enctype="multipart/form-data"
                    id="formImport">
                    @csrf
                    <div class="form-group mb-3">
                        <input class="form-control mb-3" type="text" id="initUserId" name="init_user_id"
                            placeholder="Diisi dengan id user pertama import" required>
                    </div>
                    <div class="form-group mb-3">
                        <input class="form-control mb-3" type="file" id="formFile" name="file">
                        <small>*Silahkan upload file sesuai format dari mesin fingerprint.</small><br>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-primary" type="submit">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $('#formImport').submit(function(e) {
            e.preventDefault();
            ajaxRequestDt(e, false, dataTable);
        });
    </script>
@endpush
