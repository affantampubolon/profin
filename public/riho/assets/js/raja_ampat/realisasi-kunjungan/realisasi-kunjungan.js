$(document).ready(function () {
  if (window.location.pathname == "/realisasi/verifikasi") {
    // Variabel global untuk tabel
    let table;

    // Mendapatkan nilai awal
    var tanggal_acc =
      $("#tanggalAccKunjungan").val() || new Date().toISOString().split("T")[0]; // Default ke tanggal hari ini jika kosong
    var sales_marketing = $("#salesMarketing").val();
    var grp_prod_acc = $("#grupBarang").val();
    var subgrp_prod_acc = $("#subgrupBarang").val();
    var kls_prod_acc = $("#kelasBarang").val();

    // Data kunjungan yang akan diverifikasi (dilepas dari komentar untuk memuat tabel awal)
    tabel_verifikasi_realisasi_kunjungan(
      tanggal_acc,
      sales_marketing,
      grp_prod_acc,
      subgrp_prod_acc,
      kls_prod_acc
    );

    // Fetch cabang
    $.ajax({
      url: url + "master/cabang",
      method: "GET",
      dataType: "json",
      success: function (data) {
        $("#cabangRealisasiOps")
          .empty()
          .append('<option value="" selected>Pilih Cabang</option>');
        data.forEach((cabang) => {
          $("#cabangRealisasiOps").append(
            `<option value="${cabang.branch_id}">${cabang.branch_name}</option>`
          );
        });
      },
      error: function () {
        alert("Gagal memuat data cabang");
      },
    });

    $("#cabangRealisasiOps").on("change", function () {
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
      var grp_prod_acc = $("#grupBarang").val();
      var subgrp_prod_acc = $("#subgrupBarang").val();
      var kls_prod_acc = $("#kelasBarang").val();

      console.log("Tanggal dipilih: " + tanggal_acc); // Debugging
      tabel_verifikasi_realisasi_kunjungan(
        tanggal_acc,
        sales_marketing,
        grp_prod_acc,
        subgrp_prod_acc,
        kls_prod_acc
      );
    });

    //salesMarketing
    $("#salesMarketing").on("change", function () {
      var tanggal_acc = $("#tanggalAccKunjungan").val();
      var sales_marketing = $(this).val();
      var grp_prod_acc = $("#grupBarang").val();
      var subgrp_prod_acc = $("#subgrupBarang").val();
      var kls_prod_acc = $("#kelasBarang").val();

      console.log(
        "Tanggal dipilih: " +
          tanggal_acc +
          " sales/marketing = " +
          sales_marketing
      ); // Debugging
      tabel_verifikasi_realisasi_kunjungan(
        tanggal_acc,
        sales_marketing,
        grp_prod_acc,
        subgrp_prod_acc,
        kls_prod_acc
      );
    });

    // Grup Barang
    $("#grupBarang").change(function () {
      var tanggal_acc = $("#tanggalAccKunjungan").val(); // Perbaiki ID
      var sales_marketing = $("#salesMarketing").val();
      var grp_prod_acc = $(this).val();

      $.ajax({
        url: url + "master/filter/subgrup",
        method: "POST",
        data: {
          group_prod: grp_prod_acc,
        },
        success: function (data) {
          $("#subgrupBarang").html(data);
          var subgrp_prod_acc = $("#subgrupBarang").val();

          $("#kelasBarang").select2({
            placeholder: "Semua Kelas Grup",
            allowClear: true,
            closeOnSelect: false,
          });
          $("#kelasBarang").empty();

          var kls_prod_acc = $("#kelasBarang").val();

          console.log(
            "Tanggal dipilih: " +
              tanggal_acc +
              " sales/marketing = " +
              sales_marketing +
              "grup barang = " +
              grp_prod_acc
          );
          tabel_verifikasi_realisasi_kunjungan(
            tanggal_acc,
            sales_marketing,
            grp_prod_acc,
            subgrp_prod_acc,
            kls_prod_acc
          );
        },
        error: function (xhr, status, error) {
          console.error("Error fetching subgroup data:", error);
          alert("Gagal memuat data subgroup.");
        },
      });
    });

    // SubGrup Barang
    $("#subgrupBarang").change(function () {
      var tanggal_acc = $("#tanggalAccKunjungan").val(); // Perbaiki ID
      var sales_marketing = $("#cabangRealisasiOps").val();
      var grp_prod_acc = $("#grupBarang").val();
      var subgrp_prod_acc = $(this).val();

      $.ajax({
        url: url + "master/filter/kelas",
        method: "POST",
        data: {
          group_prod: grp_prod_acc,
          subgroup_prod: subgrp_prod_acc,
        },
        success: function (data) {
          $("#kelasBarang").html(data);
          var kls_prod_acc = $("#kelasBarang").val();

          console.log(
            "Tanggal dipilih: " +
              tanggal_acc +
              " sales/marketing = " +
              sales_marketing +
              " grup barang = " +
              grp_prod_acc +
              " subgrup barang = " +
              subgrp_prod_acc
          );
          tabel_verifikasi_realisasi_kunjungan(
            tanggal_acc,
            sales_marketing,
            grp_prod_acc,
            subgrp_prod_acc,
            kls_prod_acc
          );
        },
        error: function (xhr, status, error) {
          console.error("Error fetching class data:", error);
          alert("Gagal memuat data kelas.");
        },
      });
    });

    // Kelas Barang
    $("#kelasBarang").change(function () {
      var tanggal_acc = $("#tanggalAccKunjungan").val(); // Perbaiki ID
      var sales_marketing = $("#salesMarketing").val(); // Tambah variabel yang hilang
      var grp_prod_acc = $("#grupBarang").val();
      var subgrp_prod_acc = $("#subgrupBarang").val();
      var kls_prod_acc = $(this).val();

      console.log(
        "grup barang = " +
          grp_prod_acc +
          " subgrup barang = " +
          subgrp_prod_acc +
          " kelas barang = " +
          kls_prod_acc
      );
      tabel_verifikasi_realisasi_kunjungan(
        tanggal_acc,
        sales_marketing,
        grp_prod_acc,
        subgrp_prod_acc,
        kls_prod_acc
      );
    });

    // Fungsi tabel_verifikasi_realisasi_kunjungan
    function tabel_verifikasi_realisasi_kunjungan(
      tanggal_acc,
      sales_marketing,
      grp_prod_acc,
      subgrp_prod_acc,
      kls_prod_acc
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
            { title: "Tanggal", field: "date", visible: false },
            { title: "Sales/Marketing", field: "nik", visible: false },
            {
              title: "Status",
              field: "status",
              headerHozAlign: "center",
              hozAlign: "center",
              formatter: function (cell, formatterParams) {
                var value = cell.getValue();
                console.log("Status value:", value); // Debugging
                if (value === "1") {
                  return "<i class='fa fa-circle' style='color:#578FCA'></i>";
                } else if (value === "2") {
                  return "<i class='fa fa-circle' style='color:#FF5677'></i>";
                }
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
              title: "Total Nilai (Rp)",
              field: "tot_value",
              headerHozAlign: "center",
              hozAlign: "center",
              formatter: "money",
              formatterParams: {
                decimal: ",",
                thousand: ".",
              },
            },
          ];

          if (group_id === "09" || group_id === "10") {
            columns.push({
              title: "Probabilitas",
              field: "probability",
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
              headerHozAlign: "center",
              hozAlign: "center",
              formatter: function (cell, formatterParams, onRendered) {
                var rowData = cell.getRow().getData();
                return `
              <a class="badge rounded-circle p-2 badge-light text-dark detail-btn" href="#">
                <i class="fa fa-search" style="cursor: pointer;"></i>
              </a>
              <a class="badge rounded-circle p-2 badge-primary map-btn" href="#"
                data-lat="${rowData.latitude}"
                data-lng="${rowData.longitude}"
                data-cust-id="${rowData.cust_id}"
                data-cust-name="${rowData.cust_name}">
                <i class="fa fa-location-arrow"></i>
              </a>
              <a class="badge rounded-circle p-2 badge-success feedback-btn" href="#">
                <i class="fa fa-edit" style="cursor: pointer;"></i>
              </a>`;
              },
              cellClick: function (e, cell) {
                var target = e.target.closest("a");
                if (!target) return;

                var row = cell.getRow();
                var rowData = row.getData();

                if (target.classList.contains("detail-btn")) {
                  // Panggil fungsi untuk menampilkan detail
                  showDetailModal(
                    rowData.nik,
                    rowData.date,
                    rowData.cust_id,
                    rowData.cust_name
                  );
                } else if (target.classList.contains("map-btn")) {
                  var lat = target.getAttribute("data-lat");
                  var lng = target.getAttribute("data-lng");
                  var custId = target.getAttribute("data-cust-id");
                  var custName = target.getAttribute("data-cust-name");
                  showMapModal(lat, lng, custId, custName);
                } else if (target.classList.contains("feedback-btn")) {
                  $("#feedbackModal").modal("show");
                  $("#feedbackInput").val("");
                  $("#charCount").text("0/250");
                  $("#feedbackModal").data("row", row);
                }
              },
            }
          );

          $.ajax({
            type: "POST",
            url: url + "/realisasi/verifikasi/getdata",
            data: {
              tanggal: tanggal_acc,
              sales_marketing: sales_marketing,
              grp_prod: grp_prod_acc,
              subgrp_prod: subgrp_prod_acc,
              klsgrp_prod: kls_prod_acc,
            },
            dataType: "json",
            success: function (data) {
              table = new Tabulator("#tabel_verifikasi_realisasi_kunjungan", {
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

    let map; // Variabel global untuk menyimpan instance peta

    // Fungsi untuk menampilkan modal dengan data detail
    function showDetailModal(nik, date, cust_id, cust_name) {
      // Log parameter untuk debugging
      console.log("showDetailModal called with:", {
        nik: nik,
        date: date,
        cust_id: cust_id,
        cust_name: cust_name,
      });

      // Periksa apakah elemen kode_pelanggan dan nama_pelanggan ada
      if ($("#kode_pelanggan").length === 0) {
        console.error("Elemen #kode_pelanggan tidak ditemukan di DOM");
      }
      if ($("#nama_pelanggan").length === 0) {
        console.error("Elemen #nama_pelanggan tidak ditemukan di DOM");
      }

      // Set kode_pelanggan dan nama_pelanggan di header modal
      $("#kode_pelanggan").text(cust_id || "N/A"); // Gunakan fallback jika cust_id kosong
      $("#nama_pelanggan").text(cust_name || "N/A");

      // Log nilai yang diset untuk memastikan
      console.log("Set kode_pelanggan:", $("#kode_pelanggan").text());
      console.log("Set nama_pelanggan:", $("#nama_pelanggan").text());

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
            columns.push(
              {
                title: "User Pelanggan",
                field: "cust_user_name",
                headerHozAlign: "center",
              },
              {
                title: "Probabilitas %",
                field: "probability",
                headerHozAlign: "center",
                hozAlign: "center",
              }
            );
          }

          columns.push({
            title: "Nilai (Rp)",
            field: "value",
            headerHozAlign: "center",
            hozAlign: "center",
            formatter: "money",
            formatterParams: {
              decimal: ",",
              thousand: ".",
            },
          });

          // Ambil data detail
          $.ajax({
            type: "POST",
            url: url + "realisasi/verifikasi/getdetdata",
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
                  "#tabel_det_verifikasi_realisasi_kunjungan",
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

    function showMapModal(lat, lng, custId, custName) {
      // Isi data pelanggan di modal
      $("#kode_pelanggan").text(custId);
      $("#nama_pelanggan").text(custName);

      // Tampilkan modal
      $("#mapModal").modal("show");

      // Inisialisasi peta setelah modal ditampilkan
      $("#mapModal").on("shown.bs.modal", function () {
        // Hapus peta lama jika ada
        if (map) {
          map.remove();
        }

        // Inisialisasi peta baru dengan koordinat
        map = L.map("map").setView([lat, lng], 13);

        // Tambahkan tile layer dari OpenStreetMap
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
          attribution:
            '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        }).addTo(map);

        // Tambahkan marker pada lokasi
        L.marker([lat, lng]).addTo(map);
      });
    }

    // Event listener untuk tombol "Simpan Data"
    $("#feedback_spv").on("click", function () {
      // Tampilkan modal feedback
      $("#feedbackModal").modal("show");
    });

    // Hitung karakter saat mengetik di textarea
    $("#feedbackInput").on("input", function () {
      var textLength = $(this).val().length;
      $("#charCount").text(textLength + "/250");
    });

    // Event listener untuk tombol "Simpan" di modal feedback
    $("#saveFeedback").on("click", function () {
      var feedback = $("#feedbackInput").val();
      if (feedback.length < 25 || feedback.length > 250) {
        alert("Feedback harus antara 25 dan 250 karakter.");
        return;
      }

      var row = $("#feedbackModal").data("row");
      var rowData = row.getData();

      Swal.fire({
        title: "Apakah Anda yakin menyimpan data?",
        text: "Data akan disimpan dengan feedback",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, simpan!",
        cancelButtonText: "Batal",
      }).then((result) => {
        if (result.isConfirmed) {
          // Kirim request AJAX ke controller untuk update
          $.ajax({
            type: "POST",
            url: url + "/realisasi/verifikasi/update",
            data: {
              date: rowData.date,
              nik: rowData.nik,
              cust_id: rowData.cust_id,
              flg_verify: "t",
              feedback: feedback,
            },
            dataType: "json",
            success: function (response) {
              if (response.status === "success") {
                Swal.fire({
                  title: "Berhasil!",
                  text: "Data telah disimpan.",
                  icon: "success",
                  confirmButtonText: "OK",
                }).then(() => {
                  row.delete(); // Hapus baris dari tabel
                  $("#feedbackModal").modal("hide");
                });
              } else {
                Swal.fire({
                  title: "Gagal!",
                  text: response.message,
                  icon: "error",
                  confirmButtonText: "OK",
                });
              }
            },
            error: function (xhr, status, error) {
              console.error("AJAX Error:", status, error);
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
  } else if (window.location.pathname === "/realisasi/monitoring") {
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
      var grp_prod_mon = $("#grupBarang").val();
      var subgrp_prod_mon = $("#subgrupBarang").val();
      var kls_prod_mon = $("#kelasBarang").val();
      return {
        tgl_1,
        tgl_2,
        sales_marketing,
        grp_prod_mon,
        subgrp_prod_mon,
        kls_prod_mon,
      };
    }

    // Panggil fungsi awal untuk menampilkan data
    var initialFilters = getFilterValues();
    tabel_monitoring_realisasi_kunjungan(
      initialFilters.tgl_1,
      initialFilters.tgl_2,
      initialFilters.sales_marketing,
      initialFilters.grp_prod_mon,
      initialFilters.subgrp_prod_mon,
      initialFilters.kls_prod_mon
    );

    // Fetch cabang
    $.ajax({
      url: url + "master/cabang",
      method: "GET",
      dataType: "json",
      success: function (data) {
        $("#cabangRencanaOps")
          .empty()
          .append('<option value="" selected>Pilih Cabang</option>');
        data.forEach((cabang) => {
          $("#cabangRencanaOps").append(
            `<option value="${cabang.branch_id}">${cabang.branch_name}</option>`
          );
        });
      },
      error: function () {
        alert("Gagal memuat data cabang");
      },
    });

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
      tabel_monitoring_realisasi_kunjungan(
        filters.tgl_1,
        filters.tgl_2,
        filters.sales_marketing,
        filters.grp_prod_mon,
        filters.subgrp_prod_mon,
        filters.kls_prod_mon
      );
    });

    // Event handler untuk Sales/Marketing
    $("#salesMarketing").change(function () {
      var filters = getFilterValues();
      tabel_monitoring_realisasi_kunjungan(
        filters.tgl_1,
        filters.tgl_2,
        filters.sales_marketing,
        filters.grp_prod_mon,
        filters.subgrp_prod_mon,
        filters.kls_prod_mon
      );
    });

    // Event handler untuk Grup Barang
    $("#grupBarang").change(function () {
      var filters = getFilterValues();
      $.ajax({
        url: url + "master/filter/subgrup",
        method: "POST",
        data: { group_prod: filters.grp_prod_mon },
        success: function (data) {
          $("#subgrupBarang").html(data);
          $("#kelasBarang")
            .select2({
              placeholder: "Semua Kelas Grup",
              allowClear: true,
              closeOnSelect: false,
            })
            .empty();

          var updatedFilters = getFilterValues();
          tabel_monitoring_realisasi_kunjungan(
            updatedFilters.tgl_1,
            updatedFilters.tgl_2,
            updatedFilters.sales_marketing,
            updatedFilters.grp_prod_mon,
            updatedFilters.subgrp_prod_mon,
            updatedFilters.kls_prod_mon
          );
        },
        error: function (xhr, status, error) {
          console.error("Error fetching subgroup data:", error);
          alert("Gagal memuat data subgroup.");
        },
      });
    });

    // Event handler untuk SubGrup Barang
    $("#subgrupBarang").change(function () {
      var filters = getFilterValues();
      $.ajax({
        url: url + "master/filter/kelas",
        method: "POST",
        data: {
          group_prod: filters.grp_prod_mon,
          subgroup_prod: filters.subgrp_prod_mon,
        },
        success: function (data) {
          $("#kelasBarang").html(data);
          var updatedFilters = getFilterValues();
          tabel_monitoring_realisasi_kunjungan(
            updatedFilters.tgl_1,
            updatedFilters.tgl_2,
            updatedFilters.sales_marketing,
            updatedFilters.grp_prod_mon,
            updatedFilters.subgrp_prod_mon,
            updatedFilters.kls_prod_mon
          );
        },
        error: function (xhr, status, error) {
          console.error("Error fetching class data:", error);
          alert("Gagal memuat data kelas.");
        },
      });
    });

    // Event handler untuk Kelas Barang
    $("#kelasBarang").change(function () {
      var filters = getFilterValues();
      tabel_monitoring_realisasi_kunjungan(
        filters.tgl_1,
        filters.tgl_2,
        filters.sales_marketing,
        filters.grp_prod_mon,
        filters.subgrp_prod_mon,
        filters.kls_prod_mon
      );
    });

    // Inisialisasi tabel Realisasi Kunjungan
    function tabel_monitoring_realisasi_kunjungan(
      tgl_1,
      tgl_2,
      sales_marketing,
      grp_prod_mon,
      subgrp_prod_mon,
      kls_prod_mon
    ) {
      var table; // Deklarasikan di luar AJAX agar bisa diakses oleh #selectAll

      $.ajax({
        type: "GET",
        url: url + "/pipeline/groupuser", // Ambil group_id dari server
        dataType: "json",
        success: function (response) {
          let group_id = response.group_id;
          let columns = [
            {
              title: "Tanggal Realisasi",
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
              title: "Status",
              field: "flg_visit",
              headerHozAlign: "center",
              hozAlign: "center",
              formatter: function (cell, formatterParams) {
                var value = cell.getValue();
                console.log("Status value:", value); // Debugging
                if (value === "t") {
                  return "<i class='fa fa-circle' style='color:#578FCA'></i>";
                } else if (value === "f") {
                  return "<i class='fa fa-circle' style='color:#FF5677'></i>";
                }
              },
            },
            {
              title: "Non Route?",
              field: "flg_non_route",
              headerHozAlign: "center",
              hozAlign: "center",
              formatter: function (cell, formatterParams) {
                var value = cell.getValue();
                console.log("Status value:", value); // Debugging
                if (value === "t") {
                  return "<i class='fa fa-check' style='color:#03A791'></i>";
                } else if (value === "f") {
                  return "<i class='fa fa-times' style='color:#FF5677'></i>";
                }
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
            {
              title: "Total Nilai (Rp)",
              field: "tot_value",
              headerHozAlign: "center",
              hozAlign: "center",
              formatter: "money",
              formatterParams: {
                decimal: ",",
                thousand: ".",
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
              title: "Tinjauan Spv",
              field: "feedback",
              headerHozAlign: "center",
            },
            {
              title: "Aksi",
              headerHozAlign: "center",
              hozAlign: "center",
              formatter: function (cell, formatterParams, onRendered) {
                var rowData = cell.getRow().getData();
                return `
              <a class="badge rounded-circle p-2 badge-light text-dark detail-btn" href="#">
                <i class="fa fa-search" style="cursor: pointer;"></i>
              </a>
              <a class="badge rounded-circle p-2 badge-primary map-btn" href="#"
                data-lat="${rowData.latitude}"
                data-lng="${rowData.longitude}"
                data-cust-id="${rowData.cust_id}"
                data-cust-name="${rowData.cust_name}">
                <i class="fa fa-location-arrow"></i>
              </a>`;
              },
              cellClick: function (e, cell) {
                var target = e.target.closest("a");
                if (!target) return;

                var row = cell.getRow();
                var rowData = row.getData();

                if (target.classList.contains("detail-btn")) {
                  // Panggil fungsi untuk menampilkan detail
                  showDetailModal(
                    rowData.nik,
                    rowData.date,
                    rowData.cust_id,
                    rowData.cust_name
                  );
                } else if (target.classList.contains("map-btn")) {
                  var lat = target.getAttribute("data-lat");
                  var lng = target.getAttribute("data-lng");
                  var custId = target.getAttribute("data-cust-id");
                  var custName = target.getAttribute("data-cust-name");
                  showMapModal(lat, lng, custId, custName);
                }
              },
            }
          );

          // Panggil API untuk mendapatkan data tabel
          $.ajax({
            type: "POST",
            url: url + "realisasi/monitoring/getdata",
            data: {
              tanggal_1: tgl_1,
              tanggal_2: tgl_2,
              sales_marketing: sales_marketing,
              grp_prod: grp_prod_mon,
              subgrp_prod: subgrp_prod_mon,
              klsgrp_prod: kls_prod_mon,
            },
            dataType: "json",
            success: function (data) {
              table = new Tabulator("#tabel_monitoring_realisasi_kunjungan", {
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
      // Log parameter untuk debugging
      console.log("showDetailModal called with:", {
        nik: nik,
        date: date,
        cust_id: cust_id,
        cust_name: cust_name,
      });

      // Periksa apakah elemen kode_pelanggan dan nama_pelanggan ada
      if ($("#kode_pelanggan").length === 0) {
        console.error("Elemen #kode_pelanggan tidak ditemukan di DOM");
      }
      if ($("#nama_pelanggan").length === 0) {
        console.error("Elemen #nama_pelanggan tidak ditemukan di DOM");
      }

      // Set kode_pelanggan dan nama_pelanggan di header modal
      $("#kode_pelanggan").text(cust_id || "N/A"); // Gunakan fallback jika cust_id kosong
      $("#nama_pelanggan").text(cust_name || "N/A");

      // Log nilai yang diset untuk memastikan
      console.log("Set kode_pelanggan:", $("#kode_pelanggan").text());
      console.log("Set nama_pelanggan:", $("#nama_pelanggan").text());

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
            columns.push(
              {
                title: "User Pelanggan",
                field: "cust_user_name",
                headerHozAlign: "center",
              },
              {
                title: "Probabilitas %",
                field: "probability",
                headerHozAlign: "center",
                hozAlign: "center",
              }
            );
          }

          columns.push({
            title: "Nilai (Rp)",
            field: "value",
            headerHozAlign: "center",
            hozAlign: "center",
            formatter: "money",
            formatterParams: {
              decimal: ",",
              thousand: ".",
            },
          });

          // Ambil data detail
          $.ajax({
            type: "POST",
            url: url + "realisasi/monitoring/getdetdata",
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
                  "#tabel_det_monitoring_realisasi_kunjungan",
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

    function showMapModal(lat, lng, custId, custName) {
      // Isi data pelanggan di modal
      $("#kode_pelanggan").text(custId);
      $("#nama_pelanggan").text(custName);

      // Tampilkan modal
      $("#mapModal").modal("show");

      // Inisialisasi peta setelah modal ditampilkan
      $("#mapModal").on("shown.bs.modal", function () {
        // Hapus peta lama jika ada
        if (map) {
          map.remove();
        }

        // Inisialisasi peta baru dengan koordinat
        map = L.map("map").setView([lat, lng], 13);

        // Tambahkan tile layer dari OpenStreetMap
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
          attribution:
            '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        }).addTo(map);

        // Tambahkan marker pada lokasi
        L.marker([lat, lng]).addTo(map);
      });
    }
  }
});
