$(document).ready(function () {
  if (window.location.pathname == "/master/pelanggan/index") {
    // data_mst_pelanggan();

    // Fetch cabang
    $.ajax({
      url: url + "master/cabang",
      method: "GET",
      dataType: "json",
      success: function (data) {
        $("#cabangpelanggan")
          .empty()
          .append('<option value="" selected>Pilih Cabang</option>');
        data.forEach((cabang) => {
          $("#cabangpelanggan").append(
            `<option value="${cabang.branch_id}">${cabang.branch_name}</option>`
          );
        });
      },
      error: function () {
        alert("Gagal memuat data cabang");
      },
    });

    // Fungsi untuk mendapatkan semua nilai filter saat ini
    function getFilterValues() {
      var cabang = $("#cabangpelanggan").val();
      return {
        cabang,
      };
    }

    // Panggil fungsi awal untuk menampilkan data
    var initialFilters = getFilterValues();
    data_mst_pelanggan(initialFilters.cabang);

    // Event handler untuk Sales/Marketing
    $("#cabangpelanggan").change(function () {
      var filters = getFilterValues();
      data_mst_pelanggan(filters.cabang);
    });

    function data_mst_pelanggan(cabang) {
      $.ajax({
        type: "POST",
        url: url + "master/pelanggan/getdatamstpelanggan",
        async: true,
        data: {
          branch_id: cabang,
        },
        dataType: "json",

        success: function (data) {
          // Tambahkan kelas CSS ke elemen #tabel_master_pelanggan
          $("#tabel_master_pelanggan").addClass("table-bordered table-sm");
          var table = new Tabulator("#tabel_master_pelanggan", {
            data: data,
            height: "350px",
            pagination: "local",
            paginationSize: 25,
            paginationSizeSelector: [10, 25, 50],
            layout: "fitColumns",
            columns: [
              {
                title: "Kode Pelanggan",
                field: "cust_id",
                headerHozAlign: "center",
                hozAlign: "center",
              },
              {
                title: "Nama Pelanggan",
                field: "cust_name",
                headerHozAlign: "center",
                headerFilter: "input",
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
                  showDetailModal(rowData);
                },
              },
            ],
          });
        },
      });
    }

    function showDetailModal(rowData) {
      $("#kode_pelanggan").text(rowData.cust_id || "-");
      $("#nama_pelanggan").text(rowData.cust_name || "-");
      $("#pelanggan").text(
        rowData.cust_id && rowData.cust_name
          ? `${rowData.cust_id} - ${rowData.cust_name}`
          : "-"
      );
      $("#nama_pic").text(rowData.pic_name || "-");
      $("#email").text(rowData.email || "-");
      $("#no_telp").text(rowData.phone_no || "-");
      $("#alamat").text(rowData.address || "-");
      $("#npwp").text(rowData.npwp || "-");
      $("#nama_pajak").text(rowData.cust_name_tax || "-");
      $("#alamat_pajak").text(rowData.address_tax || "-");
      $("#detailPelangganModal").modal("show");
    }
  } else if (window.location.pathname == "/master/pelanggan/registrasi") {
    // Submit form
    $("form").on("submit", function (e) {
      e.preventDefault();

      // Daftar field wajib
      const requiredFields = [
        "#namaPelanggan",
        "#namaPic",
        "#notelpPelanggan",
        "#alamatPelanggan",
        "#namaPemilik",
      ];
      let isValid = true;

      if (!isValid) {
        Swal.fire({
          title: "Peringatan",
          text: "Wajib diisi semua field yang bertanda *.",
          icon: "warning",
          confirmButtonText: "OK",
        });
        return;
      }

      // Konfirmasi SweetAlert
      Swal.fire({
        title: "Konfirmasi",
        text: "Apakah Anda yakin menyimpan data?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya, simpan!",
        cancelButtonText: "Batal",
      }).then((result) => {
        if (result.isConfirmed) {
          // Membuat objek formData
          const formData = {
            cust_name: $("#namaPelanggan").val()
              ? $("#namaPelanggan").val().toUpperCase()
              : null,
            address: $("#alamatPelanggan").val()
              ? $("#alamatPelanggan").val().toUpperCase()
              : null,
            email: $("#emailPelanggan").val() || null,
            phone_no: $("#notelpPelanggan").val() || null,
            npwp: $("#npwpPelanggan").val() || null,
            cust_name_tax: $("#namanpwpPelanggan").val()
              ? $("#namanpwpPelanggan").val().toUpperCase()
              : null,
            address_tax: $("#alamatnpwpPelanggan").val()
              ? $("#alamatnpwpPelanggan").val().toUpperCase()
              : null,
            pic_name: $("#namaPic").val()
              ? $("#namaPic").val().toUpperCase()
              : null,
          };

          // Debugging: Log formData untuk memeriksa nilai
          console.log("formData:", formData);

          $.ajax({
            url: url + "/master/pelanggan/insertpelanggan",
            method: "POST",
            data: formData,
            dataType: "json",
            success: function (response) {
              if (response.success) {
                Swal.fire({
                  title: "Sukses",
                  text: "Data berhasil disimpan",
                  icon: "success",
                  confirmButtonText: "OK",
                }).then(() => {
                  window.location.reload();
                });
              } else {
                Swal.fire({
                  title: "Gagal",
                  text:
                    "Gagal menyimpan data: " +
                    (response.message || "Kesalahan tidak diketahui"),
                  icon: "error",
                  confirmButtonText: "OK",
                });
              }
            },
            error: function (xhr, status, error) {
              console.error("Error saving data:", error);
              let errorMessage = "Terjadi kesalahan saat menyimpan data.";
              if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
              }
              Swal.fire({
                title: "Error",
                text: errorMessage,
                icon: "error",
                confirmButtonText: "OK",
              });
            },
          });
        }
      });
    });
  } else if (window.location.pathname == "/master/userpelanggan/index") {
    // Pastikan roleId tersedia (disisipkan dari view)
    var roleId = window.roleId || "0"; // Fallback ke '0' jika tidak ada

    // Variabel untuk menyimpan data jabatan
    let jabatanData = [];

    // Fungsi untuk memuat data jabatan
    function loadJabatanData() {
      return new Promise((resolve, reject) => {
        $.ajax({
          url: url + "master/userpelanggan/getdatamstposuserpelanggan",
          method: "GET",
          dataType: "json",
          success: function (data) {
            console.log("Jabatan Data:", data); // Debugging
            jabatanData = data;
            resolve(data);
          },
          error: function (xhr, status, error) {
            console.error("Error fetching jabatan data:", error);
            Swal.fire({
              title: "Error",
              text: "Gagal memuat data posisi / jabatan",
              icon: "error",
              confirmButtonText: "OK",
            });
            reject(error);
          },
        });
      });
    }

    // Fetch cabang
    $.ajax({
      url: url + "master/cabang",
      method: "GET",
      dataType: "json",
      success: function (data) {
        $("#cabanguserpelanggan")
          .empty()
          .append('<option value="" selected>Pilih Cabang</option>');
        data.forEach((cabang) => {
          $("#cabanguserpelanggan").append(
            `<option value="${cabang.branch_id}">${cabang.branch_name}</option>`
          );
        });
      },
      error: function () {
        Swal.fire({
          title: "Error",
          text: "Gagal memuat data cabang",
          icon: "error",
          confirmButtonText: "OK",
        });
      },
    });

    // Inisialisasi Select2 untuk dropdown
    $(document).ready(function () {
      $("#posisiUserPelanggan").select2({
        placeholder: "Pilih Jabatan / Posisi",
        allowClear: true,
      });
      $("#statusUser").select2({
        placeholder: "Pilih Status",
        allowClear: true,
      });
    });

    // Fungsi untuk mengisi dropdown posisi
    function populatePosisiDropdown(selectedValue) {
      $("#posisiUserPelanggan")
        .empty()
        .append('<option value="" selected>Pilih Jabatan / Posisi</option>');

      if (jabatanData.length > 0) {
        jabatanData.forEach((userjabatan) => {
          $("#posisiUserPelanggan").append(
            `<option value="${userjabatan.id}">${userjabatan.name}</option>`
          );
        });
      } else {
        console.warn("Jabatan data kosong");
      }

      // Set nilai yang dipilih
      $("#posisiUserPelanggan")
        .val(selectedValue || "")
        .trigger("change");
    }

    // Muat data jabatan saat halaman dimuat
    loadJabatanData().catch((error) => {
      console.error("Failed to load jabatan data on page load:", error);
    });

    // Fungsi untuk mendapatkan semua nilai filter saat ini
    function getFilterValues() {
      var cabang = $("#cabanguserpelanggan").val();
      return {
        cabang,
      };
    }

    // Panggil fungsi awal untuk menampilkan data
    var initialFilters = getFilterValues();
    data_mst_user_pelanggan(initialFilters.cabang);

    // Event handler untuk Cabang
    $("#cabanguserpelanggan").change(function () {
      var filters = getFilterValues();
      data_mst_user_pelanggan(filters.cabang);
    });

    // Custom filter editor untuk kolom flg_used
    var statusFilterEditor = function (
      cell,
      onRendered,
      success,
      cancel,
      editorParams
    ) {
      var select = document.createElement("select");
      select.style.padding = "4px";
      select.style.width = "100%";
      select.style.boxSizing = "border-box";

      var options = [
        { value: "all", label: "Semua" },
        { value: "t", label: "Aktif" },
        { value: "f", label: "Tidak Aktif" },
      ];

      options.forEach(function (option) {
        var opt = document.createElement("option");
        opt.value = option.value;
        opt.text = option.label;
        select.appendChild(opt);
      });

      select.value = cell.getValue() || "all";

      function buildValue() {
        success(select.value);
      }

      select.addEventListener("change", buildValue);
      select.addEventListener("blur", buildValue);
      select.addEventListener("keydown", function (e) {
        if (e.keyCode == 27) {
          cancel();
        }
      });

      return select;
    };

    // Custom filter function untuk kolom flg_used
    function statusFilterFunction(
      headerValue,
      rowValue,
      rowData,
      filterParams
    ) {
      if (headerValue === "all") {
        return true;
      }
      return rowValue === headerValue;
    }

    function data_mst_user_pelanggan(cabang) {
      $.ajax({
        type: "POST",
        url: url + "master/userpelanggan/getdatamstuserpelanggan",
        async: true,
        data: {
          branch_id: cabang,
        },
        dataType: "json",
        success: function (data) {
          var columns = [
            {
              title: "Status",
              field: "flg_used",
              headerHozAlign: "center",
              hozAlign: "center",
              headerFilter: statusFilterEditor,
              headerFilterFunc: statusFilterFunction,
              headerFilterLiveFilter: false,
              formatter: function (cell, formatterParams) {
                var value = cell.getValue();
                console.log("Status:", value);

                if (value === "t") {
                  return "<i class='fa fa-check' style='color:#03A791'></i>";
                } else if (value === "f") {
                  return "<i class='fa fa-times' style='color:#FF5677'></i>";
                }
                return "";
              },
            },
            {
              title: "Pelanggan",
              field: "cust_name",
              headerHozAlign: "center",
              hozAlign: "left",
              headerFilter: "input",
              formatter: function (cell, formatterParams, onRendered) {
                var rowData = cell.getRow().getData();
                return (
                  (rowData.cust_id || "-") + " - " + (rowData.cust_name || "-")
                );
              },
            },
            {
              title: "Nama",
              field: "name",
              headerHozAlign: "center",
              hozAlign: "left",
              headerFilter: "input",
            },
            {
              title: "Jabatan",
              field: "user_cat",
              headerHozAlign: "center",
              hozAlign: "center",
              formatter: function (cell, formatterParams, onRendered) {
                var rowData = cell.getRow().getData();
                return rowData.position_name || "-";
              },
            },
            {
              title: "No. Hp",
              field: "no_phone",
              headerHozAlign: "center",
              hozAlign: "center",
            },
          ];

          if (roleId === "1" || roleId === "2") {
            columns.push({
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
                showUpdateModal(rowData);
              },
            });
          }

          var table = new Tabulator("#tabel_master_user_pelanggan", {
            data: data,
            height: "350px",
            pagination: "local",
            paginationSize: 25,
            paginationSizeSelector: [10, 25, 50],
            layout: "fitColumns",
            columns: columns,
          });
        },
        error: function (xhr, status, error) {
          console.error("Error fetching master user pelanggan data:", error);
          Swal.fire({
            title: "Error",
            text: "Gagal memuat data master user pelanggan.",
            icon: "error",
            confirmButtonText: "OK",
          });
        },
      });
    }

    // Fungsi untuk menampilkan modal dengan data user pelanggan
    function showUpdateModal(rowData) {
      console.log("Modal Row Data:", rowData); // Debugging

      // Simpan ID untuk update
      $("#updateUserPelangganModal").data("id", rowData.id);

      // Isi teks pelanggan
      $("#pelanggan").text(
        rowData.cust_id && rowData.cust_name
          ? `${rowData.cust_id} - ${rowData.cust_name}`
          : "-"
      );

      // Isi form input
      $("#namaUser").val(rowData.name || "");
      $("#noTelp").val(rowData.no_phone || "");

      // Isi dropdown posisi
      populatePosisiDropdown(rowData.user_cat);

      // Isi dropdown status
      $("#statusUser")
        .val(rowData.flg_used === "t" ? "true" : "false")
        .trigger("change");

      // Tampilkan modal
      $("#updateUserPelangganModal").modal("show");
    }

    // Fungsi AJAX untuk mengupdate data user pelanggan
    function updateUserPelanggan(id, name, user_cat, no_phone, flg_used) {
      return new Promise((resolve, reject) => {
        $.ajax({
          type: "POST",
          url: url + "master/userpelanggan/updateuserpelanggan",
          data: {
            id: id,
            name: name,
            user_cat: user_cat,
            no_phone: no_phone,
            flg_used: flg_used,
          },
          dataType: "json",
          success: function (response) {
            if (response.success) {
              Swal.fire({
                title: "Sukses",
                text: "Data user pelanggan berhasil diperbarui.",
                icon: "success",
                confirmButtonText: "OK",
              }).then(() => {
                // Reload halaman
                window.location.reload();
              });
              resolve();
            } else {
              Swal.fire({
                title: "Error",
                text: response.message || "Gagal memperbarui data.",
                icon: "error",
                confirmButtonText: "OK",
              });
              reject();
            }
          },
          error: function (xhr) {
            Swal.fire({
              title: "Error",
              text: "Terjadi kesalahan saat memperbarui data.",
              icon: "error",
              confirmButtonText: "OK",
            });
            console.error("Error:", xhr.responseText);
            reject();
          },
        });
      });
    }

    // Event handler untuk tombol Simpan
    $("#saveUpdate").on("click", function () {
      Swal.fire({
        title: "Konfirmasi",
        text: "Apakah Anda yakin memperbarui data?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya",
        cancelButtonText: "Batal",
      }).then((result) => {
        if (result.isConfirmed) {
          // Ambil data dari form
          var id = $("#updateUserPelangganModal").data("id");
          var name = $("#namaUser").val();
          var user_cat = $("#posisiUserPelanggan").val();
          var no_phone = $("#noTelp").val();
          var flg_used = $("#statusUser").val() === "true" ? "t" : "f";

          // Validasi sederhana
          if (!id) {
            Swal.fire({
              title: "Error",
              text: "ID tidak ditemukan.",
              icon: "error",
              confirmButtonText: "OK",
            });
            return;
          }

          // Panggil fungsi update
          updateUserPelanggan(id, name, user_cat, no_phone, flg_used);
        }
      });
    });
  } else if (window.location.pathname == "/master/kelasproduk/index") {
    data_kls_produk();

    // Custom filter editor untuk kolom flg_used
    var statusFilterEditor = function (
      cell,
      onRendered,
      success,
      cancel,
      editorParams
    ) {
      var select = document.createElement("select");
      select.style.padding = "4px";
      select.style.width = "100%";
      select.style.boxSizing = "border-box";

      // Opsi dropdown
      var options = [
        { value: "all", label: "Semua" },
        { value: "t", label: "Ya" },
        { value: "f", label: "Tidak" },
      ];

      // Tambahkan opsi ke dropdown
      options.forEach(function (option) {
        var opt = document.createElement("option");
        opt.value = option.value;
        opt.text = option.label;
        select.appendChild(opt);
      });

      // Set nilai awal (jika ada)
      select.value = cell.getValue() || "all";

      // Event handler untuk perubahan nilai
      function buildValue() {
        success(select.value);
      }

      // Trigger buildValue saat nilai berubah atau dropdown kehilangan fokus
      select.addEventListener("change", buildValue);
      select.addEventListener("blur", buildValue);

      // Handle tombol Escape untuk membatalkan
      select.addEventListener("keydown", function (e) {
        if (e.keyCode == 27) {
          cancel();
        }
      });

      return select;
    };

    // Custom filter function untuk kolom flg_used
    function statusFilterFunction(
      headerValue,
      rowValue,
      rowData,
      filterParams
    ) {
      // headerValue: nilai filter yang dipilih ("all", "t", "f")
      // rowValue: nilai flg_noo pada baris ("t", "f", atau null)
      if (headerValue === "all") {
        return true; // Tampilkan semua baris
      }
      return rowValue === headerValue; // Cocokkan nilai filter dengan nilai baris
    }

    function data_kls_produk() {
      $.ajax({
        type: "POST",
        url: url + "master/kelasproduk/getdatamstklsproduk",
        async: true,
        data: {},
        dataType: "json",

        success: function (data) {
          var table = new Tabulator("#tabel_master_kls_produk", {
            data: data,
            height: "350px",
            pagination: "local",
            paginationSize: 25,
            paginationSizeSelector: [10, 25, 50],
            layout: "fitColumns",
            columns: [
              {
                title: "Status",
                field: "flg_used",
                headerHozAlign: "center",
                hozAlign: "center",
                headerFilter: statusFilterEditor,
                headerFilterFunc: statusFilterFunction,
                headerFilterLiveFilter: false, // Nonaktifkan filter langsung untuk dropdown
                formatter: function (cell, formatterParams) {
                  var value = cell.getValue();
                  console.log("Flg value:", value); // Debugging: Log the value to ensure correctness

                  if (value === "t") {
                    return "<i class='fa fa-check' style='color:#03A791'></i>";
                  } else if (value === "f") {
                    return "<i class='fa fa-times' style='color:#FF5677'></i>";
                  }
                },
              },
              {
                title: "Grup Produk",
                field: "group_name",
                headerHozAlign: "center",
                formatter: function (cell, formatterParams, onRendered) {
                  var rowData = cell.getRow().getData();
                  return (
                    (rowData.group_id || "-") +
                    " - " +
                    (rowData.group_name || "-")
                  );
                },
              },
              {
                title: "Sub Grup Produk",
                field: "subgroup_name",
                headerHozAlign: "center",
                headerFilter: "input",
                formatter: function (cell, formatterParams, onRendered) {
                  var rowData = cell.getRow().getData();
                  return (
                    (rowData.subgroup_id || "-") +
                    " - " +
                    (rowData.subgroup_name || "-")
                  );
                },
              },
              {
                title: "Kelas Grup Produk",
                field: "class_name",
                headerHozAlign: "center",
                headerFilter: "input",
                formatter: function (cell, formatterParams, onRendered) {
                  var rowData = cell.getRow().getData();
                  return (
                    (rowData.class_id || "-") +
                    " - " +
                    (rowData.class_name || "-")
                  );
                },
              },
            ],
          });
        },
      });
    }
  } else if (window.location.pathname === "/master/karyawan/index") {
    // Deklarasi elemen filter
    const $cabang = $("#cabang");

    // Fungsi untuk mendapatkan semua nilai filter saat ini
    function getFilterValues() {
      var cabang = $cabang.val();
      return { cabang };
    }

    // Fetch cabang berdasarkan session branch_id
    if (branchId !== "11") {
      // Jika branch_id bukan '11', tampilkan hanya cabang dari session dan disable dropdown
      $cabang
        .empty()
        .append(
          `<option value="${branchId}" selected>${branchId} - ${branchName}</option>`
        );
      $cabang.select2(); // Inisialisasi Select2 untuk cabang
    } else {
      // Jika branch_id adalah '11', ambil semua cabang dari API
      $.getJSON(url + "master/cabang", (branches) => {
        $cabang
          .empty()
          .append('<option value="">Pilih Cabang</option>')
          .append(
            branches.map(
              (b) =>
                `<option value="${b.branch_id}">${b.branch_id} - ${b.branch_name}</option>`
            )
          );
        $cabang.select2(); // Inisialisasi Select2 untuk cabang
      });
    }

    // Event handler untuk cabang
    $cabang.on("change", function () {
      var filters = getFilterValues();
      data_karyawan(filters.cabang);
    });

    // Panggil data_karyawan dengan nilai default saat halaman dimuat
    var filters = getFilterValues();
    data_karyawan(filters.cabang);

    // Fungsi untuk menampilkan tabel distribusi produk
    function data_karyawan(cabang) {
      $.ajax({
        type: "POST",
        url: url + "master/karyawan/datakaryawan",
        async: true,
        data: { cabang: cabang },
        dataType: "json",
        success: function (data) {
          // Inisialisasi Tabulator
          var table = new Tabulator("#table_master_karyawan", {
            data: data,
            movableColumns: true,
            layout: "fitColumns",
            height: "500px",
            responsiveLayout: "collapse",
            pagination: "local",
            paginationSize: 50,
            paginationSizeSelector: [20, 50, 70],
            columns: [
              {
                title: "NIK",
                field: "nik",
                headerHozAlign: "center",
                hozAlign: "center",
              },
              {
                title: "Nama",
                field: "emp_name",
                headerHozAlign: "center",
                headerFilter: "input",
              },
              {
                title: "Cabang",
                field: "branch_name",
                headerHozAlign: "center",
              },
              {
                title: "Departemen",
                field: "department_name",
                headerHozAlign: "center",
              },
              {
                title: "Jabatan",
                field: "position_name",
                headerHozAlign: "center",
              },
              {
                title: "Hak Akses",
                field: "role_name",
                headerHozAlign: "center",
              },
              {
                title: "Aksi",
                headerHozAlign: "center",
                hozAlign: "center",
                formatter: function (cell, formatterParams, onRendered) {
                  var rowData = cell.getRow().getData();
                  return `
                  <a class="badge rounded-circle p-2 badge-success update-btn" href="#" data-id="${rowData.id}">
                      <i class="fa fa-edit" style="cursor: pointer;"></i>
                  </a>`;
                },
                cellClick: function (e, cell) {
                  var target = e.target.closest("a");
                  if (!target) return;

                  var row = cell.getRow();
                  var rowData = row.getData();

                  if (target.classList.contains("update-btn")) {
                    showUpdateModal(rowData.id, rowData.nik);
                  }
                },
              },
            ],
          });
        },
        error: function (xhr, status, err) {
          console.error("Error fetching kunjungan sales:", err);
        },
      });
    }

    // Fungsi untuk menampilkan modal update
    function showUpdateModal(id, nik) {
      // Fetch data karyawan berdasarkan id
      $.getJSON(url + `master/karyawan/getkaryawan/${id}`, function (data) {
        if (data) {
          // Isi form dengan data awal
          $("#nikKaryawan").val(data.nik).prop("disabled", true);
          $("#namaKaryawan").val(data.emp_name).prop("disabled", true);
          $("#cabKaryawan").val(data.branch_id).trigger("change");
          $("#depKaryawan").val(data.department_id).trigger("change");
          $("#jabKaryawan").val(data.position_id).trigger("change");
          $("#roleKaryawan").val(data.role_id).trigger("change");

          // Tambahkan input tersembunyi untuk id
          $("#formUpdateKaryawan").append(
            `<input type="hidden" name="id" value="${id}">`
          );

          // Tampilkan modal
          $("#updateKaryawanModal").modal("show");

          // Event submit form
          $("#formUpdateKaryawan")
            .off("submit")
            .on("submit", function (e) {
              e.preventDefault();
              Swal.fire({
                title: "Anda yakin?",
                text: "Data karyawan akan diperbarui!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, simpan!",
              }).then((result) => {
                if (result.isConfirmed) {
                  $.ajax({
                    type: "POST",
                    url: url + "master/karyawan/updatedatakaryawan",
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function (response) {
                      if (response.status === "success") {
                        Swal.fire(
                          "Berhasil!",
                          response.message,
                          "success"
                        ).then(() => {
                          $("#updateKaryawanModal").modal("hide");
                          location.reload(); // Refresh halaman untuk update tabel
                        });
                      } else {
                        Swal.fire("Gagal!", response.message, "error");
                      }
                    },
                    error: function (xhr, status, err) {
                      Swal.fire(
                        "Error!",
                        "Terjadi kesalahan saat menyimpan data.",
                        "error"
                      );
                    },
                  });
                }
              });
            });
        }
      });
    }
  } else if (window.location.pathname == "/master/karyawan/formulir") {
    document
      .getElementById("formKaryawan")
      .addEventListener("submit", function (e) {
        e.preventDefault(); // Mencegah submit langsung
        Swal.fire({
          title: "Anda yakin?",
          text: "Data karyawan akan disimpan!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Ya, simpan!",
        }).then((result) => {
          if (result.isConfirmed) {
            this.submit(); // Submit form jika dikonfirmasi
          }
        });
      });
  } else if (window.location.pathname === "/master/user/index") {
    data_user();

    // Fungsi untuk menampilkan tabel distribusi produk
    function data_user() {
      $.ajax({
        type: "POST",
        url: url + "master/user/datauser",
        async: true,
        data: {},
        dataType: "json",
        success: function (data) {
          // Inisialisasi Tabulator
          var table = new Tabulator("#table_master_user", {
            data: data,
            movableColumns: true,
            layout: "fitColumns",
            height: "500px",
            responsiveLayout: "collapse",
            pagination: "local",
            paginationSize: 50,
            paginationSizeSelector: [20, 50, 70],
            columns: [
              {
                title: "username",
                field: "username",
                headerHozAlign: "center",
                hozAlign: "center",
                headerFilter: "input",
              },
              {
                title: "Status",
                field: "flg_used",
                headerHozAlign: "center",
                hozAlign: "center",
                editor: "list",
                editorParams: {
                  values: {
                    t: "AKTIF",
                    f: "TIDAK AKTIF",
                  },
                },
                formatter: function (cell, formatterParams) {
                  var value = cell.getValue();
                  console.log("Status value:", value);
                  if (value === "t") {
                    return "<p>AKTIF</p>";
                  } else if (value === "f") {
                    return "<p>TIDAK AKTIF</p>";
                  }
                  return value;
                },
                cellEdited: function (cell) {
                  var newValue = cell.getValue();
                  var rowData = cell.getRow().getData();
                  var id = rowData.id;

                  if (newValue === "f") {
                    Swal.fire({
                      title: "Anda yakin?",
                      text: "Status akan diubah menjadi TIDAK AKTIF!",
                      icon: "warning",
                      showCancelButton: true,
                      confirmButtonColor: "#3085d6",
                      cancelButtonColor: "#d33",
                      confirmButtonText: "Ya, ubah!",
                    }).then((result) => {
                      if (result.isConfirmed) {
                        $.ajax({
                          type: "POST",
                          url: url + "master/user/updatedatauser",
                          data: {
                            id: id,
                            status: newValue,
                          },
                          dataType: "json",
                          success: function (response) {
                            if (response.status === "success") {
                              Swal.fire(
                                "Berhasil!",
                                response.message,
                                "success"
                              );
                            } else {
                              Swal.fire(
                                "Gagal!",
                                response.message,
                                "error"
                              ).then(() => {
                                cell.restoreOldValue();
                              });
                            }
                          },
                          error: function (xhr, status, err) {
                            Swal.fire(
                              "Error!",
                              "Terjadi kesalahan saat menyimpan data.",
                              "error"
                            );
                            cell.restoreOldValue();
                          },
                        });
                      } else {
                        cell.restoreOldValue();
                      }
                    });
                  }
                },
              },
            ],
          });
          console.log("Tabulator Editors:", Tabulator.Editors); // Debug editor
        },
        error: function (xhr, status, err) {
          console.error("Error fetching data:", err);
        },
      });
    }
  } else if (window.location.pathname === "/master/user/formulir") {
    // Fetch data karyawan baru
    $.ajax({
      url: url + "master/user/filterkaryawan",
      method: "GET",
      dataType: "json",
      success: function (data) {
        $("#masterkaryawan").empty();
        $("#masterkaryawan").append(
          '<option value="" selected>Pilih Karyawan</option>'
        );
        data.forEach((userbaru) => {
          $("#masterkaryawan").append(
            `<option value="${userbaru.id}" 
                         idRefUser="${userbaru.id}" 
                         usernameKaryawan="${userbaru.nik}">
                         ${userbaru.emp_name}
                    </option>`
          );
        });
        $("#masterkaryawan").select2({
          placeholder: "Pilih Karyawan",
          allowClear: true,
        });

        $("#masterkaryawan").on("change", function () {
          const selectedOption = $(this).find("option:selected");
          const id = selectedOption.val(); // Ambil id dari value
          const username = selectedOption.attr("usernameKaryawan"); // Ambil nik sebagai username

          // Isi input tersembunyi idRefUser
          $("#idRefUser").val(id);
          // Isi field usernameKaryawan (disabled, hanya untuk tampilan)
          $("#usernameKaryawan").val(username);
        });
      },
      error: function (xhr, status, error) {
        console.error("Error fetching data pelanggan:", error);
        alert("Gagal memuat data pelanggan.");
      },
    });

    document
      .getElementById("formUser")
      .addEventListener("submit", function (e) {
        e.preventDefault(); // Mencegah submit langsung
        Swal.fire({
          title: "Anda yakin?",
          text: "Data user akan disimpan!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Ya, simpan!",
        }).then((result) => {
          if (result.isConfirmed) {
            this.submit(); // Submit form jika dikonfirmasi
          }
        });
      });
  }
});
