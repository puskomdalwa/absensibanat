<div class="accordion mb-4" id="importData">
    <div class="card accordion-item border-0 shadow-sm">
        <h2 class="accordion-header" id="importDataHeader">
            <button type="button" class="accordion-button collapsed fw-semibold text-primary" data-bs-toggle="collapse"
                data-bs-target="#importDataTarget" aria-expanded="false" aria-controls="importDataTarget">
                <i class="ti ti-file-spreadsheet me-2 fs-4"></i> Import Data Pengguna (Excel / CSV)
            </button>
        </h2>
        <div id="importDataTarget" class="accordion-collapse collapse" data-bs-parent="#importData">
            <div class="accordion-body">
                <!-- Panduan Format Import -->
                <div class="alert alert-primary border-0 mb-4" role="alert">
                    <div class="d-flex align-items-center mb-2">
                        <i class="ti ti-info-circle me-2 fs-5"></i>
                        <h6 class="alert-heading mb-0 fw-bold">Format Kolom File Import</h6>
                    </div>
                    <p class="mb-2 small">
                        Sistem mendukung import data user sekaligus <strong>Role</strong> dan <strong>Departemen</strong>. 
                        Jika Role atau Departemen belum tersedia di database, sistem akan <strong>otomatis membuatnya (auto-create)</strong>.
                    </p>
                    <div class="table-responsive bg-white rounded p-2 border">
                        <table class="table table-bordered table-sm text-center mb-0" style="font-size: 0.78rem;">
                            <thead class="table-light">
                                <tr>
                                    <th>NO</th>
                                    <th>KODE</th>
                                    <th>NAMA DOSEN</th>
                                    <th>L/P</th>
                                    <th>TTL</th>
                                    <th>E-MAIL</th>
                                    <th>HP</th>
                                    <th>STATUS</th>
                                    <th>ROLE</th>
                                    <th>DEPARTEMEN</th>
                                    <th>KODE-DEPARTEMEN</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="text-muted">
                                    <td>28</td>
                                    <td><code>80117</code></td>
                                    <td>AISYAH</td>
                                    <td><span class="badge bg-label-info">P</span></td>
                                    <td>KABUPATEN PASURUAN, 20-07-1981</td>
                                    <td>aisyah01@gmail.com</td>
                                    <td>081936926117</td>
                                    <td>AKTIF</td>
                                    <td><span class="badge bg-label-primary">user</span></td>
                                    <td>Dosen</td>
                                    <td><code>002</code></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <ul class="mb-0 mt-2 ps-3 small text-muted">
                        <li><strong>KODE</strong>: Digunakan sebagai ID/PIN login pengguna di absensi.</li>
                        <li><strong>ROLE</strong>: Jika belum ada (misal: <em>user, dosen, staff, santri</em>), otomatis dibuatkan.</li>
                        <li><strong>DEPARTEMEN & KODE-DEPARTEMEN</strong>: Jika belum ada (misal: <em>002 - Dosen</em>), otomatis didaftarkan.</li>
                        <li><strong>Password Default</strong>: User baru akan memiliki password default <code>dalwa123</code>.</li>
                    </ul>
                </div>

                <form action="{{ route('admin.user.import') }}" method="POST" enctype="multipart/form-data" id="formImport">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-medium" for="formFile">
                                File Excel / CSV <span class="text-danger">*</span>
                            </label>
                            <input class="form-control" type="file" id="formFile" name="file" accept=".xlsx,.xls,.csv" required>
                            <div class="form-text">Mendukung format file <code>.xlsx</code>, <code>.xls</code>, atau <code>.csv</code>. Header akan terdeteksi otomatis.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium" for="initUserId">
                                Mulai dari Kode / ID <span class="text-muted small">(Opsional)</span>
                            </label>
                            <input class="form-control" type="text" id="initUserId" name="init_user_id" placeholder="Kosongkan untuk proses semua">
                            <div class="form-text">Isi jika hanya ingin memproses mulai dari kode tertentu.</div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-4 pt-2 border-top">
                        <div class="btn-group">
                            <a href="{{ route('admin.user.import.template', ['format' => 'xlsx']) }}" class="btn btn-outline-success btn-sm">
                                <i class="ti ti-file-spreadsheet me-1"></i> Download Template (.xlsx)
                            </a>
                            <a href="{{ route('admin.user.import.template', ['format' => 'csv']) }}" class="btn btn-outline-secondary btn-sm">
                                <i class="ti ti-download me-1"></i> CSV
                            </a>
                        </div>
                        <button class="btn btn-primary" type="submit" id="btnSubmitImport">
                            <i class="ti ti-upload me-1"></i> Mulai Import Data
                        </button>
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
            var fileInput = document.getElementById('formFile');
            if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
                if (typeof showToastr === 'function') {
                    showToastr('error', 'Error', 'Silakan pilih file Excel / CSV terlebih dahulu.');
                } else {
                    alert('Silakan pilih file Excel / CSV terlebih dahulu.');
                }
                return false;
            }
            ajaxRequestDt(e, false, typeof dataTable !== 'undefined' ? dataTable : null);
        });
    </script>
@endpush
