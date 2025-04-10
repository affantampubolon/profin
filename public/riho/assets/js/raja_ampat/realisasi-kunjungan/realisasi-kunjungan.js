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
  }
});
