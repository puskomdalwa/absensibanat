<div class="d-flex justify-content-end mb-4">
    <button class="btn btn-primary d-flex align-items-center gap-2 px-4 py-2" onclick="downloadExcel()">
        <i class="ti ti-file-spreadsheet fs-5"></i>
        <span>Download Excel Rekap</span>
    </button>
</div>

@push('scripts')
    <script>
        function downloadExcel() {
            const params = new URLSearchParams({
                role_id: $('#role_id').val() || '*',
                departemen_id: $('#departemen_id').val() || '*',
                filter_type: $('#filter_type').val() || 'bulan_tahun',
                bulan: $('#bulan').val() || '*',
                tahun: $('#tahun').val() || '*',
                start_date: $('#start_date').val() || '',
                end_date: $('#end_date').val() || '',
            });

            window.location.href = "{{ route('admin.absensi.export.excel') }}?" + params.toString();
        }
    </script>
@endpush
