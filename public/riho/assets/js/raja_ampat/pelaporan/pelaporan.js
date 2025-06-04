$(document).ready(function () {
  if (window.location.pathname === "/pelaporan/aktivitas_kunj") {
    const MAP_ID = "map"; // Pastikan <div id="map"> ada di view

    let aktivitasData = [];
    let map;

    // Inisialisasi tanggal awal
    let currentDate = new Date().toJSON().slice(0, 10);

    // Deklarasi elemen filter
    const $rentangTanggal = $("#rentangTanggalReport");
    const $cabang = $("#cabangaktivitaskunj");
    const $sales = $("#salesMarketing");

    // Inisialisasi DateRangePicker
    $rentangTanggal.daterangepicker({
      startDate: currentDate,
      endDate: currentDate,
      locale: {
        format: "YYYY-MM-DD",
      },
    });

    // Fungsi untuk mendapatkan semua nilai filter saat ini
    function getFilterValues() {
      var dates = $rentangTanggal.data("daterangepicker");
      var tgl_1 = dates.startDate.format("YYYY-MM-DD");
      var tgl_2 = dates.endDate.format("YYYY-MM-DD");
      var cabang = $cabang.val();
      var salesMarketing = $sales.val();
      return {
        tgl_1,
        tgl_2,
        cabang,
        salesMarketing,
      };
    }

    // Fetch cabang
    $.getJSON(url + "master/cabang", (branches) => {
      $cabang
        .empty()
        .append('<option value="">Pilih Cabang</option>')
        .append(
          branches.map(
            (b) => `<option value="${b.branch_id}">${b.branch_name}</option>`
          )
        );
    });

    // Event handler untuk cabang
    $cabang.on("change", function () {
      const cab = this.value;
      $sales.empty().append('<option value="">Pilih Sales</option>');
      if (cab) {
        $.post(
          url + "master/salesmarketing",
          { branch_id: cab },
          (salesList) => {
            salesList.forEach((s) =>
              $sales.append(`<option value="${s.nik}">${s.name}</option>`)
            );
          },
          "json"
        );
      }
      var filters = getFilterValues();
      data_aktivitas_sales(
        filters.tgl_1,
        filters.tgl_2,
        filters.cabang,
        filters.salesMarketing
      );
    });

    // Event handler untuk sales/marketing
    $sales.on("change", function () {
      var filters = getFilterValues();
      data_aktivitas_sales(
        filters.tgl_1,
        filters.tgl_2,
        filters.cabang,
        filters.salesMarketing
      );
    });

    // Event handler untuk DateRangePicker
    $rentangTanggal.on("apply.daterangepicker", function (ev, picker) {
      var filters = getFilterValues();
      data_aktivitas_sales(
        filters.tgl_1,
        filters.tgl_2,
        filters.cabang,
        filters.salesMarketing
      );
    });

    // Muat sekali di awal
    var initialFilters = getFilterValues();
    data_aktivitas_sales(
      initialFilters.tgl_1,
      initialFilters.tgl_2,
      initialFilters.cabang,
      initialFilters.salesMarketing
    );

    // Inisialisasi peta sekali saja
    function initMap() {
      const el = document.getElementById(MAP_ID);
      el.style.height = "350px";
      el.style.margin = "5px";

      map = L.map(MAP_ID, {
        center: [-2.5, 117.6],
        zoom: 10,
        maxZoom: 19,
      });

      const indonesiaBounds = [
        [-10.5, 95.0],
        [6.5, 141.0],
      ];
      map.setMaxBounds(indonesiaBounds).fitBounds(indonesiaBounds);

      L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution:
          '© <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
        maxZoom: 19,
      }).addTo(map);
    }

    // Render / refresh marker
    function renderMap() {
      if (!map) initMap();

      // Hapus marker & popup lama
      map.eachLayer((layer) => {
        if (layer instanceof L.CircleMarker || layer instanceof L.Popup) {
          map.removeLayer(layer);
        }
      });

      // Tambahkan marker baru
      aktivitasData.forEach((loc) => {
        // Buat marker biasa (default Leaflet marker)
        const marker = L.marker([loc.latitude, loc.longitude])
          .addTo(map)
          .bindPopup(`<b>Pelanggan:</b> ${loc.cust_name}`);

        // Event click untuk filter tabel berdasarkan cust_id
        marker.on("click", function () {
          const date = loc.date;
          const branch_id = loc.branch_id;
          const nik = loc.nik;
          const cust_id = loc.cust_id;
          data_ditribusi_prod(date, branch_id, nik, cust_id);
        });

        // Event hover untuk menampilkan popup
        marker.on("mouseover", function () {
          this.openPopup();
        });

        marker.on("mouseout", function () {
          this.closePopup();
        });
      });
    }

    // Ambil data & update peta
    function data_aktivitas_sales(tgl_1, tgl_2, cabang, salesMarketing) {
      return $.ajax({
        type: "POST",
        url: url + "pelaporan/aktivitas_kunj/data_aktivitas",
        data: {
          tanggal_1: tgl_1,
          tanggal_2: tgl_2,
          cabang: cabang,
          sales_marketing: salesMarketing,
        },
        dataType: "json",
        success: function (data) {
          aktivitasData = data;
          renderMap();
        },
        error: function (xhr, status, err) {
          console.error("Error fetching aktivitas:", err);
          aktivitasData = [];
          renderMap();
        },
      });
    }

    // Fungsi untuk menampilkan tabel distribusi produk
    function data_ditribusi_prod(date, branch_id, nik, cust_id) {
      $.ajax({
        type: "POST",
        url: url + "pelaporan/aktivitas_kunj/data_distribusi_prod",
        async: true,
        data: {
          tanggal: date,
          cabang: branch_id,
          sales_marketing: nik,
          pelanggan: cust_id,
        },
        dataType: "json",
        success: function (data) {
          // Filter data berdasarkan cust_id (opsional, jika server belum memfilter)
          var filteredData = data.filter(function (item) {
            return item.cust_id === cust_id;
          });

          // Fungsi untuk menghitung jumlah unik nik
          function getUniqueNikCount(data) {
            let nikSet = new Set(data.map((item) => item.nik));
            return nikSet.size;
          }

          // Fungsi untuk menghitung jumlah unik class_id
          function getUniqueClassIdCount(data) {
            let classIdSet = new Set(data.map((item) => item.class_id));
            return classIdSet.size;
          }

          // Inisialisasi Tabulator
          var table = new Tabulator("#table_distribusi_prod", {
            data: filteredData,
            movableColumns: true,
            layout: "fitColumns",
            height: "500px",
            responsiveLayout: "collapse",
            pagination: "local",
            paginationSize: 50,
            paginationSizeSelector: [20, 50, 70],
            groupBy: ["date", "emp_name"], // Mengelompokkan berdasarkan date dan emp_name
            groupStartOpen: [true, false], // Grup date terbuka, grup emp_name tertutup
            groupHeader: [
              function (value, count, data) {
                // Grup header untuk date
                let uniqueNikCount = getUniqueNikCount(data);
                return `${value} - <span style="color:#27548A">Jumlah Sales / Marketing: ${uniqueNikCount}</span>`;
              },
              function (value, count, data) {
                // Grup header untuk emp_name
                let uniqueClassIdCount = getUniqueClassIdCount(data);
                return `${value} - <span style="color:#27548A">Jumlah Barang: ${uniqueClassIdCount}</span>`;
              },
            ],
            columns: [
              {
                title: "Tanggal",
                field: "date",
                headerHozAlign: "center",
                hozAlign: "center",
                visible: false, // Sembunyikan kolom date di baris data
              },
              {
                title: "Sales / Marketing",
                field: "emp_name",
                headerHozAlign: "center",
                visible: false, // Sembunyikan kolom emp_name di baris data
              },
              {
                title: "Pelanggan",
                field: "cust_name",
                headerHozAlign: "center",
                formatter: function (cell) {
                  var rowData = cell.getRow().getData();
                  return rowData.cust_id + " - " + rowData.cust_name;
                },
              },
              {
                title: "Kelas Barang",
                field: "class_name",
                headerHozAlign: "center",
                formatter: function (cell) {
                  var rowData = cell.getRow().getData();
                  return (
                    rowData.group_id +
                    "-" +
                    rowData.subgroup_id +
                    "-" +
                    rowData.class_id +
                    " " +
                    rowData.class_name
                  );
                },
              },
              {
                title: "Realisasi Nilai",
                field: "tot_real_value",
                headerHozAlign: "center",
                hozAlign: "right",
                formatter: "money",
                formatterParams: { decimal: ",", thousand: "." },
              },
              {
                title: "Target Nilai",
                field: "tot_target_value",
                headerHozAlign: "center",
                hozAlign: "right",
                formatter: "money",
                formatterParams: { decimal: ",", thousand: "." },
              },
              {
                title: "Pencapaian (%)",
                field: "prs_value",
                headerHozAlign: "center",
                hozAlign: "center",
              },
              {
                title: "Non Route?",
                field: "flg_non_route",
                headerHozAlign: "center",
                hozAlign: "center",
                formatter: function (cell) {
                  var value = cell.getValue();
                  if (value === "t") {
                    return "<i class='fa fa-check' style='color:#03A791'></i>";
                  } else if (value === "f") {
                    return "<i class='fa fa-times' style='color:#FF5677'></i>";
                  }
                },
              },
              {
                title: "Terkunjungi?",
                field: "flg_visit",
                headerHozAlign: "center",
                hozAlign: "center",
                formatter: function (cell) {
                  var value = cell.getValue();
                  if (value === "t") {
                    return "<i class='fa fa-check' style='color:#03A791'></i>";
                  } else if (value === "f") {
                    return "<i class='fa fa-times' style='color:#FF5677'></i>";
                  }
                },
              },
            ],
          });

          // Tampilkan tabel
          $("#table_distribusi_prod").show();
        },
        error: function (xhr, status, err) {
          console.error("Error fetching distribusi prod:", err);
        },
      });
    }

    // Sembunyikan tabel saat halaman dimuat
    $("#table_distribusi_prod").hide();
  } else if (window.location.pathname === "/pelaporan/distribusi_prod") {
    const MAP_ID = "map"; // Pastikan <div id="map"> ada di view

    let distribusiProdLocData = [];
    let map;

    // Inisialisasi tanggal awal
    let currentDate = new Date().toJSON().slice(0, 10);

    // Deklarasi elemen filter
    const $rentangTanggal = $("#rentangTanggalDistribusi");
    const $cabang = $("#cabangdistprod");
    const $groupId = $("#grupBarang");
    const $subgroupId = $("#subgrupBarang");
    const $classId = $("#kelasBarang");

    // Inisialisasi Select2 untuk semua dropdown
    $groupId.select2({
      placeholder: "SEMUA GRUP",
      allowClear: true,
      closeOnSelect: false,
    });

    $subgroupId.select2({
      placeholder: "SEMUA SUBGRUP",
      allowClear: true,
      closeOnSelect: false,
    });

    $classId.select2({
      placeholder: "SEMUA KELAS",
      allowClear: true,
      closeOnSelect: false,
    });

    // Inisialisasi DateRangePicker
    $rentangTanggal.daterangepicker({
      startDate: currentDate,
      endDate: currentDate,
      locale: {
        format: "YYYY-MM-DD",
      },
    });

    // Fungsi untuk mendapatkan semua nilai filter saat ini
    function getFilterValues() {
      var dates = $rentangTanggal.data("daterangepicker");
      var tgl_1 = dates.startDate.format("YYYY-MM-DD");
      var tgl_2 = dates.endDate.format("YYYY-MM-DD");
      var cabang = $cabang.val();
      var grup_barang = $groupId.val();
      var subgrup_barang = $subgroupId.val();
      var kelas_barang = $classId.val();
      return {
        tgl_1,
        tgl_2,
        cabang,
        grup_barang,
        subgrup_barang,
        kelas_barang,
      };
    }

    // Fungsi untuk memuat subgroup berdasarkan group_id
    function loadSubgroups(groupId) {
      $.ajax({
        url: url + "master/filter/subgrup",
        method: "POST",
        data: { group_prod: groupId },
        success: function (data) {
          $subgroupId.html(data).val(null).trigger("change"); // Set default ke SEMUA SUBGRUP
        },
        error: function (xhr, status, error) {
          console.error("Error fetching subgroup data:", error);
          $subgroupId.html('<option value="">Gagal memuat subgroup</option>');
        },
      });
    }

    // Fungsi untuk memuat class berdasarkan group_id dan subgroup_id
    function loadClasses(groupId, subgroupId) {
      $.ajax({
        url: url + "master/filter/kelas",
        method: "POST",
        data: {
          group_prod: groupId,
          subgroup_prod: subgroupId,
        },
        success: function (data) {
          $classId.html(data).val(null).trigger("change"); // Set default ke SEMUA KELAS
        },
        error: function (xhr, status, error) {
          console.error("Error fetching class data:", error);
          $classId.html('<option value="">Gagal memuat kelas</option>');
        },
      });
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
              (b) => `<option value="${b.branch_id}">${b.branch_name}</option>`
            )
          );
        $cabang.select2(); // Inisialisasi Select2 untuk cabang
      });
    }

    // Muat subgroup dan class secara default saat halaman dimuat
    loadSubgroups(null); // Memuat SEMUA SUBGRUP
    loadClasses(null, null); // Memuat SEMUA KELAS

    // Event handler untuk DateRangePicker
    $rentangTanggal.on("apply.daterangepicker", function (ev, picker) {
      var filters = getFilterValues();
      data_distribusi_prod(
        filters.tgl_1,
        filters.tgl_2,
        filters.cabang,
        filters.grup_barang,
        filters.subgrup_barang,
        filters.kelas_barang
      );
    });

    // Event handler untuk cabang
    $cabang.on("change", function () {
      var filters = getFilterValues();
      data_distribusi_prod(
        filters.tgl_1,
        filters.tgl_2,
        filters.cabang,
        filters.grup_barang,
        filters.subgrup_barang,
        filters.kelas_barang
      );
    });

    // Event handler untuk Grup Barang
    $groupId.on("change", function () {
      var groupId = $(this).val();
      loadSubgroups(groupId); // Memuat subgroup berdasarkan group_id
      var filters = getFilterValues();
      data_distribusi_prod(
        filters.tgl_1,
        filters.tgl_2,
        filters.cabang,
        filters.grup_barang,
        filters.subgrup_barang,
        filters.kelas_barang
      );
    });

    // Event handler untuk Subgrup Barang
    $subgroupId.on("change", function () {
      var groupId = $groupId.val();
      var subgroupId = $(this).val();
      loadClasses(groupId, subgroupId); // Memuat class berdasarkan group_id dan subgroup_id
      var filters = getFilterValues();
      data_distribusi_prod(
        filters.tgl_1,
        filters.tgl_2,
        filters.cabang,
        filters.grup_barang,
        filters.subgrup_barang,
        filters.kelas_barang
      );
    });

    // Event handler untuk Class Barang
    $classId.on("change", function () {
      var filters = getFilterValues();
      data_distribusi_prod(
        filters.tgl_1,
        filters.tgl_2,
        filters.cabang,
        filters.grup_barang,
        filters.subgrup_barang,
        filters.kelas_barang
      );
    });

    // Panggil data_distribusi_prod dengan nilai default saat halaman dimuat
    var filters = getFilterValues();
    data_distribusi_prod(
      filters.tgl_1,
      filters.tgl_2,
      filters.cabang,
      filters.grup_barang,
      filters.subgrup_barang,
      filters.kelas_barang
    );

    // Inisialisasi peta sekali saja
    function initMap() {
      const el = document.getElementById(MAP_ID);
      el.style.height = "350px";
      el.style.margin = "5px";

      map = L.map(MAP_ID, {
        center: [-2.5, 117.6],
        zoom: 10,
        maxZoom: 19,
      });

      const indonesiaBounds = [
        [-10.5, 95.0],
        [6.5, 141.0],
      ];
      map.setMaxBounds(indonesiaBounds).fitBounds(indonesiaBounds);

      L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution:
          '© <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
        maxZoom: 19,
      }).addTo(map);
    }

    // Render / refresh marker
    function renderMap() {
      if (!map) initMap();

      // Hapus marker & popup lama
      map.eachLayer((layer) => {
        if (layer instanceof L.CircleMarker || layer instanceof L.Popup) {
          map.removeLayer(layer);
        }
      });

      // Tambahkan marker baru dengan warna berdasarkan flg_noo
      distribusiProdLocData.forEach((loc) => {
        // Buat marker biasa (default Leaflet marker)
        const marker = L.marker([loc.latitude, loc.longitude])
          .addTo(map)
          .bindPopup(
            "<b>Pelanggan: </b>" +
              loc.cust_name +
              "<br>" +
              "<b>Sales / Marketing: </b>" +
              loc.emp_name
          );

        // Event hover untuk menampilkan popup
        marker.on("mouseover", function () {
          this.openPopup();
        });
        marker.on("mouseout", function () {
          this.closePopup();
        });
      });
    }

    // Ambil data & update peta
    function data_distribusi_prod(
      tgl_1,
      tgl_2,
      cabang,
      grup_barang,
      subgrup_barang,
      kelas_barang
    ) {
      return $.ajax({
        type: "POST",
        url: url + "pelaporan/distribusi_prod/data_distribusi_prod_loc",
        data: {
          tanggal_1: tgl_1,
          tanggal_2: tgl_2,
          cabang: cabang,
          grp_prod: grup_barang,
          subgrp_prod: subgrup_barang,
          klsgrp_prod: kelas_barang,
        },
        dataType: "json",
        success: function (data) {
          distribusiProdLocData = data;
          renderMap();
        },
        error: function (xhr, status, err) {
          console.error("Error fetching distribusi:", err);
          distribusiProdLocData = [];
          renderMap();
        },
      });
    }
  } else if (window.location.pathname === "/pelaporan/kunjungan_sales") {
    // Deklarasi elemen filter
    const $cabang = $("#cabangkunjsales");

    // Fungsi untuk mendapatkan semua nilai filter saat ini
    function getFilterValues() {
      var cabang = $cabang.val();
      return {
        cabang,
      };
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
              (b) => `<option value="${b.branch_id}">${b.branch_name}</option>`
            )
          );
        $cabang.select2(); // Inisialisasi Select2 untuk cabang
      });
    }

    // Event handler untuk cabang
    $cabang.on("change", function () {
      var filters = getFilterValues();
      data_kunjungan_sales(filters.cabang);
    });

    // Panggil data_kunjungan_sales dengan nilai default saat halaman dimuat
    var filters = getFilterValues();
    data_kunjungan_sales(filters.cabang);

    // Fungsi untuk menampilkan tabel distribusi produk
    function data_kunjungan_sales(cabang) {
      $.ajax({
        type: "POST",
        url: url + "pelaporan/kunjungan_sales/data_kunjungan_sales",
        async: true,
        data: {
          cabang: cabang,
        },
        dataType: "json",
        success: function (data) {
          // Fungsi untuk menghitung jumlah unik nik
          function getUniqueEmpCount(data) {
            let empSet = new Set(data.map((item) => item.nik));
            return empSet.size;
          }

          // Fungsi untuk menghitung jumlah unik pelanggan
          function getUniqueCustIdCount(data) {
            let custIdSet = new Set(data.map((item) => item.cust_id));
            return custIdSet.size;
          }

          // Inisialisasi Tabulator
          var table = new Tabulator("#table_kunjungan_sales", {
            data: data,
            movableColumns: true,
            layout: "fitColumns",
            height: "500px",
            responsiveLayout: "collapse",
            pagination: "local",
            paginationSize: 50,
            paginationSizeSelector: [20, 50, 70],
            groupBy: ["month", "emp_name"], // Mengelompokkan berdasarkan date dan emp_name
            groupStartOpen: [true, false], // Grup date terbuka, grup emp_name tertutup
            groupHeader: [
              function (value, count, data) {
                // Grup header untuk bulan
                let uniqueEmpCount = getUniqueEmpCount(data);
                return `Bulan ${value} - <span style="color:#27548A">Jumlah Sales: ${uniqueEmpCount}</span>`;
              },
              function (value, count, data) {
                // Grup header untuk emp_name
                let uniqueCustIdCount = getUniqueCustIdCount(data);
                return `${value} - <span style="color:#27548A">Jumlah Pelanggan: ${uniqueCustIdCount}</span>`;
              },
            ],
            columns: [
              {
                title: "Bulan",
                field: "month",
                headerHozAlign: "center",
                hozAlign: "center",
                visible: false, // Sembunyikan kolom date di baris data
              },
              {
                title: "Nama",
                field: "emp_name",
                headerHozAlign: "center",
                visible: false, // Sembunyikan kolom date di baris data
              },
              {
                title: "Pelanggan",
                field: "cust_name",
                headerHozAlign: "center",
                formatter: function (cell) {
                  var rowData = cell.getRow().getData();
                  return rowData.cust_id + " - " + rowData.cust_name;
                },
                // frozen: true,
              },
              {
                title: "Realisasi Call",
                field: "real_call",
                headerHozAlign: "center",
                hozAlign: "center",
              },
              {
                title: "Target Call",
                field: "target_call",
                headerHozAlign: "center",
                hozAlign: "center",
              },
              {
                title: "Call (%)",
                field: "prs_call",
                headerHozAlign: "center",
                hozAlign: "center",
              },
              {
                title: "Realisasi EC",
                field: "real_ec",
                headerHozAlign: "center",
                hozAlign: "center",
              },
              {
                title: "Target EC",
                field: "target_ec",
                headerHozAlign: "center",
                hozAlign: "center",
              },
              {
                title: "EC (%)",
                field: "prs_ec",
                headerHozAlign: "center",
                hozAlign: "center",
              },
              {
                title: "Realisasi nilai",
                field: "real_value",
                headerHozAlign: "center",
                hozAlign: "right",
                formatter: "money",
                formatterParams: { decimal: ",", thousand: "." },
              },
              {
                title: "Target nilai",
                field: "target_value",
                headerHozAlign: "center",
                hozAlign: "right",
                formatter: "money",
                formatterParams: { decimal: ",", thousand: "." },
              },
              {
                title: "Nilai (%)",
                field: "prs_value",
                headerHozAlign: "center",
                hozAlign: "center",
              },
            ],
          });
        },
        error: function (xhr, status, err) {
          console.error("Error fetching kunjungan sales:", err);
        },
      });
    }
  } else if (window.location.pathname === "/pelaporan/kunjungan_marketing") {
    // Fungsi umum untuk fetch cabang berdasarkan session branch_id
    function initializeCabang($cabang, branchId, branchName) {
      if (branchId !== "11") {
        $cabang
          .empty()
          .append(
            `<option value="${branchId}" selected>${branchId} - ${branchName}</option>`
          );
        $cabang.select2({ disabled: true });
      } else {
        $.getJSON(url + "master/cabang", (branches) => {
          $cabang
            .empty()
            .append('<option value="">Pilih Cabang</option>')
            .append(
              branches.map(
                (b) =>
                  `<option value="${b.branch_id}">${b.branch_name}</option>`
              )
            );
          $cabang.select2();
        });
      }
    }

    // Logika untuk Tab Penggunaan
    function initializeTabPenggunaan() {
      const $cabang = $("#cabangkunjmarketing");

      // Inisialisasi cabang
      initializeCabang($cabang, branchId, branchName);

      // Fungsi untuk mendapatkan nilai filter cabang
      function getPenggunaanFilterValues() {
        return { cabang: $cabang.val() };
      }

      // Event handler untuk cabang
      $cabang.on("change", function () {
        var filters = getPenggunaanFilterValues();
        data_kunjungan_marketing_guna(filters.cabang);
      });

      // Fungsi untuk menampilkan tabel kunjungan marketing guna
      function data_kunjungan_marketing_guna(cabang) {
        $.ajax({
          type: "POST",
          url: url + "pelaporan/kunjungan_marketing/data_kunj_marketing_guna",
          async: true,
          data: { cabang: cabang },
          dataType: "json",
          success: function (data) {
            function getUniqueEmpCount(data) {
              let empSet = new Set(data.map((item) => item.nik));
              return empSet.size;
            }

            var table = new Tabulator("#table_kunj_marketing_guna", {
              data: data,
              movableColumns: true,
              layout: "fitColumns",
              height: "500px",
              responsiveLayout: "collapse",
              pagination: "local",
              paginationSize: 50,
              paginationSizeSelector: [20, 50, 70],
              groupBy: ["month"],
              groupStartOpen: [false],
              groupHeader: [
                function (value, count, data) {
                  let uniqueEmpCount = getUniqueEmpCount(data);
                  return `Bulan ${value} - <span style="color:#27548A">Jumlah Marketing: ${uniqueEmpCount}</span>`;
                },
              ],
              columns: [
                {
                  title: "Bulan",
                  field: "month",
                  headerHozAlign: "center",
                  hozAlign: "center",
                  visible: false,
                },
                {
                  title: "Nama",
                  field: "emp_name",
                  headerHozAlign: "center",
                  hozAlign: "center",
                },
                {
                  title: "Realisasi Rencana",
                  field: "tot_real_plan",
                  headerHozAlign: "center",
                  hozAlign: "center",
                },
                {
                  title: "Target Rencana",
                  field: "tot_target_plan",
                  headerHozAlign: "center",
                  hozAlign: "center",
                },
                {
                  title: "Rencana (%)",
                  field: "prs_plan",
                  headerHozAlign: "center",
                  hozAlign: "center",
                },
                {
                  title: "Realisasi Kunjungan",
                  field: "tot_real",
                  headerHozAlign: "center",
                  hozAlign: "center",
                },
                {
                  title: "Target Kunjungan",
                  field: "tot_target_real",
                  headerHozAlign: "center",
                  hozAlign: "center",
                },
                {
                  title: "Kunjungan (%)",
                  field: "prs_real",
                  headerHozAlign: "center",
                  hozAlign: "center",
                },
              ],
            });
          },
          error: function (xhr, status, err) {
            console.error("Error fetching kunjungan marketing guna:", err);
          },
        });
      }

      // Inisialisasi awal
      var filters = getPenggunaanFilterValues();
      data_kunjungan_marketing_guna(filters.cabang);
    }

    // Logika untuk Tab Pelanggan
    function initializeTabPelanggan() {
      const $subgroupId = $("#subgrupBarang");
      const $classId = $("#kelasBarang");

      // Inisialisasi Select2
      $subgroupId.select2({
        placeholder: "SEMUA SUBGRUP",
        allowClear: true,
        closeOnSelect: false,
      });

      $classId.select2({
        placeholder: "SEMUA KELAS",
        allowClear: true,
        closeOnSelect: false,
      });

      // Fungsi untuk mendapatkan nilai filter pelanggan
      function getPelangganFilterValues() {
        return {
          subgrup_barang: $subgroupId.val(),
          kelas_barang: $classId.val(),
        };
      }

      // Fungsi untuk memuat subgroup
      function loadSubgroups(groupId) {
        $.ajax({
          url: url + "master/filter/subgrup",
          method: "POST",
          data: { group_prod: "02" },
          success: function (data) {
            $subgroupId.html(data).val(null).trigger("change");
          },
          error: function (xhr, status, error) {
            console.error("Error fetching subgroup data:", error);
            $subgroupId.html('<option value="">Gagal memuat subgroup</option>');
          },
        });
      }

      // Fungsi untuk memuat class
      function loadClasses(groupId, subgroupId) {
        $.ajax({
          url: url + "master/filter/kelas",
          method: "POST",
          data: {
            group_prod: "02",
            subgroup_prod: subgroupId,
          },
          success: function (data) {
            $classId.html(data).val(null).trigger("change");
          },
          error: function (xhr, status, error) {
            console.error("Error fetching class data:", error);
            $classId.html('<option value="">Gagal memuat kelas</option>');
          },
        });
      }

      // Fungsi untuk menampilkan tabel kunjungan marketing pelanggan
      function data_kunjungan_marketing_plg(subgrup_barang, kelas_barang) {
        $.ajax({
          type: "POST",
          url: url + "pelaporan/kunjungan_marketing/data_kunj_marketing_outlet",
          async: true,
          data: {
            subgrp_prod: subgrup_barang,
            klsgrp_prod: kelas_barang,
          },
          dataType: "json",
          success: function (data) {
            var table = new Tabulator("#table_kunj_marketing_pelanggan", {
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
                  title: "Cabang",
                  field: "branch_name",
                  headerHozAlign: "center",
                },
                {
                  title: "Instansi",
                  field: "tot_visit_inst",
                  headerHozAlign: "center",
                  hozAlign: "center",
                },
                {
                  title: "Laboratorium",
                  field: "tot_visit_lab",
                  headerHozAlign: "center",
                  hozAlign: "center",
                },
                {
                  title: "RS. Pemerintah",
                  field: "tot_visit_rs",
                  headerHozAlign: "center",
                  hozAlign: "center",
                },
                {
                  title: "RS. Swasta",
                  field: "tot_visit_rs_swasta",
                  headerHozAlign: "center",
                  hozAlign: "center",
                },
                {
                  title: "Total",
                  field: "tot_visit",
                  headerHozAlign: "center",
                  hozAlign: "center",
                },
                {
                  title: "Outlet Reg",
                  field: "tot_customer",
                  headerHozAlign: "center",
                  hozAlign: "center",
                },
                {
                  title: "ER (%)",
                  field: "prs_er",
                  headerHozAlign: "center",
                  hozAlign: "center",
                },
              ],
            });
          },
          error: function (xhr, status, err) {
            console.error("Error fetching kunjungan marketing pelanggan:", err);
          },
        });
      }

      // Event handler untuk Subgrup Barang
      $subgroupId.on("change", function () {
        var subgroupId = $(this).val();
        loadClasses(null, subgroupId);
        var filters = getPelangganFilterValues();
        data_kunjungan_marketing_plg(
          filters.subgrup_barang,
          filters.kelas_barang
        );
      });

      // Event handler untuk Class Barang
      $classId.on("change", function () {
        var filters = getPelangganFilterValues();
        data_kunjungan_marketing_plg(
          filters.subgrup_barang,
          filters.kelas_barang
        );
      });

      // Inisialisasi awal
      loadSubgroups(null);
      loadClasses(null, null);
      var filters = getPelangganFilterValues();
      data_kunjungan_marketing_plg(
        filters.subgrup_barang,
        filters.kelas_barang
      );
    }

    // Logika untuk Tab User
    function initializeTabUser() {
      const $cabang = $("#cabangkunjmarketinguser");
      const $sales = $("#salesMarketing");
      const $subgroupId = $("#subgrupBarang1");
      const $classId = $("#kelasBarang1");

      // Inisialisasi cabang
      initializeCabang($cabang, branchId, branchName);

      // Inisialisasi Select2
      $subgroupId.select2({
        placeholder: "SEMUA SUBGRUP",
        allowClear: true,
        closeOnSelect: false,
      });

      $classId.select2({
        placeholder: "SEMUA KELAS",
        allowClear: true,
        closeOnSelect: false,
      });

      // Fungsi untuk mendapatkan nilai filter user
      function getUserFilterValues() {
        return {
          cabang: $cabang.val(),
          salesMarketing: $sales.val(),
          subgrup_barang: $subgroupId.val(),
          kelas_barang: $classId.val(),
        };
      }

      // Fungsi untuk memuat subgroup
      function loadSubgroups(groupId) {
        $.ajax({
          url: url + "master/filter/subgrup",
          method: "POST",
          data: { group_prod: "02" },
          success: function (data) {
            $subgroupId.html(data).val(null).trigger("change");
          },
          error: function (xhr, status, error) {
            console.error("Error fetching subgroup data:", error);
            $subgroupId.html('<option value="">Gagal memuat subgroup</option>');
          },
        });
      }

      // Fungsi untuk memuat class
      function loadClasses(groupId, subgroupId) {
        $.ajax({
          url: url + "master/filter/kelas",
          method: "POST",
          data: {
            group_prod: "02",
            subgroup_prod: subgroupId,
          },
          success: function (data) {
            $classId.html(data).val(null).trigger("change");
          },
          error: function (xhr, status, error) {
            console.error("Error fetching class data:", error);
            $classId.html('<option value="">Gagal memuat kelas</option>');
          },
        });
      }

      // Event handler untuk cabang
      $cabang.on("change", function () {
        const cab = this.value;
        $sales.empty().append('<option value="">Pilih Marketing</option>');
        if (cab) {
          $.post(
            url + "master/salesmarketing",
            { branch_id: cab },
            (salesList) => {
              salesList.forEach((s) =>
                $sales.append(`<option value="${s.nik}">${s.name}</option>`)
              );
            },
            "json"
          );
        }
        var filters = getUserFilterValues();
        data_kunjungan_marketing_user(
          filters.cabang,
          filters.salesMarketing,
          filters.subgrup_barang,
          filters.kelas_barang
        );
      });

      // Event handler untuk sales/marketing
      $sales.on("change", function () {
        var filters = getUserFilterValues();
        data_kunjungan_marketing_user(
          filters.cabang,
          filters.salesMarketing,
          filters.subgrup_barang,
          filters.kelas_barang
        );
      });

      // Event handler untuk Subgrup Barang
      $subgroupId.on("change", function () {
        var subgroupId = $(this).val();
        loadClasses(null, subgroupId);
        var filters = getUserFilterValues();
        data_kunjungan_marketing_user(
          filters.cabang,
          filters.salesMarketing,
          filters.subgrup_barang,
          filters.kelas_barang
        );
      });

      // Event handler untuk Class Barang
      $classId.on("change", function () {
        var filters = getUserFilterValues();
        data_kunjungan_marketing_user(
          filters.cabang,
          filters.salesMarketing,
          filters.subgrup_barang,
          filters.kelas_barang
        );
      });

      // Inisialisasi awal
      loadSubgroups(null);
      loadClasses(null, null);
      var filters = getUserFilterValues();
      data_kunjungan_marketing_user(
        filters.cabang,
        filters.salesMarketing,
        filters.subgrup_barang,
        filters.kelas_barang
      );

      // Fungsi untuk menampilkan tabel kunjungan marketing pelanggan
      function data_kunjungan_marketing_user(
        cabang,
        salesMarketing,
        subgrup_barang,
        kelas_barang
      ) {
        $.ajax({
          type: "POST",
          url: url + "pelaporan/kunjungan_marketing/data_kunj_marketing_user",
          async: true,
          data: {
            cabang: cabang,
            sales_marketing: salesMarketing,
            subgrp_prod: subgrup_barang,
            klsgrp_prod: kelas_barang,
          },
          dataType: "json",
          success: function (data) {
            var table = new Tabulator("#table_kunj_marketing_user", {
              data: data,
              frozenColumns: true,
              // layout: "fitColumns",
              // movableColumns: true,
              height: "500px",
              // responsiveLayout: "collapse",
              pagination: "local",
              paginationSize: 50,
              paginationSizeSelector: [20, 50, 70],
              columns: [
                {
                  title: "Nama User",
                  field: "cust_user_name",
                  headerHozAlign: "center",
                  width: 250,
                },
                {
                  title: "Pelanggan",
                  field: "cust_name",
                  headerHozAlign: "center",
                  width: 250,
                },
                {
                  title: "Jabatan",
                  field: "user_cat_name",
                  headerHozAlign: "center",
                  width: 250,
                },
                {
                  title: "Jan",
                  field: "tot_jan",
                  headerHozAlign: "center",
                  hozAlign: "center",
                  frozen: true,
                },
                {
                  title: "Feb",
                  field: "tot_feb",
                  headerHozAlign: "center",
                  hozAlign: "center",
                },
                {
                  title: "Mar",
                  field: "tot_mar",
                  headerHozAlign: "center",
                  hozAlign: "center",
                },
                {
                  title: "Apr",
                  field: "tot_apr",
                  headerHozAlign: "center",
                  hozAlign: "center",
                },
                {
                  title: "Mei",
                  field: "tot_mei",
                  headerHozAlign: "center",
                  hozAlign: "center",
                },
                {
                  title: "Jun",
                  field: "tot_jun",
                  headerHozAlign: "center",
                  hozAlign: "center",
                },
                {
                  title: "Jul",
                  field: "tot_jul",
                  headerHozAlign: "center",
                  hozAlign: "center",
                },
                {
                  title: "Agt",
                  field: "tot_agt",
                  headerHozAlign: "center",
                  hozAlign: "center",
                },
                {
                  title: "Sep",
                  field: "tot_sep",
                  headerHozAlign: "center",
                  hozAlign: "center",
                },
                {
                  title: "Okt",
                  field: "tot_okt",
                  headerHozAlign: "center",
                  hozAlign: "center",
                },
                {
                  title: "Nov",
                  field: "tot_nov",
                  headerHozAlign: "center",
                  hozAlign: "center",
                },
                {
                  title: "Des",
                  field: "tot_des",
                  headerHozAlign: "center",
                  hozAlign: "center",
                },
                {
                  title: "Total",
                  field: "tot_visit",
                  headerHozAlign: "center",
                  hozAlign: "center",
                },
              ],
            });
          },
          error: function (xhr, status, err) {
            console.error("Error fetching kunjungan marketing user:", err);
          },
        });
      }
    }

    // Logika untuk Tab Kategori User
    function initializeTabUserCat() {
      const $cabang = $("#cabangkunjmarketinguser");
      const $subgroupId = $("#subgrupBarang2");
      const $classId = $("#kelasBarang2");

      // Inisialisasi cabang
      initializeCabang($cabang, branchId, branchName);

      // Inisialisasi Select2
      $subgroupId.select2({
        placeholder: "SEMUA SUBGRUP",
        allowClear: true,
        closeOnSelect: false,
      });

      $classId.select2({
        placeholder: "SEMUA KELAS",
        allowClear: true,
        closeOnSelect: false,
      });

      // Fungsi untuk mendapatkan nilai filter user
      function getUserCatFilterValues() {
        return {
          cabang: $cabang.val(),
          subgrup_barang: $subgroupId.val(),
          kelas_barang: $classId.val(),
        };
      }

      // Fungsi untuk memuat subgroup
      function loadSubgroups(groupId) {
        $.ajax({
          url: url + "master/filter/subgrup",
          method: "POST",
          data: { group_prod: "02" },
          success: function (data) {
            $subgroupId.html(data).val(null).trigger("change");
          },
          error: function (xhr, status, error) {
            console.error("Error fetching subgroup data:", error);
            $subgroupId.html('<option value="">Gagal memuat subgroup</option>');
          },
        });
      }

      // Fungsi untuk memuat class
      function loadClasses(groupId, subgroupId) {
        $.ajax({
          url: url + "master/filter/kelas",
          method: "POST",
          data: {
            group_prod: "02",
            subgroup_prod: subgroupId,
          },
          success: function (data) {
            $classId.html(data).val(null).trigger("change");
          },
          error: function (xhr, status, error) {
            console.error("Error fetching class data:", error);
            $classId.html('<option value="">Gagal memuat kelas</option>');
          },
        });
      }

      // Event handler untuk cabang
      $cabang.on("change", function () {
        var filters = getUserCatFilterValues();
        data_kunjungan_marketing_user_cat(
          filters.cabang,
          filters.subgrup_barang,
          filters.kelas_barang
        );
      });

      // Event handler untuk Subgrup Barang
      $subgroupId.on("change", function () {
        var subgroupId = $(this).val();
        loadClasses(null, subgroupId);
        var filters = getUserCatFilterValues();
        data_kunjungan_marketing_user_cat(
          filters.cabang,
          filters.subgrup_barang,
          filters.kelas_barang
        );
      });

      // Event handler untuk Class Barang
      $classId.on("change", function () {
        var filters = getUserCatFilterValues();
        data_kunjungan_marketing_user_cat(
          filters.cabang,
          filters.subgrup_barang,
          filters.kelas_barang
        );
      });

      // Inisialisasi awal
      loadSubgroups(null);
      loadClasses(null, null);
      var filters = getUserCatFilterValues();
      data_kunjungan_marketing_user_cat(
        filters.cabang,
        filters.subgrup_barang,
        filters.kelas_barang
      );

      // Fungsi untuk menampilkan tabel kunjungan marketing pelanggan
      function data_kunjungan_marketing_user_cat(
        cabang,
        subgrup_barang,
        kelas_barang
      ) {
        $.ajax({
          type: "POST",
          url:
            url + "pelaporan/kunjungan_marketing/data_kunj_marketing_user_cat",
          async: true,
          data: {
            cabang: cabang,
            subgrp_prod: subgrup_barang,
            klsgrp_prod: kelas_barang,
          },
          dataType: "json",
          success: function (data) {
            var table = new Tabulator("#table_kunj_marketing_user_cat", {
              data: data,
              layout: "fitColumns",
              movableColumns: true,
              height: "500px",
              responsiveLayout: "collapse",
              pagination: "local",
              paginationSize: 50,
              paginationSizeSelector: [20, 50, 70],
              columns: [
                {
                  title: "Nama",
                  field: "emp_name",
                  headerHozAlign: "center",
                },
                {
                  title: "Manajemen",
                  field: "tot_mnj",
                  headerHozAlign: "center",
                  hozAlign: "center",
                },
                {
                  title: "Medis",
                  field: "tot_mds",
                  headerHozAlign: "center",
                  hozAlign: "center",
                },
                {
                  title: "Non Medis",
                  field: "tot_non_mds",
                  headerHozAlign: "center",
                  hozAlign: "center",
                },
                {
                  title: "Dll",
                  field: "tot_dll",
                  headerHozAlign: "center",
                  hozAlign: "center",
                },
                {
                  title: "Total",
                  field: "tot_visit",
                  headerHozAlign: "center",
                  hozAlign: "center",
                },
              ],
            });
          },
          error: function (xhr, status, err) {
            console.error("Error fetching kunjungan marketing user cat:", err);
          },
        });
      }
    }

    // Aktivasi tab default dan inisialisasi berdasarkan tab yang aktif
    $("#mon_penggunaan").on("shown.bs.tab", function () {
      initializeTabPenggunaan();
    });

    $("#mon_pelanggan").on("shown.bs.tab", function () {
      initializeTabPelanggan();
    });

    $("#mon_user").on("shown.bs.tab", function () {
      initializeTabUser();
    });

    $("#mon_user_cat").on("shown.bs.tab", function () {
      initializeTabUserCat();
    });

    // Inisialisasi tab default (misalnya Penggunaan)
    $("#mon_penggunaan").tab("show"); // Aktifkan tab Penggunaan secara default
  }
});
