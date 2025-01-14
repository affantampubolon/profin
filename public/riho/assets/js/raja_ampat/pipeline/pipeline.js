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
  } else if (window.location.pathname === "/pipeline/formulir") {
    // var url = window.location.origin + "/";

    var grp_prod = $("#grupBarang").val();
    var subgrp_prod = $("#subgrupBarang").val();
    var kls_prod = $("#kelasBarang").val();

    // Update Option change grup barang
    $("#grupBarang").change(function () {
      var grp_prod = $(this).val();
      // AJAX request --
      $.ajax({
        url: url + "pipeline/getSubGrupBarang",
        method: "POST",
        data: {
          grp_prod: grp_prod,
        },
        success: function (data) {
          // Add options
          $("#subgrupBarang").html(data);
          var subgrp_prod = $("#subgrupBarang").val();

          // // empty options
          // $("#kelasBarang").select2({
          //   placeholder: "Pilih Kelas",
          //   allowClear: true,
          // });

          $("#kelasBarang").html(
            '<option selected value="">Pilih Kelas Barang</option>'
          );

          $("#kelasBarang").empty();

          // panggil function
          console.log("grup barang adalah = " + grp_prod);
        },
      });
    });

    // Update Option change subgrup barang
    $("#subgrupBarang").change(function () {
      var grp_prod = $("#grupBarang").val();
      var subgrp_prod = $(this).val();
      // AJAX request --
      $.ajax({
        url: url + "pipeline/getKelasBarang",
        method: "POST",
        data: {
          grp_prod: grp_prod,
          subgrp_prod: subgrp_prod,
        },
        success: function (data) {
          // Add options
          $("#kelasBarang").html(data);
          var kls_prod = $("#kelasBarang").val();

          // panggil function
          console.log(
            "grup barang = " +
              grp_prod +
              "subgrup barang adalah = " +
              subgrp_prod
          );
        },

        // error: function (xhr, status, error) {
        //   console.error("Error:", error);
        //   alert(
        //     "Terjadi kesalahan saat memuat Sub Grup Barang: " + xhr.responseText
        //   );
        // },
      });
    });

    // Update Option change subgrup barang
    $("#kelasBarang").change(function () {
      var grp_prod = $("#grupBarang").val();
      var subgrp_prod = $("#subgrupBarang").val();
      var kls_prod = $(this).val();

      // panggil function
      console.log(
        "grup barang = " +
          grp_prod +
          "subgrup barang = " +
          subgrp_prod +
          "kelas barang" +
          kls_prod
      );
    });

    // Nama bulan dalam bahasa Indonesia
    const bulanIndonesia = [
      "01 - Januari",
      "02 - Februari",
      "03 - Maret",
      "04 - April",
      "05 - Mei",
      "06 - Juni",
      "07 - Juli",
      "08 - Agustus",
      "09 - September",
      "10 - Oktober",
      "11 - November",
      "12 - Desember",
    ];

    // Bulan saat ini (01, 02, ..., 12)
    const bulanSekarang = new Date().getMonth(); // Bulan saat ini (0-based index)

    // Opsi bulan yang akan ditampilkan
    let options = "";

    // Tambahkan bulan berjalan sebagai default
    options += `<option value="${(bulanSekarang + 1)
      .toString()
      .padStart(2, "0")}" selected>${bulanIndonesia[bulanSekarang]}</option>`;

    // Tambahkan bulan lainnya
    for (let i = 0; i < 12; i++) {
      if (i !== bulanSekarang) {
        options += `<option value="${(i + 1).toString().padStart(2, "0")}">${
          bulanIndonesia[i]
        }</option>`;
      }
    }

    // Inject opsi ke dalam dropdown
    $("#bulanPipeline").html(options);

    // Inisialisasi Select2
    $("#bulanPipeline").select2({
      placeholder: "Pilih Bulan",
      allowClear: true,
    });

    // Event listener untuk menangkap perubahan pilihan
    $("#bulanPipeline").on("change", function () {
      const selectedValue = $(this).val(); // Mengambil value yang dipilih
      const selectedText = $("#bulanPipeline option:selected").text(); // Mengambil teks yang dipilih
      console.log(`Value: ${selectedValue}, Text: ${selectedText}`);
    });

    // Fetch data pelanggan
    $.ajax({
      url: url + "pipeline/getMstPelanggan",
      method: "GET",
      dataType: "json",
      success: function (data) {
        // Reset options di dropdown
        $("#masterpelanggan").empty();

        // Tambahkan opsi default
        $("#masterpelanggan").append(
          '<option value="" selected>Pilih Pelanggan</option>'
        );

        // Tambahkan opsi pelanggan berdasarkan hasil dari backend
        data.forEach((pelanggan) => {
          $("#masterpelanggan").append(
            `<option value="${pelanggan.cust_id}">
                        ${pelanggan.cust_id} - ${pelanggan.cust_name}
                    </option>`
          );
        });

        // Inisialisasi Select2 untuk dropdown
        $("#masterpelanggan").select2({
          placeholder: "Pilih Pelanggan",
          allowClear: true,
        });
      },
      error: function (xhr, status, error) {
        console.error("Error fetching data pelanggan:", error);
        alert("Gagal memuat data pelanggan.");
      },
    });

    // Event listener untuk menangkap perubahan pilihan
    $("#masterpelanggan").on("change", function () {
      const selectedValue = $(this).val(); // Mengambil value yang dipilih (cust_id)
      const selectedText = $("#masterpelanggan option:selected").text(); // Mengambil teks yang dipilih
      console.log(`Value: ${selectedValue}, Text: ${selectedText}`);
    });

    // PENYIMPANAN SEMENTARA DETAIL PIPELINE
    let pipelineTable = new Tabulator("#tabel_detail_pipeline", {
      height: "400px",
      layout: "fitColumns",
      placeholder: "Belum ada data detail pipeline.",
      columns: [
        { title: "Pelanggan", field: "cust_id", width: 200 },
        { title: "Target Call", field: "target_call", hozAlign: "center" },
        {
          title: "Target Effective Call",
          field: "target_ec",
          hozAlign: "center",
        },
        { title: "Target Nilai", field: "target_value", hozAlign: "center" },
        {
          title: "Target Probabilitas",
          field: "probability",
          hozAlign: "center",
        },
        {
          title: "Aksi",
          hozAlign: "center",
          formatter: "buttonCross",
          cellClick: function (e, cell) {
            cell.getRow().delete();
          },
        },
      ],
    });

    // Load data dari server
    fetch("/pipeline/getTemp?nik=default_nik") // Sesuaikan nik
      .then((res) => res.json())
      .then((data) => {
        if (data.status === "success") {
          pipelineTable.setData(data.data);
        } else {
          alert("Gagal memuat data dari server: " + data.message);
        }
      })
      .catch((error) => {
        console.error("Error fetching data:", error);
      });

    document
      .querySelector("#tambahDataDetPipeline")
      .addEventListener("click", function () {
        let pelanggan = document.querySelector("#masterpelanggan").value;
        let targetCall = document.querySelector("#targetCall").value;
        let targetEfCall = document.querySelector("#targetEfCall").value;
        let targetNilai = document.querySelector("#targetNilai").value;
        let targetProbabilitas = document.querySelector(
          "#targetProbabilitas"
        ).value;

        // Validasi data sebelum ditambahkan
        if (!pelanggan || !targetCall || !targetEfCall || !targetNilai) {
          alert("Semua field harus diisi!");
          return;
        }

        fetch("/pipeline/saveTemp", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            cust_id: pelanggan,
            target_call: targetCall,
            target_ec: targetEfCall,
            target_value: targetNilai,
            probability: targetProbabilitas,
          }),
        })
          .then((res) => res.json())
          .then((data) => {
            if (data.status === "success") {
              alert(data.message);
              pipelineTable.addData([
                {
                  cust_id: pelanggan,
                  target_call: targetCall,
                  target_ec: targetEfCall,
                  target_value: targetNilai,
                  probability: `${targetProbabilitas}`,
                },
              ]);
            } else {
              alert("Gagal menyimpan data: " + data.message);
            }
          })
          .catch((error) => {
            console.error("Error saving data:", error);
            alert("Terjadi kesalahan saat menyimpan data.");
          });
      });

    // MENYIMPAN FORM DATA PENGISIAN PIPELINE
    document.querySelector("form").addEventListener("submit", function (e) {
      e.preventDefault(); // Hentikan form submission

      Swal.fire({
        title: "Apakah Anda yakin?",
        text: "Data akan disimpan ke database.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, simpan!",
        cancelButtonText: "Batal",
      }).then((result) => {
        if (result.isConfirmed) {
          // Kirim data form ke server
          fetch("/pipeline/insertForm", {
            method: "POST",
            body: new FormData(e.target),
          })
            .then((response) => response.json())
            .then((data) => {
              if (data.status === "success") {
                Swal.fire("Berhasil!", data.message, "success").then(() => {
                  window.location.href = "/pipeline/pembuatan";
                });
              } else {
                Swal.fire("Gagal!", data.message, "error");
              }
            })
            .catch((error) => {
              Swal.fire(
                "Error!",
                "Terjadi kesalahan saat menyimpan data.",
                "error"
              );
            });
        }
      });
    });
  }
});
