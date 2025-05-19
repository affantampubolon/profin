$(document).ready(function () {
    if (window.location.pathname === "/pelaporan/aktivitas_kunj") {
        const MAP_ID = "map";   // pastikan <div id="map"> ada di view
      
        let aktivitasData = [];
        let map;
      
        // Panggil data dan render saat branch/sales berubah
        const $cabang = $("#cabangaktivitaskunj");
        const $sales  = $("#salesMarketing");
      
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
      
        // muat sekali di awal
        data_aktivitas_sales($cabang.val(), $sales.val());
      
        // init map – height diset di sini
        // Inisialisasi peta sekali saja
function initMap() {
    const el = document.getElementById(MAP_ID);
    el.style.height = "350px";
    el.style.margin = "10px";
  
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
      // Pilih warna berdasarkan flg_noo
      const isNoo = loc.flg_noo === true || loc.flg_noo === "true";
      const color = isNoo ? "#129990" : "#27548A";
  
      L.circleMarker([loc.latitude, loc.longitude], {
        radius: 6,            // ukuran titik
        fillColor: color,     // warna isian
        color: color,         // warna garis tepi
        weight: 1,            // ketebalan garis tepi
        fillOpacity: 1,       // opacity isian
      })
      .addTo(map)
      .bindPopup(`<b>Pelanggan:</b> ${loc.cust_name}`);
    });
  }
  
  // Ambil data & update peta
  function data_aktivitas_sales(cabang, salesMarketing) {
    return $.ajax({
      type: "POST",
      url: url + "pelaporan/aktivitas_kunj/data_aktivitas",
      data: { cabang, sales_marketing: salesMarketing },
      dataType: "json",
      success(data) {
        aktivitasData = data;
        renderMap();
      },
      error(xhr, status, err) {
        console.error("Error fetching aktivitas:", err);
      },
    });
  }
      }
      
      
});
  