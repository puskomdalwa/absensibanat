@extends('layouts.admin.template')
@section('title', 'Fingerspot Cloud API Hub')

@push('css')
    <style>
        .nav-tabs .nav-link {
            font-weight: 600;
            padding: 0.75rem 1.25rem;
        }
        .nav-tabs .nav-link i {
            margin-right: 0.5rem;
        }
        .stat-card-icon {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
        }
        .cursor-pointer {
            cursor: pointer;
        }
    </style>
@endpush

@section('content')
    {{-- Header Banner & Stats --}}
    <div class="card bg-gradient-primary text-white mb-4 border-0 shadow-sm" style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-lg-8 col-md-7 mb-3 mb-md-0">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-white bg-opacity-20 p-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="ti ti-fingerprint ti-xl text-white"></i>
                        </div>
                        <div>
                            <h4 class="text-white mb-1 fw-bold">Fingerspot Developer API Hub</h4>
                            <p class="mb-0 text-white-50 small">
                                Gateway kontrol REST API & Webhook dua arah untuk perangkat absensi biometrik Fingerspot (Online SDK).
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-5 text-md-end">
                    <div class="d-inline-flex flex-column align-items-md-end gap-1">
                        <button type="button" class="btn btn-light btn-sm text-primary fw-bold shadow-sm" id="btn-test-connection">
                            <i class="ti ti-activity me-1"></i> Cek Koneksi Cloud API
                        </button>
                        <small class="text-white-50" id="connection-status-text">Base URL: {{ $apiUrl }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Overview Stats Row --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small d-block">Mesin Terdaftar</span>
                        <h4 class="mb-0 fw-bold text-dark mt-1">{{ $totalDevices }}</h4>
                        <small class="text-primary"><i class="ti ti-cpu me-1"></i>Active Hardware</small>
                    </div>
                    <div class="stat-card-icon bg-label-primary">
                        <i class="ti ti-devices ti-md"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small d-block">Scan Hari Ini (Live)</span>
                        <h4 class="mb-0 fw-bold text-success mt-1">{{ $todayScans }}</h4>
                        <small class="text-success"><i class="ti ti-check me-1"></i>Absensi Tercatat</small>
                    </div>
                    <div class="stat-card-icon bg-label-success">
                        <i class="ti ti-calendar-event ti-md"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small d-block">User di Mesin (Cache)</span>
                        <h4 class="mb-0 fw-bold text-info mt-1">{{ $totalDeviceUsers }}</h4>
                        <small class="text-info"><i class="ti ti-users me-1"></i>Terdata di Mesin</small>
                    </div>
                    <div class="stat-card-icon bg-label-info">
                        <i class="ti ti-user-check ti-md"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small d-block">Perintah & Webhook Hari Ini</span>
                        <h4 class="mb-0 fw-bold text-warning mt-1">{{ $todayCommands }}</h4>
                        <small class="text-muted"><i class="ti ti-history me-1"></i>Audit Trail</small>
                    </div>
                    <div class="stat-card-icon bg-label-warning">
                        <i class="ti ti-broadcast ti-md"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Tabbed Container --}}
    <div class="nav-align-top mb-4">
        <ul class="nav nav-tabs nav-fill shadow-sm rounded-top" role="tablist">
            <li class="nav-item">
                <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#tab-devices" aria-controls="tab-devices" aria-selected="true">
                    <i class="ti ti-devices"></i> Perangkat Mesin
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-attlog" aria-controls="tab-attlog" aria-selected="false">
                    <i class="ti ti-calendar-time"></i> Tarik & Sinkron Absensi
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-users" aria-controls="tab-users" aria-selected="false">
                    <i class="ti ti-users"></i> Pengguna Mesin
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-logs" aria-controls="tab-logs" aria-selected="false">
                    <i class="ti ti-activity"></i> Log & Webhook
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-playground" aria-controls="tab-playground" aria-selected="false">
                    <i class="ti ti-terminal-2"></i> API Playground
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-docs" aria-controls="tab-docs" aria-selected="false">
                    <i class="ti ti-book"></i> Dokumentasi
                </button>
            </li>
        </ul>

        <div class="tab-content bg-white p-4 shadow-sm rounded-bottom border-top-0">
            {{-- Tab 1: Perangkat --}}
            <div class="tab-pane fade show active" id="tab-devices" role="tabpanel">
                @include('admin.fingerspot.tabs.devices')
            </div>

            {{-- Tab 2: Tarik Absensi --}}
            <div class="tab-pane fade" id="tab-attlog" role="tabpanel">
                @include('admin.fingerspot.tabs.attlog')
            </div>

            {{-- Tab 3: Pengguna Mesin --}}
            <div class="tab-pane fade" id="tab-users" role="tabpanel">
                @include('admin.fingerspot.tabs.users')
            </div>

            {{-- Tab 4: Log & Webhook --}}
            <div class="tab-pane fade" id="tab-logs" role="tabpanel">
                @include('admin.fingerspot.tabs.logs')
            </div>

            {{-- Tab 5: API Playground --}}
            <div class="tab-pane fade" id="tab-playground" role="tabpanel">
                @include('admin.fingerspot.tabs.playground')
            </div>

            {{-- Tab 6: Dokumentasi --}}
            <div class="tab-pane fade" id="tab-docs" role="tabpanel">
                @include('admin.fingerspot.tabs.docs')
            </div>
        </div>
    </div>

    {{-- Include All Modals --}}
    @include('admin.fingerspot.modals.modals')
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // ----------------------------------------------------
    // SETUP CSRF TOKEN FOR ALL AJAX
    // ----------------------------------------------------
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // ----------------------------------------------------
    // INITIALIZE FLATPICKR DATEPICKERS
    // ----------------------------------------------------
    if (typeof flatpickr !== 'undefined') {
        $('.date-picker').flatpickr({
            dateFormat: 'Y-m-d',
            allowInput: true
        });
    }

    // ----------------------------------------------------
    // COPY TO CLIPBOARD HELPER
    // ----------------------------------------------------
    $(document).on('click', '.btn-copy', function() {
        var text = $(this).attr('data-clipboard-text') || $(this).data('clipboard-text');
        if (!text) return;

        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(function() {
                showToastr('success', 'Berhasil', 'Teks telah disalin ke clipboard.');
            }).catch(function() {
                fallbackCopy(text);
            });
        } else {
            fallbackCopy(text);
        }
    });

    function fallbackCopy(text) {
        var temp = $("<textarea>");
        $("body").append(temp);
        temp.val(text).select();
        document.execCommand("copy");
        temp.remove();
        showToastr('success', 'Berhasil', 'Teks telah disalin ke clipboard.');
    }

    // ----------------------------------------------------
    // TEST CONNECTION BUTTON
    // ----------------------------------------------------
    $('#btn-test-connection').on('click', function() {
        var btn = $(this);
        var origHtml = btn.html();
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status"></span> Menguji...');

        $.ajax({
            url: "{{ route('admin.fingerspot.test_connection') }}",
            type: "GET",
            success: function(res) {
                btn.prop('disabled', false).html(origHtml);
                if (res.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Koneksi Cloud API Terhubung!',
                        html: `<p class="mb-1">${res.message}</p>
                               <div class="text-start bg-light p-2 rounded small font-monospace">
                                   <strong>Mesin Pengujian:</strong> ${res.device} (${res.cloud_id})<br>
                                   <strong>Latensi Jaringan:</strong> ${res.latency_ms} ms<br>
                                   <strong>Status:</strong> Terhubung ke Cloud Fingerspot
                               </div>`,
                        customClass: { confirmButton: 'btn btn-primary' },
                        buttonsStyling: false
                    });
                    $('#connection-status-text').html(`<span class="text-white"><i class="ti ti-circle-check text-success me-1"></i>Terhubung (${res.latency_ms} ms)</span>`);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Koneksi Gagal',
                        text: res.message,
                        customClass: { confirmButton: 'btn btn-primary' },
                        buttonsStyling: false
                    });
                }
            },
            error: function(err) {
                btn.prop('disabled', false).html(origHtml);
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Jaringan / Server',
                    text: err.responseJSON ? err.responseJSON.message : 'Tidak dapat menghubungi server Fingerspot.',
                    customClass: { confirmButton: 'btn btn-primary' },
                    buttonsStyling: false
                });
            }
        });
    });

    // ----------------------------------------------------
    // TAB 1: DATATABLES PERANGKAT
    // ----------------------------------------------------
    var tableDevices = $('#table-devices').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.fingerspot.devices.data') }}",
        columns: [
            { data: 'id', name: 'id' },
            { 
                data: 'name', 
                name: 'name',
                render: function(data, type, row) {
                    return `<div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2 bg-label-primary rounded p-1 d-flex align-items-center justify-content-center">
                                    <i class="ti ti-device-laptop ti-xs"></i>
                                </div>
                                <span class="fw-semibold">${data}</span>
                            </div>`;
                }
            },
            { 
                data: 'cloud_id', 
                name: 'cloud_id',
                render: function(data) {
                    return `<div class="d-flex align-items-center">
                                <code class="me-2 text-primary fw-bold">${data}</code>
                                <button type="button" class="btn btn-xs btn-icon btn-text-secondary rounded-pill btn-copy" data-clipboard-text="${data}" title="Salin Cloud ID">
                                    <i class="ti ti-copy ti-xs"></i>
                                </button>
                            </div>`;
                }
            },
            { data: 'user_count', name: 'user_count', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[0, 'asc']]
    });

    $('#btn-refresh-devices').on('click', function() {
        tableDevices.ajax.reload(null, false);
    });

    // Tambah Perangkat
    $('#form-add-device').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

        $.ajax({
            url: "{{ route('admin.fingerspot.devices.store') }}",
            type: "POST",
            data: form.serialize(),
            success: function(res) {
                submitBtn.prop('disabled', false).html('<i class="ti ti-check me-1"></i> Simpan Mesin');
                $('#modal-add-device').modal('hide');
                form[0].reset();
                showToastr('success', 'Sukses', res.message);
                tableDevices.ajax.reload(null, false);
            },
            error: function(err) {
                submitBtn.prop('disabled', false).html('<i class="ti ti-check me-1"></i> Simpan Mesin');
                var msg = err.responseJSON ? err.responseJSON.message : 'Terjadi kesalahan sistem.';
                Swal.fire({ icon: 'error', title: 'Gagal Menambah Perangkat', text: msg, customClass: { confirmButton: 'btn btn-primary' }, buttonsStyling: false });
            }
        });
    });

    // Edit Perangkat Modal Open
    $(document).on('click', '.edit-device-btn', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var cloudId = $(this).data('cloud-id');

        $('#edit-device-id').val(id);
        $('#edit-device-name').val(name);
        $('#edit-device-cloud-id').val(cloudId);
        $('#modal-edit-device').modal('show');
    });

    // Edit Perangkat Submit
    $('#form-edit-device').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Memperbarui...');

        $.ajax({
            url: "{{ route('admin.fingerspot.devices.update') }}",
            type: "PUT",
            data: form.serialize(),
            success: function(res) {
                submitBtn.prop('disabled', false).html('<i class="ti ti-check me-1"></i> Perbarui');
                $('#modal-edit-device').modal('hide');
                showToastr('success', 'Sukses', res.message);
                tableDevices.ajax.reload(null, false);
            },
            error: function(err) {
                submitBtn.prop('disabled', false).html('<i class="ti ti-check me-1"></i> Perbarui');
                var msg = err.responseJSON ? err.responseJSON.message : 'Terjadi kesalahan sistem.';
                Swal.fire({ icon: 'error', title: 'Gagal Memperbarui Perangkat', text: msg, customClass: { confirmButton: 'btn btn-primary' }, buttonsStyling: false });
            }
        });
    });

    // Hapus Perangkat
    $(document).on('click', '.delete-device-btn', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');

        Swal.fire({
            title: `Hapus perangkat "${name}"?`,
            text: "Data perangkat akan dihapus dari sistem lokal!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-danger me-3',
                cancelButton: 'btn btn-label-secondary'
            },
            buttonsStyling: false
        }).then(function(result) {
            if (result.value) {
                $.ajax({
                    url: "{{ route('admin.fingerspot.devices.delete') }}",
                    type: "DELETE",
                    data: { id: id },
                    success: function(res) {
                        showToastr('success', 'Sukses', res.message);
                        tableDevices.ajax.reload(null, false);
                    },
                    error: function(err) {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: err.responseJSON ? err.responseJSON.message : 'Gagal menghapus perangkat.' });
                    }
                });
            }
        });
    });

    // Info Online Mesin (get_device)
    $(document).on('click', '.btn-device-info', function() {
        var cloudId = $(this).data('cloud-id');
        var name = $(this).data('name');
        var btn = $(this);
        btn.prop('disabled', true);

        $.ajax({
            url: "{{ route('admin.fingerspot.devices.info') }}",
            type: "POST",
            data: { cloud_id: cloudId },
            success: function(res) {
                btn.prop('disabled', false);
                if (res.success && res.data) {
                    var d = res.data;
                    $('#live-info-device-name').text(name);
                    $('#live-info-cloud-name').text(d.device_name || name);
                    $('#live-info-cloud-id').text(d.cloud_id);
                    $('#live-info-last-act').text(d.last_activity || 'N/A');
                    $('#live-info-created-at').text(d.created_at || '-');
                    $('#live-info-webhook-url').text(d.webhook_url || '-');
                    $('#btn-copy-live-webhook').attr('data-clipboard-text', d.webhook_url || '');

                    $('#card-live-device-info').removeClass('d-none');
                    $('html, body').animate({ scrollTop: $('#card-live-device-info').offset().top - 100 }, 400);
                    showToastr('success', 'Info Diterima', 'Status online perangkat berhasil dimuat.');
                } else {
                    Swal.fire({ icon: 'warning', title: 'Respon Perangkat', text: res.message || 'Perangkat tidak merespons.' });
                }
            },
            error: function(err) {
                btn.prop('disabled', false);
                Swal.fire({ icon: 'error', title: 'Gagal Menghubungi Perangkat', text: err.responseJSON ? err.responseJSON.message : 'Koneksi gagal.' });
            }
        });
    });

    // Atur Waktu Perangkat Modal
    $(document).on('click', '.btn-device-time', function() {
        var cloudId = $(this).data('cloud-id');
        var name = $(this).data('name');

        $('#set-time-cloud-id').val(cloudId);
        $('#set-time-device-name').text(`${name} (${cloudId})`);
        $('#modal-set-time').modal('show');
    });

    $('#form-set-time').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Mengirim...');

        $.ajax({
            url: "{{ route('admin.fingerspot.devices.set_time') }}",
            type: "POST",
            data: form.serialize(),
            success: function(res) {
                submitBtn.prop('disabled', false).html('<i class="ti ti-send me-1"></i> Kirim Perintah');
                $('#modal-set-time').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Perintah Dikirim!',
                    html: `<p>Perintah sinkronisasi zona waktu berhasil dikirim ke server Fingerspot.</p>
                           <p class="small text-muted mb-0">Trans ID: <code>${res.trans_id}</code>. Mesin akan menerapkan waktu dan mengirimkan callback konfirmasi ke webhook.</p>`,
                    customClass: { confirmButton: 'btn btn-primary' },
                    buttonsStyling: false
                });
                tableCommands.ajax.reload(null, false);
            },
            error: function(err) {
                submitBtn.prop('disabled', false).html('<i class="ti ti-send me-1"></i> Kirim Perintah');
                Swal.fire({ icon: 'error', title: 'Gagal Mengirim Perintah', text: err.responseJSON ? err.responseJSON.message : 'Kesalahan jaringan.' });
            }
        });
    });

    // Restart Mesin
    $(document).on('click', '.btn-device-restart', function() {
        var cloudId = $(this).data('cloud-id');
        var name = $(this).data('name');

        Swal.fire({
            title: `Restart mesin "${name}"?`,
            text: "Perintah restart akan dikirimkan ke perangkat melalui cloud!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Restart Sekarang!',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-danger me-3',
                cancelButton: 'btn btn-label-secondary'
            },
            buttonsStyling: false
        }).then(function(result) {
            if (result.value) {
                $.ajax({
                    url: "{{ route('admin.fingerspot.devices.restart') }}",
                    type: "POST",
                    data: { cloud_id: cloudId },
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Perintah Restart Terkirim!',
                            html: `<p>Perintah restart perangkat berhasil dikirim ke cloud.</p>
                                   <p class="small text-muted mb-0">Trans ID: <code>${res.trans_id}</code></p>`,
                            customClass: { confirmButton: 'btn btn-primary' },
                            buttonsStyling: false
                        });
                        tableCommands.ajax.reload(null, false);
                    },
                    error: function(err) {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: err.responseJSON ? err.responseJSON.message : 'Gagal mengirim perintah restart.' });
                    }
                });
            }
        });
    });

    // ----------------------------------------------------
    // TAB 2: TARIK & SINKRON LOG ABSENSI (ATTLOG)
    // ----------------------------------------------------
    var currentFetchedLogs = [];
    var currentFetchedCloudId = '';

    $('#form-fetch-attlog').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = $('#btn-submit-attlog');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menarik Data...');

        currentFetchedCloudId = $('#attlog-cloud-id').val();

        $.ajax({
            url: "{{ route('admin.fingerspot.attlog.fetch') }}",
            type: "POST",
            data: form.serialize(),
            success: function(res) {
                submitBtn.prop('disabled', false).html('<i class="ti ti-download me-1"></i> Tarik Log');

                if (res.success && res.data) {
                    currentFetchedLogs = res.data;
                    var total = res.total || currentFetchedLogs.length;

                    $('#badge-total-attlog').text(`${total} Scan Ditemukan`);
                    $('#attlog-query-info').text(`Cloud ID: ${currentFetchedCloudId} | Trans ID: ${res.trans_id || '-'}`);

                    var tbody = $('#attlog-results-tbody');
                    tbody.empty();

                    if (currentFetchedLogs.length === 0) {
                        tbody.append(`<tr><td colspan="6" class="text-center py-4 text-muted">Tidak ada riwayat scan pada rentang tanggal tersebut.</td></tr>`);
                    } else {
                        $.each(currentFetchedLogs, function(idx, item) {
                            var verifyBadge = '';
                            if (item.verify == 1) verifyBadge = '<span class="badge bg-label-primary"><i class="ti ti-fingerprint me-1"></i>Fingerprint</span>';
                            else if (item.verify == 4) verifyBadge = '<span class="badge bg-label-info"><i class="ti ti-scan me-1"></i>Face</span>';
                            else if (item.verify == 3) verifyBadge = '<span class="badge bg-label-secondary"><i class="ti ti-id me-1"></i>RFID Card</span>';
                            else verifyBadge = `<span class="badge bg-label-dark">${item.verify_label}</span>`;

                            var statusBadge = '';
                            if (item.status_scan == 0) statusBadge = '<span class="badge bg-label-success">Scan In (Masuk)</span>';
                            else if (item.status_scan == 1) statusBadge = '<span class="badge bg-label-danger">Scan Out (Pulang)</span>';
                            else statusBadge = `<span class="badge bg-label-warning">${item.status_scan_label}</span>`;

                            var scanTime = item.scan_date || item.scan || '-';

                            tbody.append(`
                                <tr>
                                    <td>${idx + 1}</td>
                                    <td><code class="fw-bold">${item.pin}</code></td>
                                    <td><strong>${item.user_name}</strong></td>
                                    <td>${scanTime}</td>
                                    <td>${verifyBadge}</td>
                                    <td>${statusBadge}</td>
                                </tr>
                            `);
                        });
                    }

                    $('#card-attlog-results').removeClass('d-none');
                    $('html, body').animate({ scrollTop: $('#card-attlog-results').offset().top - 100 }, 400);
                    showToastr('success', 'Berhasil', `${total} data log absensi berhasil diambil.`);
                } else {
                    Swal.fire({ icon: 'warning', title: 'Hasil Tarik Log', text: res.message || 'Tidak ada data absensi.' });
                }
            },
            error: function(err) {
                submitBtn.prop('disabled', false).html('<i class="ti ti-download me-1"></i> Tarik Log');
                var msg = err.responseJSON ? err.responseJSON.message : 'Gagal mengambil data dari cloud.';
                Swal.fire({ icon: 'error', title: 'Gagal Menarik Data', text: msg });
            }
        });
    });

    // Sinkronkan ke Database Absensi Utama
    $('#btn-sync-all-attlog').on('click', function() {
        if (!currentFetchedLogs || currentFetchedLogs.length === 0) {
            Swal.fire({ icon: 'info', title: 'Tidak Ada Data', text: 'Silakan tarik log absensi terlebih dahulu.' });
            return;
        }

        var btn = $(this);
        Swal.fire({
            title: `Sinkronkan ${currentFetchedLogs.length} data scan?`,
            text: "Sistem akan mencatat scan datang/pulang dan menghitung durasi kategori otomatis di database lokal!",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Sinkronkan Sekarang!',
            cancelButtonText: 'Batal',
            customClass: { confirmButton: 'btn btn-success me-3', cancelButton: 'btn btn-label-secondary' },
            buttonsStyling: false
        }).then(function(result) {
            if (result.value) {
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyinkronkan...');

                $.ajax({
                    url: "{{ route('admin.fingerspot.attlog.sync') }}",
                    type: "POST",
                    data: {
                        cloud_id: currentFetchedCloudId,
                        logs: currentFetchedLogs
                    },
                    success: function(res) {
                        btn.prop('disabled', false).html('<i class="ti ti-database-import me-1"></i> Sinkronkan ke Data Absensi Utama');
                        Swal.fire({
                            icon: 'success',
                            title: 'Sinkronisasi Berhasil!',
                            html: `<p class="mb-2">${res.message}</p>
                                   <div class="text-start bg-light p-2 rounded small">
                                       <strong>Baru Dicatat:</strong> ${res.data.created}<br>
                                       <strong>Diperbarui (Scan Pulang):</strong> ${res.data.updated}<br>
                                       <strong>Dilewati (Belum 2 Jam):</strong> ${res.data.skipped}
                                   </div>`,
                            customClass: { confirmButton: 'btn btn-primary' },
                            buttonsStyling: false
                        });
                    },
                    error: function(err) {
                        btn.prop('disabled', false).html('<i class="ti ti-database-import me-1"></i> Sinkronkan ke Data Absensi Utama');
                        Swal.fire({ icon: 'error', title: 'Gagal Sinkronisasi', text: err.responseJSON ? err.responseJSON.message : 'Kesalahan server.' });
                    }
                });
            }
        });
    });

    // ----------------------------------------------------
    // TAB 3: PENGGUNA MESIN (DEVICE USERS)
    // ----------------------------------------------------
    var tableDeviceUsers = $('#table-device-users').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.fingerspot.users.data') }}",
            data: function(d) {
                d.cloud_id = $('#filter-user-cloud-id').val();
            }
        },
        columns: [
            { 
                data: 'cloud_id', 
                name: 'cloud_id',
                render: function(data, type, row) {
                    var devName = row.device ? row.device.name : 'Unknown';
                    return `<div><strong>${devName}</strong><br><small class="text-muted font-monospace">${data}</small></div>`;
                }
            },
            { data: 'user_display', name: 'user_display' },
            { data: 'credentials', name: 'credentials', orderable: false, searchable: false },
            { data: 'privilege', name: 'privilege', className: 'text-center' },
            { data: 'last_sync_at', name: 'last_sync_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[0, 'asc']]
    });

    $('#filter-user-cloud-id').on('change', function() {
        tableDeviceUsers.ajax.reload();
    });

    $('#btn-refresh-device-users').on('click', function() {
        tableDeviceUsers.ajax.reload(null, false);
    });

    // Ambil Semua PIN dari Mesin (get_all_pin)
    $('#btn-fetch-all-pin').on('click', function() {
        var cloudId = $('#filter-user-cloud-id').val();
        if (!cloudId) {
            cloudId = "{{ $devices->first() ? $devices->first()->cloud_id : '' }}";
        }

        if (!cloudId) {
            Swal.fire({ icon: 'warning', title: 'Belum Ada Mesin', text: 'Daftarkan mesin absensi terlebih dahulu.' });
            return;
        }

        Swal.fire({
            title: 'Ambil Semua PIN Pengguna?',
            text: `Perintah akan dikirim ke mesin (${cloudId}). Mesin akan merespons melalui webhook dengan daftar seluruh PIN terdaftar.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Kirim Perintah!',
            cancelButtonText: 'Batal',
            customClass: { confirmButton: 'btn btn-primary me-3', cancelButton: 'btn btn-label-secondary' },
            buttonsStyling: false
        }).then(function(result) {
            if (result.value) {
                $.ajax({
                    url: "{{ route('admin.fingerspot.users.get_all_pin') }}",
                    type: "POST",
                    data: { cloud_id: cloudId },
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Perintah get_all_pin Dikirim!',
                            html: `<p>Perintah telah diterima oleh server cloud.</p>
                                   <p class="small text-muted mb-0">Trans ID: <code>${res.trans_id}</code>. Hasilnya akan tiba via callback webhook.</p>`,
                            customClass: { confirmButton: 'btn btn-primary' },
                            buttonsStyling: false
                        });
                        tableCommands.ajax.reload(null, false);
                    },
                    error: function(err) {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: err.responseJSON ? err.responseJSON.message : 'Gagal mengirim perintah.' });
                    }
                });
            }
        });
    });

    // Refresh User Info dari Mesin (get_userinfo)
    $(document).on('click', '.btn-get-userinfo', function() {
        var cloudId = $(this).data('cloud-id');
        var pin = $(this).data('pin');

        $.ajax({
            url: "{{ route('admin.fingerspot.users.get_info') }}",
            type: "POST",
            data: { cloud_id: cloudId, pin: pin },
            success: function(res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Perintah get_userinfo Dikirim!',
                    html: `<p>Perintah pengambilan detail user PIN <strong>${pin}</strong> berhasil dikirim.</p>
                           <p class="small text-muted mb-0">Trans ID: <code>${res.trans_id}</code>. Data profil dan template biometrik akan diperbarui via callback webhook.</p>`,
                    customClass: { confirmButton: 'btn btn-primary' },
                    buttonsStyling: false
                });
                tableCommands.ajax.reload(null, false);
            },
            error: function(err) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: err.responseJSON ? err.responseJSON.message : 'Gagal mengirim perintah.' });
            }
        });
    });

    // Pilih User Lokal auto-fill di modal push user
    $('#select-local-user').on('change', function() {
        var val = $(this).val();
        if (val) {
            var name = $(this).find(':selected').data('name');
            $('#push-user-pin').val(val);
            $('#push-user-name').val(name);
        }
    });

    // Submit Push User (set_userinfo)
    $('#form-push-user').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Mengirim...');

        $.ajax({
            url: "{{ route('admin.fingerspot.users.set_info') }}",
            type: "POST",
            data: form.serialize(),
            success: function(res) {
                submitBtn.prop('disabled', false).html('<i class="ti ti-send me-1"></i> Kirim ke Mesin');
                $('#modal-push-user').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Perintah set_userinfo Dikirim!',
                    html: `<p>Data pengguna telah dikirimkan ke mesin absensi.</p>
                           <p class="small text-muted mb-0">Trans ID: <code>${res.trans_id}</code></p>`,
                    customClass: { confirmButton: 'btn btn-primary' },
                    buttonsStyling: false
                });
                tableDeviceUsers.ajax.reload(null, false);
                tableCommands.ajax.reload(null, false);
            },
            error: function(err) {
                submitBtn.prop('disabled', false).html('<i class="ti ti-send me-1"></i> Kirim ke Mesin');
                Swal.fire({ icon: 'error', title: 'Gagal', text: err.responseJSON ? err.responseJSON.message : 'Gagal mengirim perintah ke mesin.' });
            }
        });
    });

    // Registrasi Biometrik Online (reg_online)
    $(document).on('click', '.btn-reg-online', function() {
        var cloudId = $(this).data('cloud-id');
        var pin = $(this).data('pin');
        var name = $(this).data('name') || `User #${pin}`;

        $('#reg-cloud-id').val(cloudId);
        $('#reg-pin').val(pin);
        $('#reg-user-pin').text(pin);
        $('#reg-user-name').text(name);
        $('#modal-reg-online').modal('show');
    });

    $('#form-reg-online').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Mengirim...');

        $.ajax({
            url: "{{ route('admin.fingerspot.users.reg_online') }}",
            type: "POST",
            data: form.serialize(),
            success: function(res) {
                submitBtn.prop('disabled', false).html('<i class="ti ti-broadcast me-1"></i> Mulai Perekaman di Mesin');
                $('#modal-reg-online').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Perekaman Biometrik Dimulai!',
                    html: `<p>Mesin absensi fisik sedang menunggu input sidik jari/wajah untuk PIN tersebut.</p>
                           <p class="small text-muted mb-0">Trans ID: <code>${res.trans_id}</code></p>`,
                    customClass: { confirmButton: 'btn btn-primary' },
                    buttonsStyling: false
                });
                tableCommands.ajax.reload(null, false);
            },
            error: function(err) {
                submitBtn.prop('disabled', false).html('<i class="ti ti-broadcast me-1"></i> Mulai Perekaman di Mesin');
                Swal.fire({ icon: 'error', title: 'Gagal', text: err.responseJSON ? err.responseJSON.message : 'Gagal memulai perekaman.' });
            }
        });
    });

    // Copy User Modal Open
    $(document).on('click', '.btn-copy-user', function() {
        var cloudId = $(this).data('cloud-id');
        var pin = $(this).data('pin');
        var name = $(this).data('name') || `User #${pin}`;

        $('#copy-source-cloud-id').val(cloudId);
        $('#copy-source-cloud-id-text').text(cloudId);
        $('#copy-pin').val(pin);
        $('#copy-user-pin').text(pin);
        $('#copy-user-name').text(name);
        $('#modal-copy-user').modal('show');
    });

    $('#form-copy-user').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Memproses...');

        $.ajax({
            url: "{{ route('admin.fingerspot.users.copy') }}",
            type: "POST",
            data: form.serialize(),
            success: function(res) {
                submitBtn.prop('disabled', false).html('<i class="ti ti-send me-1"></i> Proses Duplikasi');
                $('#modal-copy-user').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Duplikasi Terkirim!',
                    text: res.message,
                    customClass: { confirmButton: 'btn btn-primary' },
                    buttonsStyling: false
                });
                tableDeviceUsers.ajax.reload(null, false);
                tableCommands.ajax.reload(null, false);
            },
            error: function(err) {
                submitBtn.prop('disabled', false).html('<i class="ti ti-send me-1"></i> Proses Duplikasi');
                Swal.fire({ icon: 'error', title: 'Gagal Duplikasi', text: err.responseJSON ? err.responseJSON.message : 'Kesalahan server.' });
            }
        });
    });

    // Hapus User dari Mesin (delete_userinfo)
    $(document).on('click', '.btn-delete-device-user', function() {
        var cloudId = $(this).data('cloud-id');
        var pin = $(this).data('pin');
        var name = $(this).data('name') || `User #${pin}`;

        Swal.fire({
            title: `Hapus user "${name}" dari mesin?`,
            text: `Perintah delete_userinfo untuk PIN ${pin} akan dikirimkan ke mesin (${cloudId})!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus dari Mesin!',
            cancelButtonText: 'Batal',
            customClass: { confirmButton: 'btn btn-danger me-3', cancelButton: 'btn btn-label-secondary' },
            buttonsStyling: false
        }).then(function(result) {
            if (result.value) {
                $.ajax({
                    url: "{{ route('admin.fingerspot.users.delete') }}",
                    type: "DELETE",
                    data: { cloud_id: cloudId, pin: pin },
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Perintah Hapus Dikirim!',
                            html: `<p>Perintah penghapusan user telah dikirim ke mesin.</p>
                                   <p class="small text-muted mb-0">Trans ID: <code>${res.trans_id}</code></p>`,
                            customClass: { confirmButton: 'btn btn-primary' },
                            buttonsStyling: false
                        });
                        tableCommands.ajax.reload(null, false);
                    },
                    error: function(err) {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: err.responseJSON ? err.responseJSON.message : 'Gagal mengirim perintah hapus.' });
                    }
                });
            }
        });
    });

    // ----------------------------------------------------
    // TAB 4: LOG PERINTAH & WEBHOOK
    // ----------------------------------------------------
    var tableCommands = $('#table-commands-log').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.fingerspot.commands.data') }}",
            data: function(d) {
                d.cloud_id = $('#filter-log-cloud-id').val();
                d.command_type = $('#filter-log-type').val();
                d.status = $('#filter-log-status').val();
            }
        },
        columns: [
            { data: 'created_at', name: 'created_at' },
            { data: 'command_type', name: 'command_type' },
            { 
                data: 'cloud_id', 
                name: 'cloud_id',
                render: function(data, type, row) {
                    var dev = row.device_name ? `<div>${row.device_name}</div>` : '';
                    return `${dev}<code class="small text-muted">${data || '-'}</code>`;
                }
            },
            { 
                data: 'trans_id', 
                name: 'trans_id',
                render: function(data) {
                    return data ? `<code class="small">${data}</code>` : '<span class="text-muted">-</span>';
                }
            },
            { data: 'status', name: 'status', className: 'text-center' },
            { 
                data: 'message', 
                name: 'message',
                render: function(data) {
                    return `<span class="small text-truncate d-inline-block" style="max-width: 250px;" title="${data || ''}">${data || '-'}</span>`;
                }
            },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[0, 'desc']]
    });

    $('#filter-log-cloud-id, #filter-log-type, #filter-log-status').on('change', function() {
        tableCommands.ajax.reload();
    });

    $('#btn-refresh-command-logs').on('click', function() {
        tableCommands.ajax.reload(null, false);
    });

    // Modal View Detail Command / JSON
    $(document).on('click', '.btn-view-command', function() {
        var id = $(this).data('id');

        $.ajax({
            url: "{{ route('admin.fingerspot.commands.detail') }}",
            type: "GET",
            data: { id: id },
            success: function(res) {
                if (res.status && res.data) {
                    var d = res.data;
                    $('#detail-command-type').text(d.command_type);
                    $('#detail-trans-id').text(d.trans_id || '-');
                    $('#detail-status').html(
                        d.status === 'success' ? '<span class="badge bg-label-success">Sukses</span>' :
                        (d.status === 'failed' ? '<span class="badge bg-label-danger">Gagal</span>' : '<span class="badge bg-label-warning">Pending</span>')
                    );

                    $('#detail-json-request').text(d.payload_request ? JSON.stringify(d.payload_request, null, 2) : '// Tidak ada payload request tersimpan.');
                    $('#detail-json-response').text(d.payload_response ? JSON.stringify(d.payload_response, null, 2) : '// Tidak ada respons tersimpan.');
                    $('#detail-json-callback').text(d.callback_payload ? JSON.stringify(d.callback_payload, null, 2) : '// Belum ada callback webhook diterima.');

                    $('#modal-view-command').modal('show');
                }
            }
        });
    });

    // Bersihkan Log
    $('#btn-clear-command-logs').on('click', function() {
        Swal.fire({
            title: 'Bersihkan Riwayat Log?',
            text: 'Pilih opsi pembersihan riwayat log perintah & webhook:',
            icon: 'warning',
            showCancelButton: true,
            showDenyButton: true,
            confirmButtonText: 'Bersihkan > 30 Hari',
            denyButtonText: 'Bersihkan Semua (Kosongkan)',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-warning me-2',
                denyButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-label-secondary'
            },
            buttonsStyling: false
        }).then(function(result) {
            if (result.isConfirmed || result.isDenied) {
                var days = result.isDenied ? 'all' : 30;
                $.ajax({
                    url: "{{ route('admin.fingerspot.commands.clear') }}",
                    type: "POST",
                    data: { days: days },
                    success: function(res) {
                        showToastr('success', 'Sukses', res.message);
                        tableCommands.ajax.reload(null, false);
                    }
                });
            }
        });
    });

    // ----------------------------------------------------
    // TAB 5: API PLAYGROUND & TESTER
    // ----------------------------------------------------
    function updatePlaygroundFields() {
        var ep = $('#test-endpoint').val();
        $('.dynamic-param-group').addClass('d-none');

        if (ep === 'get_attlog') {
            $('#param-group-attlog').removeClass('d-none');
        } else if (ep === 'get_userinfo' || ep === 'delete_userinfo') {
            $('#param-group-pin').removeClass('d-none');
        } else if (ep === 'set_userinfo') {
            $('#param-group-setuser').removeClass('d-none');
        } else if (ep === 'set_time') {
            $('#param-group-time').removeClass('d-none');
        } else if (ep === 'reg_online') {
            $('#param-group-pin').removeClass('d-none');
            $('#param-group-regonline').removeClass('d-none');
        }
    }

    $('#test-endpoint').on('change', updatePlaygroundFields);
    updatePlaygroundFields();

    $('#form-api-tester').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = $('#btn-run-tester');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menjalankan...');

        $('#code-test-response').text('// Mengirim permintaan ke Fingerspot API...');

        $.ajax({
            url: "{{ route('admin.fingerspot.api.test') }}",
            type: "POST",
            data: form.serialize(),
            success: function(res) {
                submitBtn.prop('disabled', false).html('<i class="ti ti-player-play me-1"></i> Eksekusi API');

                var latency = res.duration_ms || 0;
                $('#badge-test-latency').text(`${latency} ms`).removeClass('d-none');

                var pretty = JSON.stringify(res, null, 2);
                $('#code-test-response').text(pretty);
                $('#btn-copy-test-response').attr('data-clipboard-text', pretty);

                tableCommands.ajax.reload(null, false);
            },
            error: function(err) {
                submitBtn.prop('disabled', false).html('<i class="ti ti-player-play me-1"></i> Eksekusi API');
                var errObj = err.responseJSON || { error: 'Network / server error' };
                var pretty = JSON.stringify(errObj, null, 2);
                $('#code-test-response').text(pretty);
                $('#btn-copy-test-response').attr('data-clipboard-text', pretty);
            }
        });
    });
});
</script>
@endpush
