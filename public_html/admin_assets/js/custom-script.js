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
            if (offCanvasRecord != false) {
                offCanvasRecord.hide();
            }
            if (typeof dataTable !== "undefined" && dataTable !== null) {
                dataTable.ajax.reload(null, false);
            }
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
            offCanvasRecord.hide();
            location.reload();
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
    });
}
