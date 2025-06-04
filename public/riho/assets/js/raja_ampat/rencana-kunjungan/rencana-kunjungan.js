$(document).ready(function () {
  if (window.location.pathname == "/rencana/verifikasi") {
    // Variabel global untuk tabel
    let table;

    // Mendapatkan nilai awal
    var tanggal_acc =
      $("#tanggalAccKunjungan").val() || new Date().toISOString().split("T")[0]; // Default ke tanggal hari ini jika kosong
    var sales_marketing = $("#salesMarketing").val();

    // Set tanggal hari ini sebagai default dan batas minimum
    var today = new Date().toISOString().split("T")[0]; // Format YYYY-MM-DD
    $("#tanggalAccKunjungan").attr("min", today); // Nonaktifkan tanggal sebelum hari ini
    $("#tanggalAccKunjungan").val(today); // Set tanggal default ke hari ini

    // Data kunjungan yang akan diverifikasi (dilepas dari komentar untuk memuat tabel awal)
    tabel_verifikasi_rencana_kunjungan(tanggal_acc, sales_marketing);

    // Fetch cabang berdasarkan session branch_id
    if (branchId !== "11") {
      // Jika branch_id bukan '11', tampilkan hanya cabang dari session dan disable dropdown
      $("#cabangRencanaOps")
        .empty()
        .append(
          `<option value="${branchId}" selected>${branchId} - ${branchName}</option>`
        )
        .prop("disabled", true); // Nonaktifkan dropdown
    } else {
      // Jika branch_id adalah '11', ambil semua cabang dari API
      $.getJSON(url + "master/cabang", (branches) => {
        $("#cabangRencanaOps")
          .empty()
          .append('<option value="">Pilih Cabang</option>')
          .append(
            branches.map(
              (b) => `<option value="${b.branch_id}">${b.branch_name}</option>`
            )
          );
      });
    }

    $("#cabangRencanaOps").on("change", function () {
      const branchId = $(this).val();
      console.log("Cabang terpilih: ", branchId);
      $("#salesMarketing")
        .empty()
        .append('<option value="" selected>Pilih Sales/Marketing</option>');

      if (branchId) {
        $.ajax({
          url: url + "master/salesmarketing",
          method: "POST",
          data: { branch_id: branchId },
          dataType: "json",
          success: function (data) {
            data.forEach((sales) => {
              $("#salesMarketing").append(
                `<option value="${sales.nik}">${sales.name}</option>`
              );
            });
          },
        });
      }
    });

    // PERUBAHAN FILTER DROPDOWN
    // Tanggal
    $("#tanggalAccKunjungan").change(function () {
      var tanggal_acc = $(this).val();
      var sales_marketing = $("#salesMarketing").val();

      console.log("Tanggal dipilih: " + tanggal_acc); // Debugging
      tabel_verifikasi_rencana_kunjungan(tanggal_acc, sales_marketing);
    });

    // Tim Sales Marketing
    $("#salesMarketing").change(function () {
      var tanggal_acc = $("#tanggalAccKunjungan").val(); // Perbaiki ID
      var sales_marketing = $(this).val();

      console.log(
        "tanggal = " + tanggal_acc + " sales/marketing = " + sales_marketing
      );
      tabel_verifikasi_rencana_kunjungan(tanggal_acc, sales_marketing);
    });

    // Fungsi tabel_verifikasi_rencana_kunjungan
    function tabel_verifikasi_rencana_kunjungan(tanggal_acc, sales_marketing) {
      if (table) {
        table.destroy();
      }

      $.ajax({
        type: "GET",
        url: url + "/pipeline/groupuser",
        dataType: "json",
        success: function (response) {
          let group_id = response.group_id;
          let columns = [
            { title: "Sales/Marketing", field: "nik", visible: false },
            {
              title: "Tgl Kunjungan",
              field: "date",
              headerHozAlign: "center",
              hozAlign: "center",
              formatter: "datetime",
              formatterParams: {
                inputFormat: "yyyy-MM-dd",
                outputFormat: "dd-MMM-yyyy",
              },
            },
            {
              title: "Pelanggan",
              field: "cust_id",
              headerHozAlign: "center",
              formatter: function (cell, formatterParams, onRendered) {
                var rowData = cell.getRow().getData();
                return rowData.cust_id + " - " + rowData.cust_name;
              },
            },
            {
              title: "Deskripsi",
              field: "description",
              headerHozAlign: "center",
            },
          ];

          if (group_id === "09" || group_id === "10") {
            columns.push({
              title: "User Pelanggan",
              field: "cust_user_name",
              headerHozAlign: "center",
            });
          }

          columns.push(
            {
              title: "Aksi",
              field: "",
              headerHozAlign: "center",
              hozAlign: "center",
              formatter: function (cell, formatterParams, onRendered) {
                return `
                <a class="badge rounded-circle p-2 badge-light text-dark" href="#">
                  <i class="fa fa-search" style="cursor: pointer;"></i>
                </a>`;
              },
              cellClick: function (e, cell) {
                var rowData = cell.getRow().getData();
                showDetailModal(
                  rowData.nik,
                  rowData.date,
                  rowData.cust_id,
                  rowData.cust_name
                );
              },
            },
            // Dalam definisi kolom "Setujui"
            {
              title: "Setujui",
              field: "flg_approve",
              hozAlign: "center",
              headerHozAlign: "center",
              titleFormatter: function (cell) {
                var checkbox = document.createElement("input");
                checkbox.type = "checkbox";
                checkbox.id = "header-checkbox";

                checkbox.addEventListener("click", function () {
                  let isChecked = this.checked;
                  table.getRows().forEach((row) => {
                    let rowCheckbox = row
                      .getElement()
                      .querySelector(".row-checkbox");
                    if (rowCheckbox) {
                      rowCheckbox.checked = isChecked;
                    }
                  });
                });

                return checkbox;
              },
              formatter: function (cell) {
                let isApproved = cell.getValue();
                return `
                  <input type="checkbox" class="row-checkbox" ${
                    isApproved ? "checked" : ""
                  } style="margin-right: 10px;">
                  <button class="btn btn-sm btn-danger reject-btn"><i class='fa fa-edit'></i></button>
                `;
              },
              cellClick: function (e, cell) {
                let row = cell.getRow(); // Dapatkan objek baris Tabulator
                let rowData = row.getData();
                let target = e.target;

                if (target.classList.contains("reject-btn")) {
                  $("#rejectModal").modal("show");
                  $("#reject_reason").val("");
                  $("#rejectModal").data("row", row); // Simpan objek baris
                }
              },
            }
          );

          $.ajax({
            type: "POST",
            url: url + "rencana/verifikasi/getdata",
            data: {
              tanggal: tanggal_acc,
              sales_marketing: sales_marketing,
            },
            dataType: "json",
            success: function (data) {
              table = new Tabulator("#tabel_verifikasi_rencana_kunjungan", {
                data: data,
                height: "350px",
                pagination: "local",
                paginationSize: 25,
                paginationSizeSelector: [10, 25, 50],
                layout: "fitColumns",
                columns: columns,
              });

              // Event untuk tombol Simpan Data (Approve All)
              $("#saveApproveAll").on("click", function () {
                let rowsToApprove = table
                  .getRows()
                  .filter(
                    (row) =>
                      row.getElement().querySelector(".row-checkbox").checked
                  );

                if (rowsToApprove.length === 0) {
                  Swal.fire({
                    title: "Tidak ada data",
                    text: "Tidak ada data yang dipilih untuk disetujui.",
                    icon: "info",
                    confirmButtonText: "OK",
                  });
                  return;
                }

                Swal.fire({
                  title: "Apakah Anda yakin?",
                  text: "Data yang dipilih akan disetujui.",
                  icon: "warning",
                  showCancelButton: true,
                  confirmButtonText: "Ya, setujui!",
                  cancelButtonText: "Batal",
                }).then((result) => {
                  if (result.isConfirmed) {
                    // Kumpulkan kombinasi unik dari cust_id, nik, date
                    let uniqueCombinations = {};
                    rowsToApprove.forEach((row) => {
                      let rowData = row.getData();
                      let key = `${rowData.cust_id}_${rowData.nik}_${rowData.date}`;
                      if (!uniqueCombinations[key]) {
                        uniqueCombinations[key] = {
                          cust_id: rowData.cust_id,
                          nik: rowData.nik,
                          date: rowData.date,
                        };
                      }
                    });

                    let combinations = Object.values(uniqueCombinations);

                    // Kirim kombinasi ke server untuk diupdate
                    $.ajax({
                      type: "POST",
                      url: url + "/rencana/verifikasi/updateall",
                      data: {
                        combinations: JSON.stringify(combinations),
                        flg_approve: true,
                        reason_reject: "",
                      },
                      dataType: "json",
                      success: function (response) {
                        if (response.status === "success") {
                          Swal.fire({
                            title: "Berhasil!",
                            text: "Data yang dipilih telah disetujui.",
                            icon: "success",
                            confirmButtonText: "OK",
                          }).then(() => {
                            window.location.href = "/rencana/verifikasi";
                          });
                        } else {
                          Swal.fire({
                            title: "Gagal!",
                            text: "Terjadi kesalahan saat menyimpan data.",
                            icon: "error",
                            confirmButtonText: "OK",
                          });
                        }
                      },
                      error: function (xhr) {
                        console.error("Error:", xhr.responseText);
                        Swal.fire({
                          title: "Gagal!",
                          text: "Terjadi kesalahan saat menyimpan data.",
                          icon: "error",
                          confirmButtonText: "OK",
                        });
                      },
                    });
                  }
                });
              });

              // Event untuk tombol Simpan di modal penolakan
              $("#saveReject").on("click", function () {
                let reason = $("#reject_reason").val().trim();
                if (reason === "") {
                  alert("Silakan isi alasan penolakan!");
                  return;
                }

                let row = $("#rejectModal").data("row"); // Ambil objek baris
                let rowData = row.getData();

                Swal.fire({
                  title: "Apakah Anda yakin?",
                  text: "Data akan ditolak dengan alasan: " + reason,
                  icon: "warning",
                  showCancelButton: true,
                  confirmButtonText: "Ya, tolak!",
                  cancelButtonText: "Batal",
                }).then((result) => {
                  if (result.isConfirmed) {
                    updateVerifRencana(
                      rowData.cust_id,
                      rowData.nik,
                      rowData.date,
                      false,
                      reason
                    ).then(() => {
                      row.delete(); // Hapus baris
                      $("#rejectModal").modal("hide"); // Tutup modal
                    });
                  }
                });
              });
            },
          });
        },
      });
    }

    // Fungsi untuk menampilkan modal dengan data detail
    function showDetailModal(nik, date, cust_id, cust_name) {
      // Set kode_pelanggan dan nama_pelanggan di header modal
      $("#kode_pelanggan").text(cust_id);
      $("#nama_pelanggan").text(cust_name);

      // Ambil group_id dari endpoint /pipeline/groupuser
      $.ajax({
        type: "GET",
        url: url + "/pipeline/groupuser",
        dataType: "json",
        success: function (response) {
          let group_id = response.group_id;

          // Definisikan kolom dasar untuk Tabulator
          let columns = [
            {
              title: "Grup Produk",
              field: "group_id",
              headerHozAlign: "center",
              formatter: function (cell, formatterParams, onRendered) {
                var rowData = cell.getRow().getData();
                return rowData.group_id + " - " + rowData.group_name;
              },
            },
            {
              title: "SubGrup Produk",
              field: "subgroup_id",
              headerHozAlign: "center",
              formatter: function (cell, formatterParams, onRendered) {
                var rowData = cell.getRow().getData();
                return rowData.subgroup_id + " - " + rowData.subgroup_name;
              },
            },
            {
              title: "Kelas Produk",
              field: "class_id",
              headerHozAlign: "center",
              formatter: function (cell, formatterParams, onRendered) {
                var rowData = cell.getRow().getData();
                return rowData.class_id + " - " + rowData.class_name;
              },
            },
          ];

          // Tambahkan kolom User Pelanggan jika group_id adalah '02' atau '05'
          if (group_id === "02" || group_id === "05") {
            columns.push({
              title: "User Pelanggan",
              field: "cust_user_name",
              headerHozAlign: "center",
            });
          }

          // Ambil data detail
          $.ajax({
            type: "POST",
            url: url + "rencana/verifikasi/getdetdata",
            data: {
              sales_marketing: nik,
              tanggal: date,
              cust_id: cust_id,
            },
            dataType: "json",
            success: function (data) {
              // Sembunyikan tabel statis dan kosongkan tbody untuk keamanan
              $("#detailTable").hide();
              $("#detailTable tbody").empty();

              if (data.length > 0) {
                // Inisialisasi tabel Tabulator
                var detailTable = new Tabulator(
                  "#tabel_det_verifikasi_rencana_kunjungan",
                  {
                    data: data,
                    height: "250px",
                    pagination: "local",
                    paginationSize: 10,
                    paginationSizeSelector: [5, 10, 20],
                    layout: "fitColumns",
                    columns: columns,
                  }
                );
              } else {
                // Tampilkan tabel statis dengan pesan "Tidak ada data"
                $("#detailTable").show();
                // Sesuaikan colspan berdasarkan jumlah kolom
                let colspan = group_id === "02" || group_id === "05" ? 5 : 4;
                $("#detailTable tbody").append(
                  `<tr><td colspan='${colspan}'>Tidak ada data</td></tr>`
                );
              }

              $("#detailModal").modal("show");
            },
            error: function (xhr, status, error) {
              console.error("Error:", error);
              alert("Terjadi kesalahan saat mengambil data detail.");
            },
          });
        },
        error: function (xhr, status, error) {
          console.error("Error:", error);
          alert("Terjadi kesalahan saat mengambil group_id.");
        },
      });
    }

    // Fungsi AJAX untuk mengupdate verifikasi ke server
    function updateVerifRencana(cust_id, nik, date, status, reason) {
      return new Promise((resolve, reject) => {
        $.ajax({
          type: "POST",
          url: url + "/rencana/verifikasi/update",
          data: {
            cust_id: cust_id,
            nik: nik,
            date: date,
            flg_approve: status,
            reason_reject: reason,
          },
          dataType: "json",
          success: function (response) {
            if (response.status === "success") {
              let message = status
                ? `Rencana kunjungan untuk cust_id: ${cust_id}, nik: ${nik}, date: ${date} disetujui`
                : `Rencana kunjungan untuk cust_id: ${cust_id}, nik: ${nik}, date: ${date} ditolak dengan alasan: ${reason}`;
              toastr.success(message);
              resolve();
            } else {
              toastr.error("Gagal memperbarui data.");
              reject();
            }
          },
          error: function (xhr) {
            toastr.error("Terjadi kesalahan saat memperbarui data.");
            console.error("Error:", xhr.responseText);
            reject();
          },
        });
      });
    }
  } else if (window.location.pathname === "/rencana/monitoring") {
    // Inisialisasi tanggal awal
    let currentDate = new Date().toJSON().slice(0, 10);

    // Inisialisasi DateRangePicker
    $("#rentangTanggalMon").daterangepicker({
      startDate: currentDate,
      endDate: currentDate,
      locale: {
        format: "YYYY-MM-DD",
      },
    });

    // Fungsi untuk mendapatkan semua nilai filter saat ini
    function getFilterValues() {
      var dates = $("#rentangTanggalMon").data("daterangepicker");
      var tgl_1 = dates.startDate.format("YYYY-MM-DD");
      var tgl_2 = dates.endDate.format("YYYY-MM-DD");
      var sales_marketing = $("#salesMarketing").val();
      return {
        tgl_1,
        tgl_2,
        sales_marketing,
      };
    }

    // Panggil fungsi awal untuk menampilkan data
    var initialFilters = getFilterValues();
    tabel_monitoring_rencana_kunjungan(
      initialFilters.tgl_1,
      initialFilters.tgl_2,
      initialFilters.sales_marketing
    );

    // Fetch cabang berdasarkan session branch_id
    if (branchId !== "11") {
      // Jika branch_id bukan '11', tampilkan hanya cabang dari session dan disable dropdown
      $("#cabangRencanaOps")
        .empty()
        .append(
          `<option value="${branchId}" selected>${branchId} - ${branchName}</option>`
        )
        .prop("disabled", true); // Nonaktifkan dropdown
    } else {
      // Jika branch_id adalah '11', ambil semua cabang dari API
      $.getJSON(url + "master/cabang", (branches) => {
        $("#cabangRencanaOps")
          .empty()
          .append('<option value="">Pilih Cabang</option>')
          .append(
            branches.map(
              (b) => `<option value="${b.branch_id}">${b.branch_name}</option>`
            )
          );
      });
    }

    // Event handler untuk cabang
    $("#cabangRencanaOps").on("change", function () {
      const branchId = $(this).val();
      console.log("Cabang terpilih: ", branchId);
      $("#salesMarketing")
        .empty()
        .append('<option value="" selected>Pilih Sales/Marketing</option>');

      if (branchId) {
        $.ajax({
          url: url + "master/salesmarketing",
          method: "POST",
          data: { branch_id: branchId },
          dataType: "json",
          success: function (data) {
            data.forEach((sales) => {
              $("#salesMarketing").append(
                `<option value="${sales.nik}">${sales.name}</option>`
              );
            });
          },
        });
      }
    });

    // Event handler untuk DateRangePicker
    $("#rentangTanggalMon").on("apply.daterangepicker", function (ev, picker) {
      var filters = getFilterValues();
      tabel_monitoring_rencana_kunjungan(
        filters.tgl_1,
        filters.tgl_2,
        filters.sales_marketing
      );
    });

    // Event handler untuk Sales/Marketing
    $("#salesMarketing").change(function () {
      var filters = getFilterValues();
      tabel_monitoring_rencana_kunjungan(
        filters.tgl_1,
        filters.tgl_2,
        filters.sales_marketing
      );
    });

    // Inisialisasi tabel monitoring Rencana Kunjungan
    function tabel_monitoring_rencana_kunjungan(tgl_1, tgl_2, sales_marketing) {
      $.ajax({
        type: "GET",
        url: url + "/pipeline/groupuser", // Ambil group_id dari server
        dataType: "json",
        success: function (response) {
          let group_id = response.group_id;
          let columns = [
            { title: "Sales/Marketing", field: "nik", visible: false },
            {
              title: "Non route?",
              field: "flg_non_route",
              headerHozAlign: "center",
              hozAlign: "center",
              formatter: function (cell, formatterParams) {
                var value = cell.getValue();
                console.log("Status value:", value); // Debugging: Log the value to ensure correctness
                if (value === "t") {
                  return "<i class='fa fa-check' style='color:#03A791'></i>";
                } else if (value === "f") {
                  return "<i class='fa fa-times' style='color:#FF5677'></i>";
                }
              },
            },
            {
              title: "Absen?",
              field: "flg_absence",
              headerHozAlign: "center",
              hozAlign: "center",
              formatter: function (cell, formatterParams) {
                var value = cell.getValue();
                console.log("Status value:", value); // Debugging: Log the value to ensure correctness

                if (value === "t") {
                  return "<i class='fa fa-check' style='color:#03A791'></i>";
                } else if (value === "f") {
                  return "<i class='fa fa-times' style='color:#FF5677'></i>";
                }
              },
            },
            {
              title: "Tanggal Rencana",
              field: "date",
              headerHozAlign: "center",
              hozAlign: "center",
              formatter: "datetime",
              formatterParams: {
                inputFormat: "yyyy-MM-dd",
                outputFormat: "dd-MMM-yyyy",
              },
            },
            {
              title: "Pelanggan",
              field: "cust_id", // Field utama bisa tetap salah satu dari keduanya
              headerHozAlign: "center",
              formatter: function (cell, formatterParams, onRendered) {
                // Ambil data dari baris
                var rowData = cell.getRow().getData();
                // Gabungkan cust_id dan cust_name
                return rowData.cust_id + " - " + rowData.cust_name;
              },
            },
          ];

          if (group_id === "09" || group_id === "10") {
            columns.push({
              title: "User Pelanggan",
              field: "cust_user_name",
              headerHozAlign: "center",
            });
          }

          columns.push(
            {
              title: "Deskripsi",
              field: "description",
              headerHozAlign: "center",
            },
            {
              title: "Aksi",
              field: "",
              headerHozAlign: "center",
              hozAlign: "center",
              formatter: function (cell, formatterParams, onRendered) {
                return `
                <a class="badge rounded-circle p-2 badge-light text-dark" href="#">
                  <i class="fa fa-search" style="cursor: pointer;"></i>
                </a>`;
              },
              cellClick: function (e, cell) {
                var rowData = cell.getRow().getData();
                showDetailModal(
                  rowData.nik,
                  rowData.date,
                  rowData.cust_id,
                  rowData.cust_name
                );
              },
            }
          );

          // Panggil API untuk mendapatkan data tabel
          $.ajax({
            type: "POST",
            url: url + "/rencana/monitoring/getdata",
            data: {
              tanggal_1: tgl_1,
              tanggal_2: tgl_2,
              sales_marketing: sales_marketing,
            },
            dataType: "json",
            success: function (data) {
              table = new Tabulator("#tabel_monitoring_rencana_kunjungan", {
                // Gunakan var table
                data: data,
                height: "350px",
                pagination: "local",
                paginationSize: 25,
                paginationSizeSelector: [10, 25, 50],
                layout: "fitColumns",
                columns: columns,
              });
            },
          });
        },
      });
    }

    // Fungsi untuk menampilkan modal dengan data detail
    function showDetailModal(nik, date, cust_id, cust_name) {
      // Set kode_pelanggan dan nama_pelanggan di header modal
      $("#kode_pelanggan").text(cust_id);
      $("#nama_pelanggan").text(cust_name);

      // Ambil group_id dari endpoint /pipeline/groupuser
      $.ajax({
        type: "GET",
        url: url + "/pipeline/groupuser",
        dataType: "json",
        success: function (response) {
          let group_id = response.group_id;

          // Definisikan kolom dasar untuk Tabulator
          let columns = [
            {
              title: "Grup Produk",
              field: "group_id",
              headerHozAlign: "center",
              formatter: function (cell, formatterParams, onRendered) {
                var rowData = cell.getRow().getData();
                return rowData.group_id + " - " + rowData.group_name;
              },
            },
            {
              title: "SubGrup Produk",
              field: "subgroup_id",
              headerHozAlign: "center",
              formatter: function (cell, formatterParams, onRendered) {
                var rowData = cell.getRow().getData();
                return rowData.subgroup_id + " - " + rowData.subgroup_name;
              },
            },
            {
              title: "Kelas Produk",
              field: "class_id",
              headerHozAlign: "center",
              formatter: function (cell, formatterParams, onRendered) {
                var rowData = cell.getRow().getData();
                return rowData.class_id + " - " + rowData.class_name;
              },
            },
          ];

          // Tambahkan kolom User Pelanggan jika group_id adalah '02' atau '05'
          if (group_id === "02" || group_id === "05") {
            columns.push({
              title: "User Pelanggan",
              field: "cust_user_name",
              headerHozAlign: "center",
            });
          }

          // Ambil data detail
          $.ajax({
            type: "POST",
            url: url + "rencana/monitoring/getdetdata",
            data: {
              sales_marketing: nik,
              tanggal: date,
              cust_id: cust_id,
            },
            dataType: "json",
            success: function (data) {
              // Sembunyikan tabel statis dan kosongkan tbody untuk keamanan
              $("#detailTable").hide();
              $("#detailTable tbody").empty();

              if (data.length > 0) {
                // Inisialisasi tabel Tabulator
                var detailTable = new Tabulator(
                  "#tabel_det_monitoring_rencana_kunjungan",
                  {
                    data: data,
                    height: "250px",
                    pagination: "local",
                    paginationSize: 10,
                    paginationSizeSelector: [5, 10, 20],
                    layout: "fitColumns",
                    columns: columns,
                  }
                );
              } else {
                // Tampilkan tabel statis dengan pesan "Tidak ada data"
                $("#detailTable").show();
                // Sesuaikan colspan berdasarkan jumlah kolom
                let colspan = group_id === "02" || group_id === "05" ? 5 : 4;
                $("#detailTable tbody").append(
                  `<tr><td colspan='${colspan}'>Tidak ada data</td></tr>`
                );
              }

              $("#detailModalMonitoring").modal("show");
            },
            error: function (xhr, status, error) {
              console.error("Error:", error);
              alert("Terjadi kesalahan saat mengambil data detail.");
            },
          });
        },
        error: function (xhr, status, error) {
          console.error("Error:", error);
          alert("Terjadi kesalahan saat mengambil group_id.");
        },
      });
    }
  } else if (window.location.pathname == "/rencana/buka_verifikasi") {
    // Variabel global untuk tabel
    let table;

    // Mendapatkan nilai awal
    var tanggal_acc =
      $("#tanggalDispenKunjungan").val() ||
      new Date().toISOString().split("T")[0]; // Default ke tanggal hari ini jika kosong
    var sales_marketing = $("#salesMarketing").val();

    // Set tanggal hari ini sebagai default dan batas minimum
    var today = new Date().toISOString().split("T")[0]; // Format YYYY-MM-DD
    $("#tanggalDispenKunjungan").attr("min", today); // Nonaktifkan tanggal sebelum hari ini
    $("#tanggalDispenKunjungan").val(today); // Set tanggal default ke hari ini

    // Data kunjungan yang akan diverifikasi (dilepas dari komentar untuk memuat tabel awal)
    tabel_buka_verifikasi_rencana_kunjungan(tanggal_acc, sales_marketing);

    // Fetch cabang berdasarkan session branch_id
    if (branchId !== "11") {
      // Jika branch_id bukan '11', tampilkan hanya cabang dari session dan disable dropdown
      $("#cabangBukaVerifikasi")
        .empty()
        .append(
          `<option value="${branchId}" selected>${branchId} - ${branchName}</option>`
        )
        .prop("disabled", true); // Nonaktifkan dropdown
    } else {
      // Jika branch_id adalah '11', ambil semua cabang dari API
      $.getJSON(url + "master/cabang", (branches) => {
        $("#cabangBukaVerifikasi")
          .empty()
          .append('<option value="">Pilih Cabang</option>')
          .append(
            branches.map(
              (b) => `<option value="${b.branch_id}">${b.branch_name}</option>`
            )
          );
      });
    }

    $("#cabangBukaVerifikasi").on("change", function () {
      const branchId = $(this).val();
      console.log("Cabang terpilih: ", branchId);
      $("#salesMarketing")
        .empty()
        .append('<option value="" selected>Pilih Sales/Marketing</option>');

      if (branchId) {
        $.ajax({
          url: url + "master/salesmarketing",
          method: "POST",
          data: { branch_id: branchId },
          dataType: "json",
          success: function (data) {
            data.forEach((sales) => {
              $("#salesMarketing").append(
                `<option value="${sales.nik}">${sales.name}</option>`
              );
            });
          },
        });
      }
    });

    // PERUBAHAN FILTER DROPDOWN
    // Tanggal
    $("#tanggalDispenKunjungan").change(function () {
      var tanggal_acc = $(this).val();
      var sales_marketing = $("#salesMarketing").val();

      console.log("Tanggal dipilih: " + tanggal_acc); // Debugging
      tabel_buka_verifikasi_rencana_kunjungan(tanggal_acc, sales_marketing);
    });

    // Tim Sales Marketing
    $("#salesMarketing").change(function () {
      var tanggal_acc = $("#tanggalDispenKunjungan").val(); // Perbaiki ID
      var sales_marketing = $(this).val();

      console.log(
        "tanggal = " + tanggal_acc + " sales/marketing = " + sales_marketing
      );
      tabel_buka_verifikasi_rencana_kunjungan(tanggal_acc, sales_marketing);
    });

    // Fungsi tabel_buka_verifikasi_rencana_kunjungan
    function tabel_buka_verifikasi_rencana_kunjungan(
      tanggal_acc,
      sales_marketing
    ) {
      if (table) {
        table.destroy();
      }

      $.ajax({
        type: "GET",
        url: url + "/pipeline/groupuser",
        dataType: "json",
        success: function (response) {
          let group_id = response.group_id;
          let columns = [
            { title: "Sales/Marketing", field: "nik", visible: false },
            {
              title: "Tgl Kunjungan",
              field: "date",
              headerHozAlign: "center",
              hozAlign: "center",
              formatter: "datetime",
              formatterParams: {
                inputFormat: "yyyy-MM-dd",
                outputFormat: "dd-MMM-yyyy",
              },
            },
            {
              title: "Pelanggan",
              field: "cust_id",
              headerHozAlign: "center",
              formatter: function (cell, formatterParams, onRendered) {
                var rowData = cell.getRow().getData();
                return rowData.cust_id + " - " + rowData.cust_name;
              },
            },
            {
              title: "Deskripsi",
              field: "description",
              headerHozAlign: "center",
            },
          ];

          if (group_id === "09" || group_id === "10") {
            columns.push({
              title: "User Pelanggan",
              field: "cust_user_name",
              headerHozAlign: "center",
            });
          }

          columns.push(
            {
              title: "Aksi",
              field: "",
              headerHozAlign: "center",
              hozAlign: "center",
              formatter: function (cell, formatterParams, onRendered) {
                return `
                <a class="badge rounded-circle p-2 badge-light text-dark" href="#">
                  <i class="fa fa-search" style="cursor: pointer;"></i>
                </a>`;
              },
              cellClick: function (e, cell) {
                var rowData = cell.getRow().getData();
                showDetailModal(
                  rowData.nik,
                  rowData.date,
                  rowData.cust_id,
                  rowData.cust_name
                );
              },
            },
            // Dalam definisi kolom "Setujui"
            {
              title: "Setujui",
              field: "status",
              hozAlign: "center",
              headerHozAlign: "center",
              titleFormatter: function (cell) {
                var checkbox = document.createElement("input");
                checkbox.type = "checkbox";
                checkbox.id = "header-checkbox";

                checkbox.addEventListener("click", function () {
                  let isChecked = this.checked;
                  table.getRows().forEach((row) => {
                    let rowCheckbox = row
                      .getElement()
                      .querySelector(".row-checkbox");
                    if (rowCheckbox) {
                      rowCheckbox.checked = isChecked;
                    }
                  });
                });

                return checkbox;
              },
              formatter: function (cell) {
                let isApproved = cell.getValue();
                return `
                  <input type="checkbox" class="row-checkbox" ${
                    isApproved ? "checked" : ""
                  } style="margin-right: 10px;">
                `;
              },
              cellClick: function (e, cell) {
                let row = cell.getRow(); // Dapatkan objek baris Tabulator
                let rowData = row.getData();
                let target = e.target;
              },
            }
          );

          $.ajax({
            type: "POST",
            url: url + "rencana/buka_verifikasi/getdata",
            data: {
              tanggal: tanggal_acc,
              sales_marketing: sales_marketing,
            },
            dataType: "json",
            success: function (data) {
              table = new Tabulator(
                "#tabel_buka_verifikasi_rencana_kunjungan",
                {
                  data: data,
                  height: "350px",
                  pagination: "local",
                  paginationSize: 25,
                  paginationSizeSelector: [10, 25, 50],
                  layout: "fitColumns",
                  columns: columns,
                }
              );

              // Event untuk tombol Simpan Data (Approve All)
              $("#saveApproveAll").on("click", function () {
                let rowsToApprove = table
                  .getRows()
                  .filter(
                    (row) =>
                      row.getElement().querySelector(".row-checkbox").checked
                  );

                if (rowsToApprove.length === 0) {
                  Swal.fire({
                    title: "Tidak ada data",
                    text: "Tidak ada data yang dipilih untuk disetujui.",
                    icon: "info",
                    confirmButtonText: "OK",
                  });
                  return;
                }

                Swal.fire({
                  title: "Apakah Anda yakin?",
                  text: "Data yang dipilih akan disetujui.",
                  icon: "warning",
                  showCancelButton: true,
                  confirmButtonText: "Ya, setujui!",
                  cancelButtonText: "Batal",
                }).then((result) => {
                  if (result.isConfirmed) {
                    // Kumpulkan kombinasi unik dari cust_id, nik, date
                    let uniqueCombinations = {};
                    rowsToApprove.forEach((row) => {
                      let rowData = row.getData();
                      let key = `${rowData.cust_id}_${rowData.nik}_${rowData.date}`;
                      if (!uniqueCombinations[key]) {
                        uniqueCombinations[key] = {
                          cust_id: rowData.cust_id,
                          nik: rowData.nik,
                          date: rowData.date,
                        };
                      }
                    });

                    let combinations = Object.values(uniqueCombinations);

                    // Kirim kombinasi ke server untuk diupdate
                    $.ajax({
                      type: "POST",
                      url: url + "/rencana/buka_verifikasi/updateall",
                      data: {
                        combinations: JSON.stringify(combinations),
                        status: 4,
                      },
                      dataType: "json",
                      success: function (response) {
                        if (response.status === "success") {
                          Swal.fire({
                            title: "Berhasil!",
                            text: "Data yang dipilih telah disetujui.",
                            icon: "success",
                            confirmButtonText: "OK",
                          }).then(() => {
                            window.location.href = "/rencana/buka_verifikasi";
                          });
                        } else {
                          Swal.fire({
                            title: "Gagal!",
                            text: "Terjadi kesalahan saat menyimpan data.",
                            icon: "error",
                            confirmButtonText: "OK",
                          });
                        }
                      },
                      error: function (xhr) {
                        console.error("Error:", xhr.responseText);
                        Swal.fire({
                          title: "Gagal!",
                          text: "Terjadi kesalahan saat menyimpan data.",
                          icon: "error",
                          confirmButtonText: "OK",
                        });
                      },
                    });
                  }
                });
              });
            },
          });
        },
      });
    }

    // Fungsi untuk menampilkan modal dengan data detail
    function showDetailModal(nik, date, cust_id, cust_name) {
      // Set kode_pelanggan dan nama_pelanggan di header modal
      $("#kode_pelanggan").text(cust_id);
      $("#nama_pelanggan").text(cust_name);

      // Ambil group_id dari endpoint /pipeline/groupuser
      $.ajax({
        type: "GET",
        url: url + "/pipeline/groupuser",
        dataType: "json",
        success: function (response) {
          let group_id = response.group_id;

          // Definisikan kolom dasar untuk Tabulator
          let columns = [
            {
              title: "Grup Produk",
              field: "group_id",
              headerHozAlign: "center",
              formatter: function (cell, formatterParams, onRendered) {
                var rowData = cell.getRow().getData();
                return rowData.group_id + " - " + rowData.group_name;
              },
            },
            {
              title: "SubGrup Produk",
              field: "subgroup_id",
              headerHozAlign: "center",
              formatter: function (cell, formatterParams, onRendered) {
                var rowData = cell.getRow().getData();
                return rowData.subgroup_id + " - " + rowData.subgroup_name;
              },
            },
            {
              title: "Kelas Produk",
              field: "class_id",
              headerHozAlign: "center",
              formatter: function (cell, formatterParams, onRendered) {
                var rowData = cell.getRow().getData();
                return rowData.class_id + " - " + rowData.class_name;
              },
            },
          ];

          // Tambahkan kolom User Pelanggan jika group_id adalah '02' atau '05'
          if (group_id === "02" || group_id === "05") {
            columns.push({
              title: "User Pelanggan",
              field: "cust_user_name",
              headerHozAlign: "center",
            });
          }

          // Ambil data detail
          $.ajax({
            type: "POST",
            url: url + "rencana/buka_verifikasi/getdetdata",
            data: {
              sales_marketing: nik,
              tanggal: date,
              cust_id: cust_id,
            },
            dataType: "json",
            success: function (data) {
              // Sembunyikan tabel statis dan kosongkan tbody untuk keamanan
              $("#detailTable").hide();
              $("#detailTable tbody").empty();

              if (data.length > 0) {
                // Inisialisasi tabel Tabulator
                var detailTable = new Tabulator(
                  "#tabel_det_verifikasi_rencana_kunjungan",
                  {
                    data: data,
                    height: "250px",
                    pagination: "local",
                    paginationSize: 10,
                    paginationSizeSelector: [5, 10, 20],
                    layout: "fitColumns",
                    columns: columns,
                  }
                );
              } else {
                // Tampilkan tabel statis dengan pesan "Tidak ada data"
                $("#detailTable").show();
                // Sesuaikan colspan berdasarkan jumlah kolom
                let colspan = group_id === "02" || group_id === "05" ? 5 : 4;
                $("#detailTable tbody").append(
                  `<tr><td colspan='${colspan}'>Tidak ada data</td></tr>`
                );
              }

              $("#detailModal").modal("show");
            },
            error: function (xhr, status, error) {
              console.error("Error:", error);
              alert("Terjadi kesalahan saat mengambil data detail.");
            },
          });
        },
        error: function (xhr, status, error) {
          console.error("Error:", error);
          alert("Terjadi kesalahan saat mengambil group_id.");
        },
      });
    }

    // Fungsi AJAX untuk mengupdate verifikasi ke server
    function updateVerifRencana(cust_id, nik, date, status) {
      return new Promise((resolve, reject) => {
        $.ajax({
          type: "POST",
          url: url + "/rencana/buka_verifikasi/update",
          data: {
            cust_id: cust_id,
            nik: nik,
            date: date,
            status: status,
          },
          dataType: "json",
          success: function (response) {
            if (response.status === "success") {
              let message = status
                ? `Rencana kunjungan untuk cust_id: ${cust_id}, nik: ${nik}, date: ${date} disetujui`
                : `Rencana kunjungan untuk cust_id: ${cust_id}, nik: ${nik}, date: ${date} ditolak dengan alasan: ${reason}`;
              toastr.success(message);
              resolve();
            } else {
              toastr.error("Gagal memperbarui data.");
              reject();
            }
          },
          error: function (xhr) {
            toastr.error("Terjadi kesalahan saat memperbarui data.");
            console.error("Error:", xhr.responseText);
            reject();
          },
        });
      });
    }
  }
});
