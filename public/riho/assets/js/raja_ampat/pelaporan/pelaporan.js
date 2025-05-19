$(document).ready(function () {
  if (window.location.pathname === "/pelaporan/aktivitas_kunj") {
    const MAP_ID = "map"; // Pastikan <div id="map"> ada di view

    let aktivitasData = [];
    let map;

    // Panggil data dan render saat branch/sales berubah
    const $cabang = $("#cabangaktivitaskunj");
    const $sales = $("#salesMarketing");

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
      data_aktivitas_sales(cab, $sales.val());
    });

    $sales.on("change", function () {
      data_aktivitas_sales($cabang.val(), this.value);
    });

    // Muat sekali di awal
    data_aktivitas_sales($cabang.val(), $sales.val());

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
          const branch_id = loc.branch_id;
          const nik = loc.nik;
          const cust_id = loc.cust_id;
          data_ditribusi_prod(branch_id, nik, cust_id);
        });
      });
    }

    // Ambil data & update peta
    function data_aktivitas_sales(cabang, salesMarketing) {
      return $.ajax({
        type: "POST",
        url: url + "pelaporan/aktivitas_kunj/data_aktivitas",
        data: { cabang, sales_marketing: salesMarketing },
        dataType: "json",
        success: function (data) {
          aktivitasData = data;
          renderMap();
        },
        error: function (xhr, status, err) {
          console.error("Error fetching aktivitas:", err);
        },
      });
    }

    // Fungsi untuk mengubah data flat menjadi nested tree structure
    function buildTreeData(data) {
      const tree = [];
      const monthMap = new Map();
      const dateMap = new Map();
      const nikMap = new Map();
      const custMap = new Map();

      data.forEach((item) => {
        const monthKey = item.month;
        const dateKey = `${monthKey}-${item.date}`;
        const nikKey = `${dateKey}-${item.nik}`;
        const custKey = `${nikKey}-${item.cust_id}`;

        // Level 1: Month
        if (!monthMap.has(monthKey)) {
          const monthNode = { month: item.month, _children: [] };
          monthMap.set(monthKey, monthNode);
          tree.push(monthNode);
        }
        const monthNode = monthMap.get(monthKey);

        // Level 2: Date
        if (!dateMap.has(dateKey)) {
          const dateNode = { date: item.date, _children: [] };
          dateMap.set(dateKey, dateNode);
          monthNode._children.push(dateNode);
        }
        const dateNode = dateMap.get(dateKey);

        // Level 3: NIK (Sales/Marketing)
        if (!nikMap.has(nikKey)) {
          const nikNode = {
            nik: item.nik,
            emp_name: item.emp_name,
            _children: [],
          };
          nikMap.set(nikKey, nikNode);
          dateNode._children.push(nikNode);
        }
        const nikNode = nikMap.get(nikKey);

        // Level 4: Cust ID (Pelanggan)
        if (!custMap.has(custKey)) {
          const custNode = {
            cust_id: item.cust_id,
            cust_name: item.cust_name,
            _children: [],
          };
          custMap.set(custKey, custNode);
          nikNode._children.push(custNode);
        }
        const custNode = custMap.get(custKey);

        // Detail data sebagai children dari cust_id
        const detail = {
          class_id: item.class_id,
          class_name: item.class_name,
          tot_real_value: item.tot_real_value,
          tot_target_value: item.tot_target_value,
          prs_value: item.prs_value,
          flg_non_route: item.flg_non_route,
          flg_visit: item.flg_visit,
          group_id: item.group_id,
          subgroup_id: item.subgroup_id,
        };
        custNode._children.push(detail);
      });

      return tree;
    }

    // Fungsi untuk menampilkan tabel distribusi produk dalam tree structure
    function data_ditribusi_prod(branch_id, nik, cust_id) {
      $.ajax({
        type: "POST",
        url: url + "pelaporan/aktivitas_kunj/data_distribusi_prod",
        async: true,
        data: {
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

          // Transformasi data flat ke nested tree structure
          var treeData = buildTreeData(filteredData);

          // Inisialisasi Tabulator dengan tree structure
          var table = new Tabulator("#table_distribusi_prod", {
            data: treeData,
            dataTree: true, // Aktifkan Tree Structure
            dataTreeStartExpanded: true, // Expand tree di awal
            movableColumns: true,
            layout: "fitColumns",
            height: "500px",
            responsiveLayout: "collapse",
            pagination: "local",
            paginationSize: 50,
            paginationSizeSelector: [20, 50, 75],
            columns: [
              {
                title: "Bulan",
                field: "month",
                width: 100,
                headerHozAlign: "center",
                hozAlign: "center",
              },
              {
                title: "Tanggal",
                field: "date",
                width: 120,
                headerHozAlign: "center",
                hozAlign: "center",
              },
              {
                title: "Sales / Marketing",
                field: "emp_name",
                width: 150,
                headerHozAlign: "center",
                formatter: function (cell) {
                  var rowData = cell.getRow().getData();
                  return rowData.nik
                    ? rowData.nik + " - " + rowData.emp_name
                    : "";
                },
              },
              {
                title: "Pelanggan",
                field: "cust_name",
                width: 150,
                headerHozAlign: "center",
                formatter: function (cell) {
                  var rowData = cell.getRow().getData();
                  return rowData.cust_id
                    ? rowData.cust_id + " - " + rowData.cust_name
                    : "";
                },
              },
              {
                title: "Kelas Barang",
                field: "class_name",
                width: 150,
                headerHozAlign: "center",
                formatter: function (cell) {
                  var rowData = cell.getRow().getData();
                  return rowData.class_id
                    ? rowData.group_id +
                        "-" +
                        rowData.subgroup_id +
                        "-" +
                        rowData.class_id +
                        " " +
                        rowData.class_name
                    : "";
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
