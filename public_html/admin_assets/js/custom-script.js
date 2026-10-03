const params = new URLSearchParams(window.location.search);
function ajaxRequestDt(e, offCanvasRecord, dataTable) {
    var submitBtn = $(e.currentTarget).find('button[type="submit"]');
    $.ajax({
        type: "POST",
        url: $(e.currentTarget).attr("action"),
        data: new FormData(e.currentTarget),
        processData: false,
        contentType: false,
        beforeSend: function () {
            submitBtn.attr("disabled", true);
            submitBtn.html("Process...");
        },
        complete: function () {
            submitBtn.attr("disabled", false);
            submitBtn.html("Submit");
        },
        success: function (response) {
            showToastr(response.type, response.type, response.message);
            if (response.status !== false && response.type !== "error") {
                if (offCanvasRecord != false) {
                    offCanvasRecord.hide();
                }
                if (typeof dataTable !== "undefined" && dataTable !== null) {
                    dataTable.ajax.reload(null, false);
                }
            }
        },
        error: function (xhr) {
            var msg = "Terjadi kesalahan saat memproses data.";
            if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            }
            showToastr("error", "ERROR", msg);
        },
    });
}

function ajaxRequestWithRefresh(e, offCanvasRecord) {
    var submitBtn = $(e.currentTarget).find('button[type="submit"]');
    $.ajax({
        type: "POST",
        url: $(e.currentTarget).attr("action"),
        data: new FormData(e.currentTarget),
        processData: false,
        contentType: false,
        beforeSend: function () {
            submitBtn.attr("disabled", true);
            submitBtn.html("Process...");
        },
        complete: function () {
            submitBtn.attr("disabled", false);
            submitBtn.html("Submit");
        },
        success: function (response) {
            showToastr(response.type, response.type, response.message);
            if (response.status !== false && response.type !== "error") {
                if (offCanvasRecord) offCanvasRecord.hide();
                location.reload();
            }
        },
        error: function (xhr) {
            var msg = "Terjadi kesalahan saat memproses data.";
            if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            }
            showToastr("error", "ERROR", msg);
        },
    });
}

function ajaxRequest(e) {
    var submitBtn = $(e.currentTarget).find('button[type="submit"]');
    $.ajax({
        type: "POST",
        url: $(e.currentTarget).attr("action"),
        data: new FormData(e.currentTarget),
        processData: false,
        contentType: false,
        beforeSend: function () {
            submitBtn.attr("disabled", true);
            submitBtn.html("Process...");
        },
        complete: function () {
            submitBtn.attr("disabled", false);
            submitBtn.html("Submit");
        },
        success: function (response) {
            showToastr(response.type, response.type, response.message);
        },
        error: function (xhr) {
            var msg = "Terjadi kesalahan saat memproses data.";
            if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            }
            showToastr("error", "ERROR", msg);
        },
    });
}
