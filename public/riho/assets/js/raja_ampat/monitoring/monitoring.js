$(document).ready(function () {
  if (window.location.pathname === "/monitoring/detproyek/index") {
    // Deklarasi elemen filter
    const $tahun = $("#tahunfilter");

    // Fungsi untuk mendapatkan semua nilai filter saat ini
    function getFilterValues() {
      var tahun = $tahun.val();
      return {
        tahun,
      };
    }

    // Event handler untuk tahun
    $tahun.on("change", function () {
      var filters = getFilterValues();
      data_detail_proyek(filters.tahun);
    });

    var filters = getFilterValues();
    data_detail_proyek(filters.tahun);

    // Fungsi untuk unduh data proyek
    function unduh_data_proyek(tahun) {
      // Tampilkan progress bar
      var progressBar = $("#progressBar"); // Pastikan elemen ini ada di HTML
      var progressContainer = progressBar.parent();
      progressContainer.show(); // Tampilkan container progress bar
      progressBar.css("width", "0%");

      // Buat URL dengan parameter tahun
      var url = "/monitoring/detproyek/getunduhdata";
      if (tahun) {
        url += "?tahun=" + encodeURIComponent(tahun);
      }

      // Buat permintaan AJAX untuk mendapatkan file Excel
      var xhr = new XMLHttpRequest();
      xhr.open("GET", url, true);
      xhr.responseType = "blob"; // Set respons sebagai blob untuk file

      // Event saat proses download berlangsung (progress)
      xhr.onprogress = function (event) {
        if (event.lengthComputable) {
          var percentComplete = (event.loaded / event.total) * 100;
          progressBar.css("width", percentComplete + "%");
        }
      };

      // Event saat respons diterima
      xhr.onload = function () {
        if (this.status === 200) {
          // Buat blob dari respons dan trigger download
          var blob = new Blob([this.response], {
            type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
          });
          var url = window.URL.createObjectURL(blob);
          var a = document.createElement("a");
          a.href = url;
          a.download =
            "data_proyek_" + (tahun || new Date().getFullYear()) + ".xlsx"; // Gunakan tahun dari JS
          document.body.appendChild(a);
          a.click();
          window.URL.revokeObjectURL(url);
          document.body.removeChild(a);
        }
        // Sembunyikan progress bar setelah download selesai
        setTimeout(function () {
          progressContainer.hide(); // Sembunyikan container progress bar
        }, 1000); // Tunggu 1 detik untuk memastikan download dimulai
      };

      // Event saat error
      xhr.onerror = function () {
        alert("Terjadi kesalahan saat mengunduh file.");
        progressContainer.hide(); // Sembunyikan progress bar jika error
      };

      // Kirim permintaan
      xhr.send();
    }

    // Event listener untuk tombol unduh data Excel
    $("#unduhdataexcel").on("click", function () {
      var filters = getFilterValues();
      unduh_data_proyek(filters.tahun);
    });

    // Fungsi untuk menampilkan tabel detail proyek
    function data_detail_proyek(tahun) {
      // Tambahkan kelas CSS ke elemen #tabel_detail_proyek
      $("#tabel_detail_proyek").addClass("table-bordered table-sm");
      $.ajax({
        type: "POST",
        url: url + "monitoring/detproyek/getdetdata",
        async: true,
        data: {
          tahun: tahun,
        },
        dataType: "json",
        success: function (data) {
          // Inisialisasi Tabulator
          var table = new Tabulator("#tabel_detail_proyek", {
            data: data,
            // movableColumns: true,
            // layout: "fitColumns",
            height: "500px",
            frozenColumns: true,
            pagination: "local",
            paginationSize: 50,
            paginationSizeSelector: [25, 50, 75],
            columns: [
              {
                title: "Aksi",
                headerHozAlign: "center",
                hozAlign: "center",
                frozen: true,
                formatter: function (cell, formatterParams, onRendered) {
                  var rowData = cell.getRow().getData();
                  return `
                                    <a class="badge rounded-circle p-2 badge-light text-dark detail-btn" href="#" data-id="${rowData.id}">
                                        <i class="fa fa-search" style="cursor: pointer;"></i>
                                    </a>`;
                },
                cellClick: function (e, cell) {
                  var target = e.target.closest("a");
                  if (!target) return;

                  var row = cell.getRow();
                  var rowData = row.getData();

                  if (target.classList.contains("detail-btn")) {
                    showDetailModal(rowData.id, rowData.nik);
                  }
                },
              },
              {
                title: "Tgl Registrasi",
                field: "create_date_project",
                headerHozAlign: "center",
                hozAlign: "center",
                formatter: "datetime",
                formatterParams: {
                  inputFormat: "yyyy-MM-dd HH:mm:ss",
                  outputFormat: "dd-MMM-yyyy",
                },
                frozen: true,
              },
              {
                title: "No. WBS",
                field: "wbs_no",
                headerHozAlign: "center",
                headerFilter: "input",
                frozen: true,
              },
              {
                title: "Nama Pekerjaan",
                field: "job_name",
                headerHozAlign: "center",
                headerFilter: "input",
                minWidth: 250,
              },
              {
                title: "Perusahaan",
                field: "company_name",
                headerHozAlign: "center",
                headerFilter: "input",
                minWidth: 200,
              },
              {
                title: "Progres (%)",
                field: "progress",
                headerHozAlign: "center",
                formatter: function (cell, formatterParams, onRendered) {
                  var value = cell.getValue();
                  var color;
                  if (value < 50) {
                    color = "#FF6363"; // Merah
                  } else if (value < 100) {
                    color = "#F68537"; // Oranye
                  } else {
                    color = "#03A791"; // Hijau
                  }
                  return `<div style="background-color: ${color}; width: 100%; height: 100%; color: white; text-align: center; line-height: 2em;">${value}%</div>`;
                },
                minWidth: 50,
              },
            ],
          });
        },
        error: function (xhr, status, err) {
          console.error("Error fetching:", err);
        },
      });
    }

    // Fungsi untuk menampilkan modal detail
    function showDetailModal(id) {
      // Fetch data proyek berdasarkan id
      $.getJSON(url + `monitoring/detproyek/getdetdata/${id}`, function (data) {
        if (data) {
          console.log("Data received:", data); // Debugging

          // Fungsi untuk memformat tanggal ke DD-MM-YYYY
          function formatDate(dateStr) {
            if (!dateStr || dateStr === "N/A") return "N/A";
            const date = new Date(dateStr);
            if (isNaN(date.getTime())) return "N/A";
            const day = String(date.getDate()).padStart(2, "0");
            const month = String(date.getMonth() + 1).padStart(2, "0");
            const year = date.getFullYear();
            return `${day}-${month}-${year}`;
          }

          // Fungsi untuk memformat angka dengan separator ribuan (titik) dan desimal (koma)
          function formatNumber(num) {
            if (!num && num !== 0) return "N/A";
            num = Number(num);
            if (isNaN(num)) return "N/A";
            const parts = num.toString().split(".");
            let integerPart = parts[0];
            let decimalPart = parts[1] ? parts[1].padEnd(2, "0") : "00";
            integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            return decimalPart ? `${integerPart},${decimalPart}` : integerPart;
          }

          // Fungsi untuk mengisi data dan label per tab
          function updateTabContent(tabId) {
            // Kosongkan semua elemen dan sembunyikan semua label
            $(".modal-body p").text("");
            $(".modal-body label").hide();

            if (tabId === "#proyek1") {
              // Tampilkan label untuk tab Proyek
              $(".proyek-label").show();
              // Isi data untuk tab Proyek
              $("#nowbsheader").text(data.wbs_no || "N/A");
              $("#noso").text(data.so_no || "N/A");
              $("#reportno").text(data.report_no || "N/A");
              $("#jobname").text(data.job_name || "N/A");
              $("#companyname").text(data.company_name || "N/A");
              $("#companyaddress").text(data.company_address || "N/A");
              $("#companypic").text(data.company_pic || "N/A");
              $("#telpno").text(data.hp_no || "N/A");
              $("#email").text(data.email || "N/A");
              $("#joblocation").text(data.job_location || "N/A");
              $("#projectmanager").text(data.pm_name || "N/A");
              $("#inspector").text(data.insp_name || "N/A");
              $("#jobstartdate").text(formatDate(data.job_start_date));
              $("#jobenddate").text(formatDate(data.job_finish_date));
              $("#jobtotaltime").text(data.job_tot_time || "N/A");
            } else if (tabId === "#invoice1") {
              // Tampilkan label untuk tab Invoice
              $(".invoice-label").show();
              // Isi data untuk tab Invoice
              $("#invoicesenddate").text(formatDate(data.invoice_send_date));
              $("#invoicereceivedate").text(
                formatDate(data.invoice_receive_date)
              );
              $("#invoicereceivename").text(data.invoice_receive_name || "N/A");
            } else if (tabId === "#anggaranbiaya1") {
              // Tampilkan label untuk tab Anggaran & Biaya
              $(".anggaranbiaya-label").show();
              // Isi data untuk tab Anggaran & Biaya
              $("#arbalance").text(formatNumber(data.ar_balance));
              $("#contractamt").text(formatNumber(data.contract_amt));
              $("#revenueamt").text(formatNumber(data.revenue_amt));
              $("#budgetamt").text(formatNumber(data.budget_amt));
              $("#realamt").text(formatNumber(data.real_amt));
              $("#prsachiev").text(formatNumber(data.prs_achiev));
              $("#realdropamt").text(formatNumber(data.real_drop_amt));
            }
          }

          // Inisialisasi data untuk tab default (Proyek) saat modal dibuka
          updateTabContent("#proyek1");

          // Tampilkan modal
          $("#dataDetProyekModal").modal("show");

          // Tambahkan event listener untuk perubahan tab
          $('a[data-bs-toggle="pill"]').on("shown.bs.tab", function (e) {
            const tabId = $(e.target).attr("href"); // Ambil ID tab yang aktif
            updateTabContent(tabId);
          });
        }
      }).fail(function (xhr, status, error) {
        console.error("Error fetching detail data:", error);
      });
    }
  } else if (window.location.pathname === "/monitoring/anggaranbiaya/index") {
    // Deklarasi elemen filter
    const $nowbs = $("#nowbsfilter");

    // Fungsi untuk mendapatkan semua nilai filter saat ini
    function getFilterValues() {
      var nowbs = $nowbs.val();
      return {
        nowbs,
      };
    }

    $.getJSON(url + "proyek/dataproyekfilter", (nowbs) => {
      $nowbs
        .empty()
        .append('<option value="">Pilih No. WBS</option>')
        .append(
          nowbs.map(
            (wbs) =>
              `<option value="${wbs.id}">${wbs.wbs_no} - ${wbs.so_no}</option>`
          )
        );
      $nowbs.select2(); // Inisialisasi Select2 untuk cabang
    });

    // Event handler untuk no. wbs
    $nowbs.on("change", function () {
      var filters = getFilterValues();
      data_anggaranbiaya_proyek(filters.nowbs);
    });

    var filters = getFilterValues();
    data_anggaranbiaya_proyek(filters.nowbs);

    // Fungsi untuk unduh data proyek
    function unduh_data_anggaranbiaya(nowbs) {
      // Tampilkan progress bar
      var progressBar = $("#progressBar"); // Pastikan elemen ini ada di HTML
      var progressContainer = progressBar.parent();
      progressContainer.show(); // Tampilkan container progress bar
      progressBar.css("width", "0%");

      // Buat URL dengan parameter tahun
      var url = "/monitoring/anggaranbiaya/getunduhdata";
      if (nowbs) {
        url += "?nowbs=" + encodeURIComponent(nowbs);
      }

      // Buat permintaan AJAX untuk mendapatkan file Excel
      var xhr = new XMLHttpRequest();
      xhr.open("GET", url, true);
      xhr.responseType = "blob"; // Set respons sebagai blob untuk file

      // Event saat proses download berlangsung (progress)
      xhr.onprogress = function (event) {
        if (event.lengthComputable) {
          var percentComplete = (event.loaded / event.total) * 100;
          progressBar.css("width", percentComplete + "%");
        }
      };

      // Event saat respons diterima
      xhr.onload = function () {
        if (this.status === 200) {
          // Buat blob dari respons dan trigger download
          var blob = new Blob([this.response], {
            type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
          });
          var url = window.URL.createObjectURL(blob);
          var a = document.createElement("a");
          a.href = url;
          a.download = "biaya_anggaran" + nowbs + ".xlsx"; // Gunakan tahun dari JS
          document.body.appendChild(a);
          a.click();
          window.URL.revokeObjectURL(url);
          document.body.removeChild(a);
        }
        // Sembunyikan progress bar setelah download selesai
        setTimeout(function () {
          progressContainer.hide(); // Sembunyikan container progress bar
        }, 1000); // Tunggu 1 detik untuk memastikan download dimulai
      };

      // Event saat error
      xhr.onerror = function () {
        alert("Terjadi kesalahan saat mengunduh file.");
        progressContainer.hide(); // Sembunyikan progress bar jika error
      };

      // Kirim permintaan
      xhr.send();
    }

    // Event listener untuk tombol unduh data Excel
    $("#unduhdataexcel").on("click", function () {
      var filters = getFilterValues();
      unduh_data_anggaranbiaya(filters.nowbs);
    });

    // Fungsi untuk menampilkan tabel detail proyek
    function data_anggaranbiaya_proyek(nowbs) {
      // Tambahkan kelas CSS ke elemen #tabel_detail_proyek
      $("#tabel_anggaran_biaya").addClass("table-bordered table-sm");
      $.ajax({
        type: "POST",
        url: url + "monitoring/anggaranbiaya/getdetdata",
        async: true,
        data: {
          nowbs: nowbs,
        },
        dataType: "json",
        success: function (data) {
          // Inisialisasi Tabulator
          var table = new Tabulator("#tabel_anggaran_biaya", {
            data: data,
            // movableColumns: true,
            // layout: "fitColumns",
            height: "500px",
            frozenColumns: true,
            pagination: "local",
            paginationSize: 50,
            paginationSizeSelector: [25, 50, 75],
            columns: [
              {
                title: "Aksi",
                headerHozAlign: "center",
                hozAlign: "center",
                frozen: true,
                formatter: function (cell, formatterParams, onRendered) {
                  var rowData = cell.getRow().getData();
                  return `
                                    <a class="badge rounded-circle p-2 badge-light text-dark detail-btn" href="#" data-id="${rowData.id}">
                                        <i class="fa fa-search" style="cursor: pointer;"></i>
                                    </a>`;
                },
                cellClick: function (e, cell) {
                  var target = e.target.closest("a");
                  if (!target) return;

                  var row = cell.getRow();
                  var rowData = row.getData();

                  if (target.classList.contains("detail-btn")) {
                    // Panggil fungsi untuk menampilkan detail
                    showDetailModal(rowData.id_ref, rowData.coa);
                  }
                },
              },
              {
                title: "COA",
                field: "coa",
                headerHozAlign: "center",
                formatter: function (cell, formatterParams, onRendered) {
                  var rowData = cell.getRow().getData();
                  return rowData.coa + " - " + rowData.coa_name;
                },
                frozen: true,
                minWidth: 200,
              },
              {
                title: "Nilai Anggaran",
                field: "budget_amt",
                headerHozAlign: "center",
                hozAlign: "right",
                formatter: "money",
                formatterParams: { decimal: ",", thousand: "." },
                minWidth: 150,
              },
              {
                title: "Nilai Realisasi Biaya",
                field: "real_amt",
                headerHozAlign: "center",
                hozAlign: "right",
                formatter: "money",
                formatterParams: { decimal: ",", thousand: "." },
                minWidth: 150,
              },
              {
                title: "Nilai Dropping",
                field: "real_drop_amt",
                headerHozAlign: "center",
                hozAlign: "right",
                formatter: "money",
                formatterParams: { decimal: ",", thousand: "." },
                minWidth: 150,
              },
              {
                title: "Nilai Sisa Anggaran",
                field: "net_plan_amt",
                headerHozAlign: "center",
                hozAlign: "right",
                formatter: "money",
                formatterParams: { decimal: ",", thousand: "." },
                minWidth: 150,
              },
            ],
          });
        },
        error: function (xhr, status, err) {
          console.error("Error fetching:", err);
        },
      });
    }

    // Fungsi untuk menampilkan modal dengan data detail
    function showDetailModal(id_ref, coa) {
      // Log parameter untuk debugging
      console.log("Mengirim parameter - nowbs:", id_ref, "coa:", coa);

      // Deklarasi variabel untuk tabel Tabulator agar hanya diinisialisasi sekali
      let realisasiTable = null;
      let droppingTable = null;

      // Fungsi untuk menginisialisasi atau memperbarui tabel berdasarkan tab
      function updateTabContent(tabId) {
        if (tabId === "#realisasibiaya1") {
          $("#tabel_detail_realisasi").addClass("table-bordered table-sm");
          $.ajax({
            type: "POST",
            url: url + "monitoring/anggaranbiaya/getdatadetrealisasi",
            data: {
              id_ref: id_ref,
              coa: coa,
            },
            dataType: "json",
            success: function (data) {
              if (!realisasiTable) {
                // Inisialisasi tabel pertama kali
                realisasiTable = new Tabulator("#tabel_detail_realisasi", {
                  data: data,
                  layout: "fitColumns",
                  height: "350px",
                  responsiveLayout: "collapse",
                  pagination: "local",
                  paginationSize: 50,
                  paginationSizeSelector: [25, 50, 75],
                  columns: [
                    {
                      title: "Tgl Pembuatan",
                      field: "create_date",
                      headerHozAlign: "center",
                      hozAlign: "center",
                      formatter: "datetime",
                      formatterParams: {
                        inputFormat: "yyyy-MM-dd HH:mm:ss",
                        outputFormat: "dd-MMM-yyyy",
                      },
                    },
                    {
                      title: "Uraian",
                      field: "description",
                      headerHozAlign: "center",
                      minWidth: 200,
                    },
                    {
                      title: "Nilai Realisasi Biaya",
                      field: "real_amt",
                      headerHozAlign: "center",
                      hozAlign: "right",
                      formatter: "money",
                      formatterParams: { decimal: ",", thousand: "." },
                      minWidth: 150,
                    },
                    {
                      title: "Nama Pembuat",
                      field: "emp_name",
                      headerHozAlign: "center",
                      minWidth: 200,
                    },
                  ],
                });
              } else {
                // Perbarui data jika tabel sudah ada
                realisasiTable.setData(data);
              }
            },
            error: function (xhr, status, err) {
              console.error("Error fetching realisasi data:", err);
              alert("Gagal memuat data realisasi biaya: " + xhr.responseText);
            },
          });
        } else if (tabId === "#dropping1") {
          $("#tabel_detail_dropping").addClass("table-bordered table-sm");
          $.ajax({
            type: "POST",
            url: url + "monitoring/anggaranbiaya/getdatadetdropping",
            data: {
              id_ref: id_ref,
              coa: coa,
            },
            dataType: "json",
            success: function (data) {
              if (!droppingTable) {
                // Inisialisasi tabel pertama kali
                droppingTable = new Tabulator("#tabel_detail_dropping", {
                  data: data,
                  layout: "fitColumns",
                  height: "350px",
                  responsiveLayout: "collapse",
                  pagination: "local",
                  paginationSize: 50,
                  paginationSizeSelector: [25, 50, 75],
                  columns: [
                    {
                      title: "Tgl Pembuatan",
                      field: "create_date",
                      headerHozAlign: "center",
                      hozAlign: "center",
                      formatter: "datetime",
                      formatterParams: {
                        inputFormat: "yyyy-MM-dd HH:mm:ss",
                        outputFormat: "dd-MMM-yyyy",
                      },
                    },
                    {
                      title: "Uraian",
                      field: "description",
                      headerHozAlign: "center",
                      minWidth: 200,
                    },
                    {
                      title: "Nilai Dropping",
                      field: "real_drop_amt",
                      headerHozAlign: "center",
                      hozAlign: "right",
                      formatter: "money",
                      formatterParams: { decimal: ",", thousand: "." },
                      minWidth: 150,
                    },
                    {
                      title: "Nama Pembuat",
                      field: "emp_name",
                      headerHozAlign: "center",
                      minWidth: 200,
                    },
                  ],
                });
              } else {
                // Perbarui data jika tabel sudah ada
                droppingTable.setData(data);
              }
            },
            error: function (xhr, status, err) {
              console.error("Error fetching dropping data:", err);
              alert("Gagal memuat data dropping: " + xhr.responseText);
            },
          });
        }
      }

      // Inisialisasi data untuk tab default (Realisasi Biaya) saat modal dibuka
      updateTabContent("#realisasibiaya1");

      // Tampilkan modal
      $("#dataDetRealisasiModal").modal("show");

      // Event listener untuk perubahan tab
      $('a[data-bs-toggle="pill"]').on("shown.bs.tab", function (e) {
        const tabId = $(e.target).attr("href");
        updateTabContent(tabId);
      });
    }
  } else if (
    window.location.pathname === "/monitoring/pembayaranpiutang/index"
  ) {
    // Deklarasi elemen filter
    const $tahun = $("#tahunfilter");

    // Fungsi untuk mendapatkan semua nilai filter saat ini
    function getFilterValues() {
      var tahun = $tahun.val();
      return {
        tahun,
      };
    }

    // Event handler untuk tahun
    $tahun.on("change", function () {
      var filters = getFilterValues();
      data_pembayaran_piutang(filters.tahun);
    });

    var filters = getFilterValues();
    data_pembayaran_piutang(filters.tahun);

    // Fungsi untuk unduh data pembayaran piutang
    function unduh_data_pembayaran_piutang(tahun) {
      // Tampilkan progress bar
      var progressBar = $("#progressBar"); // Pastikan elemen ini ada di HTML
      var progressContainer = progressBar.parent();
      progressContainer.show(); // Tampilkan container progress bar
      progressBar.css("width", "0%");

      // Buat URL dengan parameter tahun
      var url = "/monitoring/pembayaranpiutang/getunduhdata";
      if (tahun) {
        url += "?tahun=" + encodeURIComponent(tahun);
      }

      // Buat permintaan AJAX untuk mendapatkan file Excel
      var xhr = new XMLHttpRequest();
      xhr.open("GET", url, true);
      xhr.responseType = "blob"; // Set respons sebagai blob untuk file

      // Event saat proses download berlangsung (progress)
      xhr.onprogress = function (event) {
        if (event.lengthComputable) {
          var percentComplete = (event.loaded / event.total) * 100;
          progressBar.css("width", percentComplete + "%");
        }
      };

      // Event saat respons diterima
      xhr.onload = function () {
        if (this.status === 200) {
          // Buat blob dari respons dan trigger download
          var blob = new Blob([this.response], {
            type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
          });
          var url = window.URL.createObjectURL(blob);
          var a = document.createElement("a");
          a.href = url;
          a.download =
            "data_pembayaran_piutang_" +
            (tahun || new Date().getFullYear()) +
            ".xlsx"; // Gunakan tahun dari JS
          document.body.appendChild(a);
          a.click();
          window.URL.revokeObjectURL(url);
          document.body.removeChild(a);
        }
        // Sembunyikan progress bar setelah download selesai
        setTimeout(function () {
          progressContainer.hide(); // Sembunyikan container progress bar
        }, 1000); // Tunggu 1 detik untuk memastikan download dimulai
      };

      // Event saat error
      xhr.onerror = function () {
        alert("Terjadi kesalahan saat mengunduh file.");
        progressContainer.hide(); // Sembunyikan progress bar jika error
      };

      // Kirim permintaan
      xhr.send();
    }

    // Event listener untuk tombol unduh data Excel
    $("#unduhdataexcel").on("click", function () {
      var filters = getFilterValues();
      unduh_data_pembayaran_piutang(filters.tahun);
    });

    // Fungsi untuk menampilkan tabel pembayaran piutang
    function data_pembayaran_piutang(tahun) {
      // Tambahkan kelas CSS ke elemen #tabel_pembayaran_piutang
      $("#tabel_pembayaran_piutang").addClass("table-bordered table-sm");
      $.ajax({
        type: "POST",
        url: url + "monitoring/pembayaranpiutang/getdetdata",
        async: true,
        data: {
          tahun: tahun,
        },
        dataType: "json",
        success: function (data) {
          // Inisialisasi Tabulator
          var table = new Tabulator("#tabel_pembayaran_piutang", {
            data: data,
            // renderHorizontal: "virtual", // Enable horizontal virtual DOM
            // movableColumns: true,
            // layout: "fitData", // Changed from "fitDataTable" to "fitColumns"
            height: "500px",
            frozenColumns: true,
            pagination: "local",
            paginationSize: 50,
            paginationSizeSelector: [25, 50, 75],
            columns: [
              {
                title: "Aksi",
                headerHozAlign: "center",
                hozAlign: "center",
                frozen: true,
                formatter: function (cell, formatterParams, onRendered) {
                  var rowData = cell.getRow().getData();
                  return `
                              <a class="badge rounded-circle p-2 badge-light text-dark detail-btn" href="#" data-id="${rowData.id}">
                                  <i class="fa fa-search" style="cursor: pointer;"></i>
                              </a>`;
                },
                cellClick: function (e, cell) {
                  var target = e.target.closest("a");
                  if (!target) return;

                  var row = cell.getRow();
                  var rowData = row.getData();

                  if (target.classList.contains("detail-btn")) {
                    // Panggil fungsi untuk menampilkan detail
                    showDetailModal(rowData.id);
                  }
                },
                minWidth: 100, // Added for action column
              },
              {
                title: "Bulan",
                field: "month",
                headerHozAlign: "center",
                hozAlign: "center",
                frozen: true,
                minWidth: 50,
              },
              {
                title: "No. WBS",
                field: "wbs_no",
                headerHozAlign: "center",
                headerFilter: "input",
                frozen: true,
                minWidth: 150,
              },
              {
                title: "No. SO",
                field: "so_no",
                headerHozAlign: "center",
                headerFilter: "input",
                minWidth: 150,
              },
              {
                title: "Perusahaan",
                field: "company_name",
                headerHozAlign: "center",
                headerFilter: "input",
                minWidth: 200, // Increased for better readability
              },
              {
                title: "Nilai Pendapatan",
                field: "revenue_amt",
                headerHozAlign: "center",
                hozAlign: "right",
                formatter: "money",
                formatterParams: { decimal: ",", thousand: "." },
                minWidth: 150,
              },
              {
                title: "Total Pembayaran",
                field: "payment_amt",
                headerHozAlign: "center",
                hozAlign: "right",
                formatter: "money",
                formatterParams: { decimal: ",", thousand: "." },
                minWidth: 150,
              },
              {
                title: "Saldo Piutang",
                field: "ar_balance",
                headerHozAlign: "center",
                hozAlign: "right",
                formatter: "money",
                formatterParams: { decimal: ",", thousand: "." },
                minWidth: 150,
              },
            ],
          });
        },
        error: function (xhr, status, err) {
          console.error("Error fetching:", err);
        },
      });
    }

    // Fungsi untuk menampilkan modal dengan data detail
    function showDetailModal(id) {
      // Log untuk debugging
      console.log("Mengirim parameter - id_ref:", id);

      // Tampilkan modal
      $("#dataPembayaranPiutangModal").modal("show");

      // Tambahkan class pada tabel
      $("#tabel_detail_pembayaran_piutang").addClass("table-bordered table-sm");

      // Inisialisasi variabel tabel
      let paymentTable = null;

      // Ambil data dan tampilkan dengan Tabulator
      $.ajax({
        type: "POST",
        url: url + "monitoring/pembayaranpiutang/getdetpembayaranpiutang",
        data: {
          id_ref: id,
        },
        dataType: "json",
        success: function (data) {
          if (!paymentTable) {
            // Inisialisasi tabel Tabulator pertama kali
            paymentTable = new Tabulator("#tabel_detail_pembayaran_piutang", {
              data: data,
              layout: "fitColumns",
              height: "350px",
              responsiveLayout: "collapse",
              pagination: "local",
              paginationSize: 50,
              paginationSizeSelector: [25, 50, 75],
              columns: [
                {
                  title: "Tgl Pembuatan",
                  field: "create_date",
                  headerHozAlign: "center",
                  hozAlign: "center",
                },
                {
                  title: "Uraian",
                  field: "description",
                  headerHozAlign: "center",
                  minWidth: 200,
                },
                {
                  title: "Nilai Pembayaran",
                  field: "payment_amt",
                  headerHozAlign: "center",
                  hozAlign: "right",
                  formatter: "money",
                  formatterParams: { decimal: ",", thousand: "." },
                  minWidth: 150,
                },
                {
                  title: "Nama Pembuat",
                  field: "emp_name",
                  headerHozAlign: "center",
                  minWidth: 200,
                },
              ],
            });
          } else {
            // Update data jika tabel sudah ada
            paymentTable.setData(data);
          }
        },
        error: function (xhr, status, err) {
          console.error("Error fetching pembayaran piutang data:", err);
          alert("Gagal memuat data pembayaran piutang: " + xhr.responseText);
        },
      });
    }
  }
});
