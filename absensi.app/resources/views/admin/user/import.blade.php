<div class="accordion mb-5" id="importData">
    <div class="card accordion-item">
        <h2 class="accordion-header" id="importDataHeader">
            <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse"
                data-bs-target="#importDataTarget" aria-expanded="false" aria-controls="importDataTarget">Import Data <i
                    class="ti ti-file-import ms-1"></i></button>
        </h2>
        <div id="importDataTarget" class="accordion-collapse collapse" data-bs-parent="#importData" style="">
            <div class="accordion-body">
                <form action="{{ route('admin.user.import') }}" method="POST" enctype="multipart/form-data"
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
