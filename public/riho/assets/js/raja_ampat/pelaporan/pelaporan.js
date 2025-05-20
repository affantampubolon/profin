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

      // Tambahkan marker baru dengan warna berdasarkan flg_noo
      aktivitasData.forEach((loc) => {
        const isNoo = loc.flg_noo === true || loc.flg_noo === "true";
        const color = isNoo ? "#129990" : "#27548A";

        const marker = L.circleMarker([loc.latitude, loc.longitude], {
          radius: 6,
          fillColor: color,
          color: color,
          weight: 1,
          fillOpacity: 1,
        })
          .addTo(map)
          .bindPopup(`<b>Pelanggan:</b> ${loc.cust_name}`);

        // Event hover untuk menampilkan popup
        marker.on("mouseover", function () {
          this.openPopup();
        });
        marker.on("mouseout", function () {
          this.closePopup();
        });

        // Event click untuk menampilkan tabel
        marker.on("click", function () {
          const date = loc.date;
          const branch_id = loc.branch_id;
          const nik = loc.nik;
          const cust_id = loc.cust_id;
          data_ditribusi_prod(date, branch_id, nik, cust_id);
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
  }
});
