toastr.options = {
    maxOpened: 1,
    autoDismiss: true,
    closeButton: true,
    newestOnTop: true,
    progressBar: true,
    positionClass:
        $("#positionGroup input:radio:checked").val() || "toast-top-right", // Menyesuaikan posisi berdasarkan input radio
    onclick: null,
};

function showToastr(status, title, message) {
    // Tentukan jenis status yang diterima dan tampilkan toastr sesuai status
    if (status === "success") {
        toastr.success(message, title.toUpperCase()); // Menampilkan toastr success
    } else if (status === "error") {
        toastr.error(message, title.toUpperCase()); // Menampilkan toastr error
    } else if (status === "info") {
        toastr.info(message, title.toUpperCase()); // Menampilkan toastr info
    } else if (status === "warning") {
        toastr.warning(message, title.toUpperCase()); // Menampilkan toastr warning
    }
}
