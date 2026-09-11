<!-- Core JS -->
<!-- build:js admin_assets/vendor/js/core.js -->

<script src="{{ asset('admin_assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('admin_assets/vendor/libs/jquery-ui/jquery-ui.js') }}"></script>
<script src="{{ asset('admin_assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('admin_assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('admin_assets/vendor/libs/node-waves/node-waves.js') }}"></script>
<script src="{{ asset('admin_assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('admin_assets/vendor/libs/hammer/hammer.js') }}"></script>
<script src="{{ asset('admin_assets/vendor/libs/i18n/i18n.js') }}"></script>
<script src="{{ asset('admin_assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
<script src="{{ asset('admin_assets/vendor/js/menu.js') }}"></script>

<!-- endbuild -->

<!-- Vendors JS -->
<script src="{{ asset('admin_assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
<script src="{{ asset('admin_assets/vendor/libs/swiper/swiper.js') }}"></script>
<script src="{{ asset('admin_assets/vendor/libs/toastr/toastr.js') }}"></script>
<script src="{{ asset('admin_assets/vendor/libs/toastr/toastr-custom.js') }}"></script>
<script src="{{ asset('admin_assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>

<script src="{{ asset('admin_assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
{{-- datatables simple (for simple init datatables, own work source code) --}}
<script src="{{ asset('admin_assets/vendor/libs/datatables-bs5/datatables-simple.js') }}"></script>

<script src="{{ asset('admin_assets/vendor/libs/moment/moment.js') }}"></script>
<script src="{{ asset('admin_assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
<script src="{{ asset('admin_assets/vendor/libs/select2/select2.js') }}"></script>
<script src="{{ asset('admin_assets/vendor/libs/tagify/tagify.js') }}"></script>
<script src="{{ asset('admin_assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
<script src="{{ asset('admin_assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
<script src="{{ asset('admin_assets/vendor/libs/bloodhound/bloodhound.js') }}"></script>
<script src="{{ asset('admin_assets/vendor/libs/summernote/summernote-bs5.js') }}"></script>

<!-- Form Validation -->
<script src="{{ asset('admin_assets/vendor/libs/@form-validation/popular.js') }}"></script>
<script src="{{ asset('admin_assets/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
<script src="{{ asset('admin_assets/vendor/libs/@form-validation/auto-focus.js') }}"></script>

<script src="{{ asset('admin_assets/vendor/libs/block-ui/block-ui.js') }}"></script>
<script src="{{ asset('admin_assets/vendor/libs/sortablejs/sortable.js') }}"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

<!-- Main JS -->
<script src="{{ asset('admin_assets/js/main.js') }}"></script>
<script src="{{ asset('admin_assets/js/custom-script.js') }}"></script>

<script>
    $(document).ready(function() {
        var select2 = $('.select2');
        if (select2.length) {
            select2.each(function() {
                var $this = $(this);
                $this.wrap('<div class="position-relative"></div>').select2({
                    placeholder: 'Select value',
                    dropdownParent: $this.parent()
                });
            });
        }
    });
</script>

@if (session('success'))
    <script script>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: "{{ session('success') }}",
            timer: 3000,
            showConfirmButton: false
        });
    </script>
@endif
@if (session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: "{{ session('error') }}",
            timer: 3000,
            showConfirmButton: false
        });
    </script>
@endif
