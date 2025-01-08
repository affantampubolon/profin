$(document).ready(function () {
  if (window.location.pathname == "/pipeline/pembuatan") {
    // Data pelanggan
    const customerNames = [
      "Toko Haji Tongkang",
      "T.B Bangun Bersama",
      "Apotek Sehat Sentosa",
      "RS. Umrah Depok",
      "Labkesda Kabupaten Pandeglang",
      "Toko Abu Abi",
      "Buaya Swalayan",
      "Apotek Bugar Sejahtera",
      "Rumah Sakit Umum Daerah Dr. Karya",
      "Labkesda Kabupaten Rokan Hilir",
      "Toko Buku Bobo",
      "Kios Baju Wanita",
      "Toko Obat Ong Sang Ling Long",
      "IHC Pelindo Banjarmasin",
      "Laboratorium Kota Subulussalam",
    ];

    // Skala probabilitas
    const probabilityScales = [10, 20, 30, 40, 50, 60, 70, 80, 90, 100];

    // Data dummy
    const dummyData = customerNames.map((name) => ({
      cust_name: name,
      target_call: 10,
      target_ec: 5,
      probability: 50,
    }));

    // Inisialisasi tabel Tabulator
    var table = new Tabulator("#tabel_pembuatan_pipeline", {
      layout: "fitColumns",
      columns: [
        {
          title: "Pelanggan",
          field: "cust_name",
          editor: "list",
          editorParams: {
            values: customerNames,
            autocomplete: true,
            allowEmpty: false,
            listOnEmpty: true,
            valuesLookup: true,
          },
        },
        {
          title: "Target Call",
          field: "target_call",
          hozAlign: "center",
          editor: "number",
        },
        {
          title: "Target EC",
          field: "target_ec",
          hozAlign: "center",
          editor: "number",
        },
        {
          title: "Probabilitas",
          field: "probability",
          editor: "list",
          editorParams: {
            values: probabilityScales,
            autocomplete: true,
            allowEmpty: false,
            listOnEmpty: true,
            valuesLookup: true,
          },
        },
      ],
      data: dummyData,
    });

    // Form Upload
    const uploadForm = document.getElementById("uploadForm");
    const statusDiv = document.getElementById("uploadStatus");

    if (uploadForm && statusDiv) {
      uploadForm.addEventListener("submit", async function (e) {
        e.preventDefault(); // Mencegah reload halaman
        const formData = new FormData(this); // Ambil data form

        try {
          // Tampilkan status awal
          statusDiv.innerText = "Uploading...";
          const response = await fetch("/pipeline/upload", {
            method: "POST",
            body: formData,
          });

          // Parse response JSON
          const result = await response.json();

          // Update status berdasarkan hasil upload
          if (result.status === "success") {
            statusDiv.innerText = `Success: ${result.message}`;
          } else {
            statusDiv.innerText = `Error: ${result.message}`;
          }
        } catch (error) {
          statusDiv.innerText = "Error: Failed to upload file.";
        }
      });
    }
  }
});
