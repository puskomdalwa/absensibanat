function dtResponsive(name) {
    return {
        details: {
            display: $.fn.dataTable.Responsive.display.modal({
                header: function (row) {
                    var data = row.data();
                    return "Details of " + name;
                },
            }),
            type: "column",
            renderer: function (api, rowIdx, columns) {
                var data = $.map(columns, function (col, i) {
                        ? '<tr data-dt-row="' +
                              col.rowIndex +
                              '" data-dt-column="' +
                              col.columnIndex +
                              '">' +
                              "<td>" +
                              col.title +
                              ":" +
                              "</td> " +
                              "<td>" +
                              col.data +
                              "</td>" +
                              "</tr>"
                        : "";
                }).join("");

                return data
                    ? $('<table class="table"/><tbody />').append(data)
                    : false;
            },
        }
    };
}
