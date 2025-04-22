$(document).ready(function () {
  if (window.location.pathname == "/realisasi/verifikasi") {
    // Variabel global untuk tabel
    let table;

    // Mendapatkan nilai awal
    var tanggal_acc =
      $("#tanggalAccKunjungan").val() || new Date().toISOString().split("T")[0]; // Default ke tanggal hari ini jika kosong
    var cabang = $("#cabangRealisasiOps").val();
    var grp_prod_acc = $("#grupBarang").val();
    var subgrp_prod_acc = $("#subgrupBarang").val();
    var kls_prod_acc = $("#kelasBarang").val();

    // Data kunjungan yang akan diverifikasi (dilepas dari komentar untuk memuat tabel awal)
    tabel_verifikasi_realisasi_kunjungan(
      tanggal_acc,
      cabang,
      grp_prod_acc,
      subgrp_prod_acc,
      kls_prod_acc
    );

    // Ambil branch_id dari API
    $.ajax({
      url: url + "/realisasi/cabuser",
      method: "GET",
      dataType: "json",
      success: function (response) {
        const branchId = response.branch_id;

        // Inisialisasi Select2
        $("#cabangRealisasiOps").select2();

        // Fetch cabang hanya jika branch_id adalah '11'
        if (branchId === "11") {
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
              // Reinisialisasi Select2 setelah data diisi
              $("#cabangRealisasiOps").select2();
            },
            error: function (xhr, status, error) {
              console.error("Gagal memuat data cabang:", error);
              alert("Gagal memuat data cabang");
            },
          });
        }
      },
      error: function (xhr, status, error) {
        console.error("Gagal memuat session branch_id:", error);
      },
    });

    // PERUBAHAN FILTER DROPDOWN
    // Tanggal
    $("#tanggalAccKunjungan").change(function () {
      var tanggal_acc = $(this).val();
      var cabang = $("#cabangRealisasiOps").val();
      var grp_prod_acc = $("#grupBarang").val();
      var subgrp_prod_acc = $("#subgrupBarang").val();
      var kls_prod_acc = $("#kelasBarang").val();

      console.log("Tanggal dipilih: " + tanggal_acc); // Debugging
      tabel_verifikasi_realisasi_kunjungan(
        tanggal_acc,
        cabang,
        grp_prod_acc,
        subgrp_prod_acc,
        kls_prod_acc
      );
    });

    //cabang
    $("#cabangRealisasiOps").on("change", function () {
      var tanggal_acc = $("#tanggalAccKunjungan").val();
      var cabang = $(this).val();
      var grp_prod_acc = $("#grupBarang").val();
      var subgrp_prod_acc = $("#subgrupBarang").val();
      var kls_prod_acc = $("#kelasBarang").val();

      console.log("Tanggal dipilih: " + tanggal_acc + " cabang = " + cabang); // Debugging
      tabel_verifikasi_realisasi_kunjungan(
        tanggal_acc,
        cabang,
        grp_prod_acc,
        subgrp_prod_acc,
        kls_prod_acc
      );
    });

    // Grup Barang
    $("#grupBarang").change(function () {
      var tanggal_acc = $("#tanggalAccKunjungan").val(); // Perbaiki ID
      var cabang = $("#cabangRealisasiOps").val();
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
              " cabang = " +
              cabang +
              "grup barang = " +
              grp_prod_acc
          );
          tabel_verifikasi_realisasi_kunjungan(
            tanggal_acc,
            cabang,
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
      var cabang = $("#cabangRealisasiOps").val();
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
              " cabang = " +
              cabang +
              " grup barang = " +
              grp_prod_acc +
              " subgrup barang = " +
              subgrp_prod_acc
          );
          tabel_verifikasi_realisasi_kunjungan(
            tanggal_acc,
            cabang,
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
      var cabang = $("#cabangRealisasiOps").val(); // Tambah variabel yang hilang
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
        cabang,
        grp_prod_acc,
        subgrp_prod_acc,
        kls_prod_acc
      );
    });

    // Fungsi tabel_verifikasi_realisasi_kunjungan
    function tabel_verifikasi_realisasi_kunjungan(
      tanggal_acc,
      cabang,
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
            {
              title: "Status",
              field: "status",
              headerHozAlign: "center",
              hozAlign: "center",
              formatter: function (cell, formatterParams) {
                var value = cell.getValue();
                console.log("Status value:", value); // Debugging: Log the value to ensure correctness

                if (value === "1") {
                  return "<i class='fa fa-circle' style='color:#578FCA'></i>";
                } else if (value === "2") {
                  return "<i class='fa fa-circle' style='color:#FF5677'></i>";
                }
              },
            },
            {
              title: "Sales / Marketing",
              field: "emp_name",
              headerHozAlign: "center",
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
              title: "Kelas Barang",
              field: "class_name",
              headerHozAlign: "center",
              formatter: function (cell, formatterParams, onRendered) {
                // Ambil data dari baris
                var rowData = cell.getRow().getData();
                // Gabungkan cust_id dan cust_name
                return (
                  rowData.group_id +
                  rowData.subgroup_id +
                  rowData.class_id +
                  " - " +
                  rowData.class_name
                );
              },
            },
            {
              title: "Nilai",
              field: "value",
              headerHozAlign: "center",
            },
          ];

          if (group_id === "02" || group_id === "05") {
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
              formatter: function (cell, formatterParams, onRendered) {
                var rowData = cell.getRow().getData();
                return `<button class="btn btn-pill btn-outline btn-sm btn-primary map-btn" 
                    data-lat="${rowData.latitude}" 
                    data-lng="${rowData.longitude}" 
                    data-cust-id="${rowData.cust_id}" 
                    data-cust-name="${rowData.cust_name}">
                <i class="fa fa-location-arrow"></i>
            </button>`;
              },
              cellClick: function (e, cell) {
                var button = cell.getElement().querySelector(".map-btn");
                var lat = button.getAttribute("data-lat");
                var lng = button.getAttribute("data-lng");
                var custId = button.getAttribute("data-cust-id");
                var custName = button.getAttribute("data-cust-name");
                showMapModal(lat, lng, custId, custName);
              },
            }
          );

          $.ajax({
            type: "POST",
            url: url + "/realisasi/verifikasi/getdata",
            data: {
              tanggal: tanggal_acc,
              cabang: cabang,
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

    // Event listener untuk tombol "Simpan" di modal
    $("#saveFeedback").on("click", function () {
      var feedback = $("#feedbackInput").val();
      if (feedback.length < 50 || feedback.length > 250) {
        alert("Feedback harus antara 50 dan 250 karakter.");
        return;
      }

      // Ambil semua ID dari data tabel
      var ids = table.getData().map((row) => row.id);
      console.log("IDs to update:", ids); // Debugging

      if (!ids || ids.length === 0) {
        Swal.fire({
          icon: "error",
          title: "Gagal",
          text: "Tidak ada data untuk diperbarui.",
        });
        return;
      }

      // Kirim request AJAX ke controller
      $.ajax({
        type: "POST",
        url: url + "realisasi/verifikasi/update",
        data: {
          ids: ids,
          flg_verify: "t", // Ubah ke string 't' untuk PostgreSQL
          feedback: feedback,
        },
        dataType: "json",
        success: function (response) {
          console.log("Response:", response); // Debugging
          if (response.status === "success") {
            Swal.fire({
              icon: "success",
              title: "Sukses",
              text: response.message,
            });
            $("#feedbackModal").modal("hide");
            table.setData(); // Refresh tabel
          } else {
            Swal.fire({
              icon: "error",
              title: "Gagal",
              text: response.message,
            });
          }
        },
        error: function (xhr, status, error) {
          console.error("AJAX Error:", status, error); // Debugging
          Swal.fire({
            icon: "error",
            title: "Gagal",
            text: "Terjadi kesalahan saat menyimpan data: " + error,
          });
        },
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
              formatter: "datetime",
              formatterParams: {
                inputFormat: "yyyy-MM-dd",
                outputFormat: "dd-MMM-yyyy",
              },
            },
            {
              title: "Kelas Barang",
              field: "class_name",
              headerHozAlign: "center",
              formatter: function (cell, formatterParams, onRendered) {
                // Ambil data dari baris
                var rowData = cell.getRow().getData();
                // Gabungkan cust_id dan cust_name
                return (
                  rowData.group_id +
                  rowData.subgroup_id +
                  rowData.class_id +
                  " - " +
                  rowData.class_name
                );
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

          if (group_id === "02" || group_id === "05") {
            columns.push({
              title: "User Pelanggan",
              field: "cust_user_name",
              headerHozAlign: "center",
            });
          }
          columns.push({
            title: "Deskripsi",
            field: "description",
            headerHozAlign: "center",
          });

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
  }
});
