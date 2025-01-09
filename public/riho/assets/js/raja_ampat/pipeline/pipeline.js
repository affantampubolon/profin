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
    const fileInput = document.getElementById("fileInput");
    const progressBarModal = document.getElementById("progressBarModal");
    const uploadStatusModal = document.getElementById("uploadStatusModal");
    const finishButtonModal = document.getElementById("finishButtonModal");
    const uploadModal = new bootstrap.Modal(
      document.getElementById("uploadModal")
    );

    if (
      uploadForm &&
      fileInput &&
      progressBarModal &&
      uploadStatusModal &&
      finishButtonModal
    ) {
      uploadForm.addEventListener("submit", async function (e) {
        e.preventDefault(); // Prevent page reload
        const formData = new FormData(this); // Gather form data

        try {
          // Reset modal content
          uploadStatusModal.innerHTML = "<span>Memulai proses unggah...</span>";
          progressBarModal.style.width = "0%";
          progressBarModal.setAttribute("aria-valuenow", 0);
          finishButtonModal.style.display = "none";

          // Show modal
          uploadModal.show();

          // Simulate progress bar (for demo purposes)
          let progress = 0;
          const progressInterval = setInterval(() => {
            progress += 10;
            if (progress <= 90) {
              progressBarModal.style.width = `${progress}%`;
              progressBarModal.setAttribute("aria-valuenow", progress);
            }
          }, 300);

          // Send file with Fetch API
          const response = await fetch("/pipeline/upload", {
            method: "POST",
            body: formData,
          });

          // Parse response JSON
          const result = await response.json();

          // Stop progress bar simulation
          clearInterval(progressInterval);
          progressBarModal.style.width = "100%";
          progressBarModal.setAttribute("aria-valuenow", 100);

          // Display result
          if (result.status === "success") {
            uploadStatusModal.innerHTML = `<span class="text-success">${result.message}</span>`;
            finishButtonModal.style.display = "block";
          } else {
            uploadStatusModal.innerHTML = `<span class="text-danger">${result.message}</span>`;
          }
        } catch (error) {
          // Handle errors
          uploadStatusModal.innerHTML = `<span class="text-danger">Terjadi kesalahan saat unggah.</span>`;
        }

        // Add event listener to "Selesai" button
        finishButtonModal.addEventListener("click", () => {
          uploadModal.hide(); // Hide modal
          fileInput.value = ""; // Clear input file
        });
      });

      // Reset input file when modal is closed
      document
        .getElementById("uploadModal")
        .addEventListener("hidden.bs.modal", () => {
          fileInput.value = ""; // Clear input file
        });
    }
  }
});
