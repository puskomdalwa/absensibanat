/*
Example:
var dataTable = initDataTables('table-1', 'loader-category', 'card-category', 'new-record-button', false,
    'News', "{{ route('admin.category.data') }}",
    [{
            data: "name",
            name: "name",
            className: "align-middle",
        },
        {
            data: "description",
            name: "description",
            className: "align-middle",
        },
        {
            data: "action",
            name: "action",
            className: "align-middle",
            searchable: false,
            orderable: false,
        },
    ]
);
*/

function initDataTables(
  tableId,
  loaderId,
  cardId,
  newRecordId,
  isResponsive,
  title,
  url,
  columns,
  params = false,
  saveState = false,
  orderBy = 0
) {
  var initColumns = [];
  if (isResponsive) {
    initColumns.push({
      // For Responsive
      className: "control",
      orderable: false,
      searchable: false,
      data: "id",
      render: function (data, type, full, meta) {
        return "";
      },
    });

    $(`#${tableId} thead tr`).prepend("<th></th>");
  }

  initColumns.push({
    data: title == "Absensi " ? "tgl_absen" : "id",
    render: function (data, type, row, meta) {
      return meta.row + meta.settings._iDisplayStart + 1;
    },
  });

  let dataTable = $("#" + tableId).DataTable({
    autoWidth: true,
    processing: true,
    serverSide: true,
    search: {
      return: true,
    },
    stateSave: saveState,
    ajax: {
      url: url,
      method: "GET",
      data: function (d) {
        if (params) {
          params.forEach((variable) => {
            d[variable] = $(`#${variable}`).val(); // Menggunakan template literal untuk memilih elemen
          });
        }
      },
      beforeSend: function () {
        // var loader = `@include('components.loader', ['idLoader' => ${loaderId}])`;
        // $("#" + loaderId).append(loader);
        var loader = `
                    <div class="loader-overlay" id="${loaderId}">
                    <div class="sk-fold sk-primary">
                        <div class="sk-fold-cube"></div>
                        <div class="sk-fold-cube"></div>
                        <div class="sk-fold-cube"></div>
                        <div class="sk-fold-cube"></div>
                    </div>
                    <h5>LOADING...</h5>
                </div>
                `;
        $("#" + cardId).append(loader);
      },
      complete: function () {
        $("#" + loaderId).remove();
      },
    },
    columns: [...initColumns, ...columns],
    order: [[isResponsive ? 1 : orderBy, "desc"]],
    dom: '<"card-header flex-column flex-md-row"<"head-label text-center"><"dt-action-buttons text-end pt-6 pt-md-0"B>><"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end mt-n6 mt-md-0"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
    displayLength: 10,
    lengthMenu: [5, 10, 25, 50, 75, 100, "All"],
    language: {
      paginate: {
        next: '<i class="ti ti-chevron-right ti-sm"></i>',
        previous: '<i class="ti ti-chevron-left ti-sm"></i>',
      },
    },
    buttons: [
      {
        extend: "collection",
        className:
          "btn btn-label-primary dropdown-toggle me-4 waves-effect waves-light border-none",
        text: '<i class="ti ti-file-export ti-xs me-sm-1"></i> <span class="d-none d-sm-inline-block">Export</span>',
        buttons: [
          {
            extend: "print",
            text: '<i class="ti ti-printer me-1" ></i>Print',
            className: "dropdown-item",
            exportOptions: {
              columns: function (idx, data, node) {
                // Kembalikan `true` untuk mengekspor semua kolom
                return true;
              },
              // prevent avatar to be display
              format: {
                body: function (inner, coldex, rowdex) {
                  if (inner.length <= 0) return inner;
                  var el = $.parseHTML(inner);
                  var result = "";
                  $.each(el, function (index, item) {
                    if (
                      item.classList !== undefined &&
                      item.classList.contains("user-name")
                    ) {
                      let lastChild = item.lastElementChild;
                      if (lastChild) {
                        let userName = lastChild.firstElementChild
                          ? lastChild.firstElementChild.textContent
                          : "";

                        result += userName;
                      }
                    } else if (item.innerText === undefined) {
                      result = result + item.textContent;
                    } else result = result + item.innerText;
                  });
                  return result;
                },
              },
            },
            customize: function (win) {
              //customize print view for dark
              $(win.document.body)
                .css("color", config.colors.headingColor)
                .css("border-color", config.colors.borderColor)
                .css("background-color", config.colors.bodyBg);
              $(win.document.body)
                .find("table")
                .addClass("compact")
                .css("color", "inherit")
                .css("border-color", "inherit")
                .css("background-color", "inherit");
            },
            action: function () {
              exportAllDataTable("print", tableId, title);
            },
          },
          {
            extend: "csv",
            text: '<i class="ti ti-file-text me-1" ></i>Csv',
            className: "dropdown-item",
            exportOptions: {
              columns: function (idx, data, node) {
                // Kembalikan `true` untuk mengekspor semua kolom
                return true;
              },
              // prevent avatar to be display
              format: {
                body: function (inner, coldex, rowdex) {
                  if (inner.length <= 0) return inner;
                  var el = $.parseHTML(inner);
                  var result = "";
                  $.each(el, function (index, item) {
                    if (
                      item.classList !== undefined &&
                      item.classList.contains("user-name")
                    ) {
                      let lastChild = item.lastElementChild;
                      if (lastChild) {
                        let userName = lastChild.firstElementChild
                          ? lastChild.firstElementChild.textContent
                          : "";

                        result += userName;
                      }
                    } else if (item.innerText === undefined) {
                      result = result + item.textContent;
                    } else result = result + item.innerText;
                  });
                  return result;
                },
              },
            },
            action: function () {
              exportAllDataTable("csv", tableId, title);
            },
          },
          {
            extend: "excel",
            text: '<i class="ti ti-file-spreadsheet me-1"></i>Excel',
            className: "dropdown-item",
            exportOptions: {
              columns: function (idx, data, node) {
                // Kembalikan `true` untuk mengekspor semua kolom
                return true;
              },
              // prevent avatar to be display
              format: {
                body: function (inner, coldex, rowdex) {
                  if (inner.length <= 0) return inner;
                  var el = $.parseHTML(inner);
                  var result = "";
                  $.each(el, function (index, item) {
                    if (
                      item.classList !== undefined &&
                      item.classList.contains("user-name")
                    ) {
                      let lastChild = item.lastElementChild;
                      if (lastChild) {
                        let userName = lastChild.firstElementChild
                          ? lastChild.firstElementChild.textContent
                          : "";

                        result += userName;
                      }
                    } else if (item.innerText === undefined) {
                      result = result + item.textContent;
                    } else result = result + item.innerText;
                  });
                  return result;
                },
              },
            },
            action: function () {
              exportAllDataTable("excel", tableId, title);
            },
          },
          {
            extend: "pdf",
            text: '<i class="ti ti-file-description me-1"></i>Pdf',
            className: "dropdown-item",
            exportOptions: {
              columns: function (idx, data, node) {
                // Kembalikan `true` untuk mengekspor semua kolom
                return true;
              },
              // prevent avatar to be display
              format: {
                body: function (inner, coldex, rowdex) {
                  if (inner.length <= 0) return inner;
                  var el = $.parseHTML(inner);
                  var result = "";
                  $.each(el, function (index, item) {
                    if (
                      item.classList !== undefined &&
                      item.classList.contains("user-name")
                    ) {
                      let lastChild = item.lastElementChild;
                      if (lastChild) {
                        let userName = lastChild.firstElementChild
                          ? lastChild.firstElementChild.textContent
                          : "";

                        result += userName;
                      }
                    } else if (item.innerText === undefined) {
                      result = result + item.textContent;
                    } else result = result + item.innerText;
                  });
                  return result;
                },
              },
            },
            action: function () {
              exportAllDataTable("pdf", tableId, title);
            },
          },
          {
            extend: "copy",
            text: '<i class="ti ti-copy me-1" ></i>Copy',
            className: "dropdown-item",
            exportOptions: {
              columns: function (idx, data, node) {
                // Kembalikan `true` untuk mengekspor semua kolom
                return true;
              },
              // prevent avatar to be display
              format: {
                body: function (inner, coldex, rowdex) {
                  if (inner.length <= 0) return inner;
                  var el = $.parseHTML(inner);
                  var result = "";
                  $.each(el, function (index, item) {
                    if (
                      item.classList !== undefined &&
                      item.classList.contains("user-name")
                    ) {
                      let lastChild = item.lastElementChild;
                      if (lastChild) {
                        let userName = lastChild.firstElementChild
                          ? lastChild.firstElementChild.textContent
                          : "";

                        result += userName;
                      }
                    } else if (item.innerText === undefined) {
                      result = result + item.textContent;
                    } else result = result + item.innerText;
                  });
                  return result;
                },
              },
            },
            action: function () {
              exportAllDataTable("copy", tableId, title);
            },
          },
        ],
      },
      ...(newRecordId
        ? [
            {
              attr: {
                id: newRecordId,
              },
              text: '<i class="ti ti-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Add New Record</span>',
              className: "create-new btn btn-primary waves-effect waves-light",
            },
          ]
        : []),
    ],
    responsive: isResponsive
      ? {
          details: {
            display: $.fn.dataTable.Responsive.display.modal({
              header: function (row) {
                var data = row.data();
                return "Details of " + data["name"];
              },
            }),
            type: "column",
            renderer: function (api, rowIdx, columns) {
              var data = $.map(columns, function (col, i) {
                return col.title !== "" // ? Do not show row in modal popup if title is blank (for check box)
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
          },
        }
      : false,
    initComplete: function (settings, json) {
      $(".card-header").after('<hr class="my-0">');
    },
  });

  $("div.head-label").html('<h5 class="card-title mb-0">' + title + "</h5>");
  $("div.dataTables_filter input", dataTable.table().container()).focus();
  return dataTable;
}

function exportAllDataTable(method = "excel", tableId, title) {
  const dt = $("#" + tableId).DataTable();
  const pageSize = 500;
  let start = 0;
  let allData = [];

  function fetchDataPart() {
    console.log("asd");
    return $.ajax({
      url: dt.ajax.url(),
      type: "GET", // sesuaikan dengan method server
      data: $.extend({}, dt.ajax.params(), {
        start: start,
        length: pageSize,
      }),
    });
  }

  function loopFetch() {
    fetchDataPart().done(function (json) {
      if (!json || !json.data || json.data.length === 0) {
        return finishExport();
      }

      allData = allData.concat(json.data);
      start += pageSize;

      // lanjut ambil batch berikutnya
      if (json.data.length === pageSize) {
        loopFetch(); // continue
      } else {
        finishExport(); // done
      }
    });
  }

  function finishExport() {
    // Buat table temporer
    let $tempTable = $("<table>");
    let thead = $("<thead>").appendTo($tempTable);
    let tr = $("<tr>").appendTo(thead);

    dt.columns(":visible").every(function () {
      $("<th>").text(this.header().innerText).appendTo(tr);
    });

    let tbody = $("<tbody>").appendTo($tempTable);

    for (let row of allData) {
      let $row = $("<tr>").appendTo(tbody);

      dt.columns(":visible").every(function () {
        const colKey = this.dataSrc(); // ambil key field
        const raw = row[colKey] ?? "";

        // Optional: hilangkan tag/avatar kalau perlu
        const tempDiv = $("<div>").html(raw);
        $row.append($("<td>").text(tempDiv.text()));
      });
    }

    $tempTable.appendTo("body");
    // Inisialisasi datatable export pada table sementara
    $tempTable.DataTable({
      dom: "Bfrtip",
      buttons: [
        {
          extend: method,
          title: "Export All Data - " + title,
          exportOptions: { columns: ":visible" },
          customize:
            method === "pdf"
              ? function (doc) {
                  const colCount = doc.content[1].table.body[0].length;
                  doc.content[1].table.widths = Array(colCount).fill("*");

                  doc.content[1].layout = {
                    hLineWidth: function () {
                      return 0.5;
                    },
                    vLineWidth: function () {
                      return 0.5;
                    },
                    hLineColor: function () {
                      return "#000";
                    },
                    vLineColor: function () {
                      return "#000";
                    },
                  };
                }
              : undefined,
        },
      ],
      paging: false,
      ordering: false,
      searching: false,
      destroy: true,
      initComplete: function () {
        const api = this.api();
        api.buttons(0, 0).trigger();

        setTimeout(() => {
          api.destroy();
          $tempTable.remove();
        }, 1000);
      },
    });
  }

  // mulai proses
  loopFetch();
}
