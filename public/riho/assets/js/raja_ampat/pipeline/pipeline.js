$(document).ready(function () {
  if (window.location.pathname == "/pipeline/pembuatan") {
    //mendapatkan nilai
    var tahun = $("#tahunPipelineDet").val();
    var bulan = $("#bulanPipelineDet").val();

    //data draft pipeline
    tabel_draft_pipeline(tahun, bulan);

    //FUNGSI DROPDOWN BULAN
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
    $("#bulanPipelineDet").html(options);

    // Inisialisasi Select2
    $("#bulanPipelineDet").select2({
      placeholder: "Pilih Bulan",
      allowClear: true,
    });

    // Event listener untuk menangkap perubahan pilihan
    $("#bulanPipelineDet").on("change", function () {
      const selectedValue = $(this).val(); // Mengambil value yang dipilih
      const selectedText = $("#bulanPipelineDet option:selected").text(); // Mengambil teks yang dipilih
      console.log(`Value: ${selectedValue}, Text: ${selectedText}`);
    });

    //PERUBAHAN FILTER DROPDOWN
    //Tahun
    $("#tahunPipelineDet").change(function () {
      var tahun = $(this).val();
      var bulan = $("#bulanPipelineDet").val();

      tabel_draft_pipeline(tahun, bulan);
    });

    //Bulan
    $("#bulanPipelineDet").change(function () {
      var tahun = $("#tahunPipelineDet").val();
      var bulan = $(this).val();

      tabel_draft_pipeline(tahun, bulan);
    });

    // Inisialisasi tabel Draft Pipeline
    function tabel_draft_pipeline(tahun, bulan) {
      $.ajax({
        type: "GET",
        url: url + "/pipeline/groupuser", // Ambil group_id dari server
        dataType: "json",
        success: function (response) {
          let group_id = response.group_id;
          let columns = [
            {
              title: "Nama Kelas Barang",
              field: "class_name",
              headerHozAlign: "center",
            },
            {
              title: "Pelanggan",
              field: "cust_id",
              headerHozAlign: "center",
              formatter: function (cell, formatterParams, onRendered) {
                var rowData = cell.getRow().getData();
                return rowData.cust_id + " - " + rowData.cust_name;
              },
            },
            {
              title: "Target Nilai (Rp)",
              field: "target_value",
              headerHozAlign: "center",
              hozAlign: "right",
              formatter: "money",
              formatterParams: {
                decimal: ",",
                thousand: ".",
              },
              editor: "input",
              cellEdited: function (cell) {
                updateDraftPipeline(cell.getRow().getData());
              },
              cssClass: "highlight-column",
            },
          ];

          // Tambahkan kolom "Frekuensi Kunjungan" jika group_id = "01" atau "03"
          if (group_id === "01" || group_id === "03") {
            columns.push({
              title: "Frekuensi Kunjungan",
              field: "freq_visit",
              headerHozAlign: "center",
              hozAlign: "center",
              editor: "input",
              cellEdited: function (cell) {
                updateDraftPipeline(cell.getRow().getData());
              },
              cssClass: "highlight-column",
            });
          }

          // Tambahkan kolom "Probabilitas" jika group_id = "02" atau "05"
          if (group_id === "02" || group_id === "05") {
            columns.push({
              title: "Probabilitas (%)",
              field: "probability",
              headerHozAlign: "center",
              hozAlign: "center",
              editor: "list",
              editorParams: {
                valuesURL: url + "/master/probabilitas", // Ambil data dari API
                placeholderLoading: "Menunggu Data...",
                itemFormatter: function (label, value, item, element) {
                  return (
                    "<strong>" + value + " % " + " - " + label + "</strong>"
                  );
                },
              },
              cellEdited: function (cell) {
                updateDraftPipeline(cell.getRow().getData());
              },
              cssClass: "highlight-column",
            });
          }

          // Tambahkan kolom aksi
          // Tambahkan kolom aksi dengan SweetAlert konfirmasi
          columns.push({
            title: "Aksi",
            hozAlign: "center",
            headerHozAlign: "center",
            formatter: "buttonCross",
            cellClick: function (e, cell) {
              // Tampilkan SweetAlert konfirmasi
              Swal.fire({
                title: "Konfirmasi",
                text: "Apakah Anda yakin ingin menghapus data?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, hapus!",
                cancelButtonText: "Batal",
              }).then((result) => {
                if (result.isConfirmed) {
                  // Panggil fungsi deleteDraftPipeline jika dikonfirmasi
                  deleteDraftPipeline(cell.getRow())
                    .then(() => {
                      // Tampilkan SweetAlert sukses
                      Swal.fire({
                        title: "Sukses",
                        text: "Data berhasil terhapus",
                        icon: "success",
                        confirmButtonText: "OK",
                      });
                    })
                    .catch((error) => {
                      // Tampilkan SweetAlert error jika gagal
                      Swal.fire({
                        title: "Error",
                        text:
                          "Terjadi kesalahan saat menghapus data: " +
                          error.message,
                        icon: "error",
                        confirmButtonText: "OK",
                      });
                    });
                }
              });
            },
          });

          // Panggil API untuk mendapatkan data tabel
          $.ajax({
            type: "POST",
            url: url + "/pipeline/draft/getdata",
            data: {
              thn: tahun,
              bln: bulan,
            },
            dataType: "json",
            success: function (data) {
              new Tabulator("#tabel_draft_pipeline", {
                data: data,
                height: "350px",
                pagination: "local",
                paginationSize: 50,
                paginationSizeSelector: [25, 50, 75],
                layout: "fitColumns",
                columns: columns, // Gunakan kolom yang sudah difilter
              });
            },
          });
        },
      });
    }

    // Fungsi untuk mengupdate data saat sel diubah
    function updateDraftPipeline(data) {
      fetch(url + "/pipeline/draft/update", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          id: data.id,
          freq_visit: data.freq_visit,
          target_value: data.target_value,
          probability: data.probability,
        }),
      })
        .then((res) => res.json())
        .then((response) => {
          console.log("Response JSON:", response); // Debug respons JSON
          console.log("Toastr Message:", response.message); // Debug message sebelum ditampilkan
          if (response.status === "success") {
            toastr.success(response.message || "Data berhasil diperbarui");
          } else {
            toastr.error(response.message);
          }
        })
        .catch((error) => {
          console.error("Error updating data:", error);
          toastr.error("Terjadi kesalahan saat memperbarui data.");
        });
    }

    // Fungsi untuk menghapus data
    function deleteDraftPipeline(row) {
      return new Promise((resolve, reject) => {
        const data = row.getData();
        fetch(url + "/pipeline/draft/delete", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ id: data.id }),
        })
          .then((res) => res.json())
          .then((response) => {
            if (response.status === "success") {
              row.delete(); // Hapus baris dari tabel
              resolve(response); // Resolusi promise untuk SweetAlert sukses
            } else {
              reject(new Error(response.message || "Gagal menghapus data")); // Tolak promise untuk SweetAlert error
            }
          })
          .catch((error) => {
            console.error("Error deleting data:", error);
            reject(error); // Tolak promise untuk SweetAlert error
          });
      });
    }

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

    // Fungsi untuk memformat angka dengan separator ribuan
    function formatNumberWithCommas(value) {
      // Hapus semua karakter selain digit
      value = value.replace(/[^\d]/g, "");

      // Tambahkan koma sebagai pemisah ribuan
      return value.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    // Fungsi untuk menghapus separator ribuan
    function removeCommas(value) {
      return value.replace(/,/g, ""); // Hapus semua koma
    }

    // Tambahkan event listener ke input fields
    ["freqVisit", "targetNilai"].forEach((id) => {
      const inputField = document.getElementById(id);
      if (!inputField) return; // Jika elemen tidak ditemukan, skip

      // Event saat mengetik untuk memformat angka dengan separator ribuan
      inputField.addEventListener("input", function () {
        const rawValue = this.value.replace(/[^\d]/g, ""); // Ambil angka asli
        this.value = formatNumberWithCommas(rawValue); // Tampilkan nilai dengan separator ribuan
      });

      // Batasi input hanya angka
      inputField.addEventListener("keypress", function (e) {
        const charCode = e.which || e.keyCode;
        // Izinkan hanya angka (48-57), backspace (8), dan delete (46)
        if (
          !(charCode >= 48 && charCode <= 57) &&
          charCode !== 8 &&
          charCode !== 46
        ) {
          e.preventDefault();
        }
      });
    });

    // Update Option change grup barang
    $("#grupBarang").change(function () {
      var grp_prod = $(this).val();
      // AJAX request --
      $.ajax({
        url: url + "/master/barang/subgrup",
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
        url: url + "/master/barang/kelas",
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
      url: url + "master/pelanggan/datapelanggancab",
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

    // Konfigurasi Toastr
    toastr.options = {
      closeButton: true,
      debug: false,
      newestOnTop: false,
      progressBar: false,
      positionClass: "toast-top-right",
      preventDuplicates: false,
      onclick: null,
      showDuration: "200",
      hideDuration: "1000",
      timeOut: "5000",
      extendedTimeOut: "1000",
      showEasing: "swing",
      hideEasing: "linear",
      showMethod: "fadeIn",
      hideMethod: "fadeOut",
    };

    // Saat modal dibuka, inisialisasi ulang Select2 pada elemen dalam modal
    $("#formdetailpipeline").on("shown.bs.modal", function () {
      $("#masterpelanggan").select2({
        dropdownParent: $("#formdetailpipeline"),
      });

      $("#targetProbabilitas").select2({
        dropdownParent: $("#formdetailpipeline"),
      });
    });

    // PENYIMPANAN SEMENTARA DETAIL PIPELINE
    let pipelineTable = new Tabulator("#tabel_detail_pipeline", {
      height: "400px",
      layout: "fitColumns",
      placeholder: "Belum ada data detail pipeline.",
      columns: [
        {
          title: "Pelanggan",
          field: "cust_id",
          width: 200,
          headerHozAlign: "center",
        },
        {
          title: "Frekuensi Kunjungan",
          field: "freq_visit",
          headerHozAlign: "center",
          hozAlign: "center",
        },
        {
          title: "Target Nilai",
          field: "target_value",
          headerHozAlign: "center",
          hozAlign: "center",
        },
        {
          title: "Target Probabilitas",
          field: "probability",
          headerHozAlign: "center",
          hozAlign: "center",
        },
        {
          title: "Aksi",
          headerHozAlign: "center",
          hozAlign: "center",
          formatter: "buttonCross",
          cellClick: function (e, cell) {
            const data = cell.getRow().getData();
            fetch("/pipeline/temp/delete", {
              method: "POST",
              headers: { "Content-Type": "application/json" },
              body: JSON.stringify({ cust_id: data.cust_id }),
            })
              .then((res) => res.json())
              .then((response) => {
                if (response.status === "success") {
                  cell.getRow().delete();
                  toastr.success("Data berhasil dihapus.");
                } else {
                  toastr.error(response.message);
                }
              })
              .catch((error) => {
                console.error("Error deleting data:", error);
                toastr.error("Terjadi kesalahan saat menghapus data.");
              });
          },
        },
      ],
    });

    // Load data dari server
    fetch("/pipeline/temp/getdata?nik=default_nik")
      .then((res) => res.json())
      .then((data) => {
        if (data.status === "success") {
          pipelineTable.setData(data.data);
        } else {
          toastr.error("Proses memuat data: " + data.message);
        }
      })
      .catch((error) => {
        console.error("Error fetching data:", error);
        toastr.error("Terjadi kesalahan saat memuat data.");
      });

    const tambahButton = document.querySelector("#tambahDataDetPipeline");
    if (tambahButton) {
      tambahButton.addEventListener("click", function () {
        const pelanggan = document.querySelector("#masterpelanggan")?.value;
        const freqVisitElement = document.querySelector("#freqVisit");
        const targetNilaiElement = document.querySelector("#targetNilai");
        const targetProbabilitasElement = document.querySelector(
          "#targetProbabilitas"
        );

        const freqVisit = removeCommas(freqVisitElement?.value || "0");
        const targetNilai = removeCommas(targetNilaiElement?.value || "0");
        const targetProbabilitas = targetProbabilitasElement?.value;

        if (!pelanggan || !targetNilai) {
          toastr.error("Semua field harus diisi!");
          return;
        }

        // Validasi duplikasi di client
        const existingData = pipelineTable
          .getData()
          .find((row) => row.cust_id === pelanggan);
        if (existingData) {
          toastr.error("ID pelanggan tidak boleh sama.");
          return;
        }

        // Kirim data ke server
        fetch("/pipeline/temp/save", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            cust_id: pelanggan,
            freq_visit: freqVisit,
            target_value: targetNilai,
            probability: targetProbabilitas,
          }),
        })
          .then((res) => res.json())
          .then((response) => {
            if (response.status === "success") {
              toastr.success("Data berhasil ditambahkan.");
              pipelineTable.addData([
                {
                  cust_id: pelanggan,
                  freq_visit: freqVisit,
                  target_value: targetNilai,
                  probability: targetProbabilitas || "",
                },
              ]);

              // **RESET FORM SETELAH SIMPAN**
              if (freqVisitElement) freqVisitElement.value = "";
              if (targetNilaiElement) targetNilaiElement.value = "";

              // Reset Select2 dropdown (cek dulu apakah elemen ada)
              if ($("#masterpelanggan").length) {
                $("#masterpelanggan").val("").trigger("change");
              }
              if ($("#targetProbabilitas").length) {
                $("#targetProbabilitas").val("").trigger("change");
              }
              console.log("Reset form setelah penyimpanan berhasil.");
            } else {
              toastr.error(response.message);
            }
          })
          .catch((error) => {
            console.error("Error saving data:", error);
            toastr.error("Terjadi kesalahan saat menyimpan data.");
          });
      });
    } else {
      console.error("Element #tambahDataDetPipeline tidak ditemukan di DOM.");
    }

    const form = document.getElementById("formPipeline"); // Replace with your form ID

    form.addEventListener("submit", (e) => {
      e.preventDefault();

      Swal.fire({
        title: "Apakah Anda yakin?",
        text: "Data akan disimpan ke database.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, simpan!",
        cancelButtonText: "Batal",
      }).then((result) => {
        if (result.isConfirmed) {
          fetch("/pipeline/temp/insert", {
            method: "POST",
            body: new FormData(form),
          })
            .then((response) => response.json())
            .then((data) => {
              if (data.status === "success") {
                // Setelah berhasil menyimpan, hapus data temporary
                fetch("/pipeline/temp/delete", {
                  method: "POST",
                  headers: {
                    "Content-Type": "application/json",
                  },
                  body: JSON.stringify({ nik: data.nik, cust_id: null }), // Tambahkan cust_id sebagai null
                })
                  .then((res) => res.json())
                  .then((deleteData) => {
                    if (deleteData.status === "success") {
                      console.log("Data temporary berhasil dihapus");
                    } else {
                      console.error(
                        "Gagal menghapus data temporary:",
                        deleteData.message
                      );
                    }
                  })
                  .catch((error) =>
                    console.error("Error saat menghapus data temporary:", error)
                  );

                Swal.fire("Berhasil!", data.message, "success").then(() => {
                  window.location.href = "/pipeline/pembuatan";
                });
              } else {
                Swal.fire("Gagal!", data.message, "error");
              }
            })
            .catch((error) => {
              console.error("Fetch Error:", error);
              Swal.fire(
                "Error!",
                "Terjadi kesalahan saat menyimpan data.",
                "error"
              );
            });
        }
      });
    });
  } else if (window.location.pathname === "/pipeline/persetujuan") {
    // Variabel global untuk tabel
    let table;

    // Mendapatkan nilai awal
    var tahun_acc = $("#tahunAccPipeline").val();
    var bulan_acc = $("#bulanAccPipeline").val();
    var sales_marketing = $("#salesMarketing").val();

    // Data pipeline yang akan diverifikasi
    tabel_verifikasi_pipeline(tahun_acc, bulan_acc, sales_marketing);

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
    $("#bulanAccPipeline").html(options);

    // Inisialisasi Select2
    $("#bulanAccPipeline").select2({
      placeholder: "Pilih Bulan",
      allowClear: true,
    });

    // Event listener untuk menangkap perubahan pilihan
    $("#bulanAccPipeline").on("change", function () {
      const selectedValue = $(this).val(); // Mengambil value yang dipilih
      const selectedText = $("#bulanAccPipeline option:selected").text(); // Mengambil teks yang dipilih
      console.log(`Value: ${selectedValue}, Text: ${selectedText}`);
    });

    // Fetch cabang berdasarkan session branch_id
    if (branchId !== "11") {
      // Jika branch_id bukan '11', tampilkan hanya cabang dari session dan disable dropdown
      $("#cabangOps")
        .empty()
        .append(
          `<option value="${branchId}" selected>${branchId} - ${branchName}</option>`
        )
        .prop("disabled", true); // Nonaktifkan dropdown
    } else {
      // Jika branch_id adalah '11', ambil semua cabang dari API
      $.getJSON(url + "master/cabang", (branches) => {
        $("#cabangOps")
          .empty()
          .append('<option value="">Pilih Cabang</option>')
          .append(
            branches.map(
              (b) => `<option value="${b.branch_id}">${b.branch_name}</option>`
            )
          );
      });
    }

    // Event handler untuk perubahan cabang (tidak diubah)
    $("#cabangOps").on("change", function () {
      const branchId = $(this).val();
      console.log("Cabang terpilih: ", branchId); // Log Cabang terpilih
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

    // PERUBAHAN FILTER DROPDOWN
    // Tahun
    $("#tahunAccPipeline").change(function () {
      var tahun_acc = $(this).val();
      var bulan_acc = $("#bulanAccPipeline").val();
      var sales_marketing = $("#salesMarketing").val();

      tabel_verifikasi_pipeline(tahun_acc, bulan_acc, sales_marketing);
    });

    // Bulan
    $("#bulanAccPipeline").change(function () {
      var tahun_acc = $("#tahunAccPipeline").val();
      var bulan_acc = $(this).val();
      var sales_marketing = $("#salesMarketing").val();

      tabel_verifikasi_pipeline(tahun_acc, bulan_acc, sales_marketing);
    });

    // Tim Sales Marketing
    $("#salesMarketing").change(function () {
      var tahun_acc = $("#tahunAccPipeline").val();
      var bulan_acc = $("#bulanAccPipeline").val();
      var sales_marketing = $(this).val();

      console.log(
        "tahun = " +
          tahun_acc +
          " bulan = " +
          bulan_acc +
          " sales/marketing = " +
          sales_marketing
      );

      tabel_verifikasi_pipeline(tahun_acc, bulan_acc, sales_marketing);
    });

    // Inisialisasi tabel Verifikasi Pipeline
    function tabel_verifikasi_pipeline(tahun_acc, bulan_acc, sales_marketing) {
      if (table) {
        table.destroy(); // Hancurkan tabel lama jika ada
      }

      $.ajax({
        type: "GET",
        url: url + "/pipeline/groupuser", // Ambil group_id dari server
        dataType: "json",
        success: function (response) {
          let group_id = response.group_id;
          let columns = [
            {
              title: "Nama Kelas Barang",
              field: "class_name",
              headerHozAlign: "center",
            },
            {
              title: "Pelanggan",
              field: "cust_id",
              headerHozAlign: "center",
              formatter: function (cell, formatterParams, onRendered) {
                var rowData = cell.getRow().getData();
                return rowData.cust_id + " - " + rowData.cust_name;
              },
            },
            {
              title: "Target Nilai (Rp)",
              field: "target_value",
              headerHozAlign: "center",
              hozAlign: "right",
              formatter: "money",
              formatterParams: { decimal: ",", thousand: "." },
            },
          ];

          // Tambahkan kolom sesuai group_id
          if (group_id === "01" || group_id === "03") {
            columns.push({
              title: "Frekuensi Kunjungan",
              field: "freq_visit",
              headerHozAlign: "center",
              hozAlign: "center",
            });
          }

          if (group_id === "02" || group_id === "05") {
            columns.push({
              title: "Probabilitas",
              field: "probability",
              headerHozAlign: "center",
              hozAlign: "center",
            });
          }

          // Kolom "Setujui" dengan checkbox di header
          columns.push({
            title: "Setujui",
            field: "flg_approve",
            hozAlign: "center",
            headerHozAlign: "center",
            titleFormatter: function (cell) {
              var checkbox = document.createElement("input");
              checkbox.type = "checkbox";
              checkbox.id = "header-checkbox";

              // Logika untuk memilih/membatalkan semua checkbox di baris
              checkbox.addEventListener("click", function () {
                let isChecked = this.checked;
                table.getRows().forEach((row) => {
                  let rowCheckbox = row
                    .getElement()
                    .querySelector(".row-checkbox");
                  if (rowCheckbox) {
                    rowCheckbox.checked = isChecked;
                  }
                });
              });

              return checkbox;
            },
            formatter: function (cell) {
              let isApproved = cell.getValue();
              return `
            <input type="checkbox" class="row-checkbox" ${
              isApproved ? "checked" : ""
            } style="margin-right: 10px;">
            <button class="btn btn-sm btn-danger reject-btn"><i class='fa fa-edit'></i></button>
          `;
            },
            cellClick: function (e, cell) {
              let row = cell.getRow();
              let rowData = row.getData();
              let target = e.target;

              // Jika tombol "Reject" diklik (penolakan per baris)
              if (target.classList.contains("reject-btn")) {
                $("#rejectModal").modal("show");
                $("#reject_reason").val("");
                $("#rejectModal").data("row", row); // Simpan objek baris untuk digunakan nanti
              }
              // Tidak ada aksi saat checkbox diklik, hanya tandai untuk approve all
            },
          });

          // Panggil API untuk mendapatkan data tabel
          $.ajax({
            type: "POST",
            url: url + "/pipeline/verifikasi/getdata",
            data: {
              thn: tahun_acc,
              bln: bulan_acc,
              sales_marketing: sales_marketing,
            },
            dataType: "json",
            success: function (data) {
              table = new Tabulator("#tabel_verifikasi_pipeline", {
                data: data,
                height: "350px",
                pagination: "local",
                paginationSize: 100,
                paginationSizeSelector: [50, 100, 150, 200, 250],
                layout: "fitColumns",
                columns: columns,
              });

              // Event untuk tombol Simpan Data (Approve All)
              $("#saveApproveAll").on("click", function () {
                let rowsToApprove = table
                  .getRows()
                  .filter(
                    (row) =>
                      row.getElement().querySelector(".row-checkbox").checked
                  );

                if (rowsToApprove.length === 0) {
                  Swal.fire({
                    title: "Tidak ada data",
                    text: "Tidak ada data yang dipilih untuk disetujui.",
                    icon: "info",
                    confirmButtonText: "OK",
                  });
                  return;
                }

                Swal.fire({
                  title: "Apakah Anda yakin?",
                  text: "Data yang dipilih akan disetujui.",
                  icon: "warning",
                  showCancelButton: true,
                  confirmButtonText: "Ya, setujui!",
                  cancelButtonText: "Batal",
                }).then((result) => {
                  if (result.isConfirmed) {
                    let updatePromises = [];

                    rowsToApprove.forEach((row) => {
                      let rowData = row.getData();
                      updatePromises.push(
                        updateVerifPipeline(rowData.id, true, "", row)
                      );
                    });

                    Promise.all(updatePromises)
                      .then(() => {
                        // Tampilkan notifikasi keseluruhan dan reload halaman
                        Swal.fire({
                          title: "Berhasil!",
                          text: "Data yang dipilih telah disetujui.",
                          icon: "success",
                          confirmButtonText: "OK",
                        }).then(() => {
                          window.location.href = "/pipeline/persetujuan";
                        });
                      })
                      .catch((error) => {
                        console.error("Error:", error);
                        Swal.fire({
                          title: "Gagal!",
                          text: "Terjadi kesalahan saat menyimpan data.",
                          icon: "error",
                          confirmButtonText: "OK",
                        });
                      });
                  }
                });
              });

              // Event untuk tombol Simpan di modal penolakan
              $("#saveReject").on("click", function () {
                let reason = $("#reject_reason").val().trim();
                if (reason === "") {
                  alert("Silakan isi alasan penolakan!");
                  return;
                }

                let row = $("#rejectModal").data("row");
                let rowData = row.getData();

                Swal.fire({
                  title: "Apakah Anda yakin?",
                  text: "Data akan ditolak dengan alasan: " + reason,
                  icon: "warning",
                  showCancelButton: true,
                  confirmButtonText: "Ya, tolak!",
                  cancelButtonText: "Batal",
                }).then((result) => {
                  if (result.isConfirmed) {
                    updateVerifPipeline(rowData.id, false, reason, row).then(
                      () => {
                        row.delete(); // Hapus baris setelah ditolak
                        $("#rejectModal").modal("hide");
                      }
                    );
                  }
                  // Jika dibatalkan, modal tetap terbuka dengan alasan yang sudah diisi
                });
              });
            },
          });
        },
      });
    }

    // Fungsi AJAX untuk mengupdate verifikasi ke server
    function updateVerifPipeline(id, status, reason, row) {
      return new Promise((resolve, reject) => {
        $.ajax({
          type: "POST",
          url: url + "/pipeline/verifikasi/update",
          data: { id: id, flg_approve: status, reason_reject: reason },
          dataType: "json",
          success: function (response) {
            if (response.status === "success") {
              // Hanya tampilkan Toastr untuk penolakan (status = false), bukan untuk persetujuan
              if (!status) {
                let message = `Pipeline ID ${id} ditolak dengan alasan: ${reason}`;
                toastr.success(message);
              }
              resolve();
            } else {
              toastr.error("Gagal memperbarui data.");
              reject();
            }
          },
          error: function (xhr) {
            toastr.error("Terjadi kesalahan saat memperbarui data.");
            console.error("Error:", xhr.responseText);
            reject();
          },
        });
      });
    }
  } else if (window.location.pathname === "/pipeline/monitoring") {
    //mendapatkan nilai
    var tahun_mon = $("#tahunMonPipeline").val();
    var bulan_mon = $("#bulanMonPipeline").val();
    var sales_marketing = $("#salesMarketing").val();

    //data pipeline yang akan diverifikasi
    tabel_monitoring_pipeline(tahun_mon, bulan_mon, sales_marketing);

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
    $("#bulanMonPipeline").html(options);

    // Inisialisasi Select2
    $("#bulanMonPipeline").select2({
      placeholder: "Pilih Bulan",
      allowClear: true,
    });

    // Event listener untuk menangkap perubahan pilihan
    $("#bulanMonPipeline").on("change", function () {
      const selectedValue = $(this).val(); // Mengambil value yang dipilih
      const selectedText = $("#bulanMonPipeline option:selected").text(); // Mengambil teks yang dipilih
      console.log(`Value: ${selectedValue}, Text: ${selectedText}`);
    });

    // Fetch cabang berdasarkan session branch_id
    if (branchId !== "11") {
      // Jika branch_id bukan '11', tampilkan hanya cabang dari session dan disable dropdown
      $("#cabangOps")
        .empty()
        .append(
          `<option value="${branchId}" selected>${branchId} - ${branchName}</option>`
        )
        .prop("disabled", true); // Nonaktifkan dropdown
    } else {
      // Jika branch_id adalah '11', ambil semua cabang dari API
      $.getJSON(url + "master/cabang", (branches) => {
        $("#cabangOps")
          .empty()
          .append('<option value="">Pilih Cabang</option>')
          .append(
            branches.map(
              (b) => `<option value="${b.branch_id}">${b.branch_name}</option>`
            )
          );
      });
    }

    $("#cabangOps").on("change", function () {
      const branchId = $(this).val();
      console.log("Cabang terpilih: ", branchId); // Log Cabang terpilih
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

    //PERUBAHAN FILTER DROPDOWN
    //Tahun
    $("#tahunMonPipeline").change(function () {
      var tahun_mon = $(this).val();
      var bulan_mon = $("#bulanMonPipeline").val();
      var sales_marketing = $("#salesMarketing").val();

      tabel_monitoring_pipeline(tahun_mon, bulan_mon, sales_marketing);
    });

    //Bulan
    $("#bulanMonPipeline").change(function () {
      var tahun_mon = $("#tahunMonPipeline").val();
      var bulan_mon = $(this).val();
      var sales_marketing = $("#salesMarketing").val();

      tabel_monitoring_pipeline(tahun_mon, bulan_mon, sales_marketing);
    });

    //Tim Sales Marketing
    $("#salesMarketing").change(function () {
      var tahun_mon = $("#tahunMonPipeline").val();
      var bulan_mon = $("#bulanMonPipeline").val();
      var sales_marketing = $(this).val();

      // panggil function
      console.log(
        "tahun = " +
          tahun_mon +
          " bulan = " +
          bulan_mon +
          " sales/marketing = " +
          sales_marketing
      );

      tabel_monitoring_pipeline(tahun_mon, bulan_mon, sales_marketing);
    });

    // Inisialisasi tabel Verifikasi Pipeline
    function tabel_monitoring_pipeline(tahun_mon, bulan_mon, sales_marketing) {
      var table; // Deklarasikan di luar AJAX agar bisa diakses oleh #selectAll

      $.ajax({
        type: "GET",
        url: url + "/pipeline/groupuser", // Ambil group_id dari server
        dataType: "json",
        success: function (response) {
          let group_id = response.group_id;
          let columns = [
            {
              title: "Kelas Produk",
              field: "class_name",
              headerHozAlign: "center",
              formatter: function (cell, formatterParams, onRendered) {
                var rowData = cell.getRow().getData();
                return (
                  rowData.subgroup_id +
                  " - " +
                  rowData.class_id +
                  " - " +
                  rowData.class_name
                );
              },
            },
            {
              title: "Pelanggan",
              field: "cust_id",
              headerHozAlign: "center",
              formatter: function (cell, formatterParams, onRendered) {
                var rowData = cell.getRow().getData();
                return rowData.cust_id + " - " + rowData.cust_name;
              },
            },
            {
              title: "Target Nilai (Rp)",
              field: "target_value",
              headerHozAlign: "center",
              hozAlign: "right",
              formatter: "money",
              formatterParams: { decimal: ",", thousand: "." },
            },
            {
              title: "Realisasi Nilai (Rp)",
              field: "real_value",
              headerHozAlign: "center",
              hozAlign: "right",
              formatter: "money",
              formatterParams: { decimal: ",", thousand: "." },
            },
          ];

          // Tambahkan kolom sesuai group_id
          if (group_id === "01" || group_id === "03") {
            columns.push({
              title: "Frekuensi Kunjungan",
              field: "freq_visit",
              headerHozAlign: "center",
              hozAlign: "center",
            });
          }

          if (group_id === "02" || group_id === "05") {
            columns.push(
              {
                title: "Penyesuaian Nilai (Rp)",
                field: "adj_value",
                headerHozAlign: "center",
                hozAlign: "right",
                formatter: "money",
                formatterParams: { decimal: ",", thousand: "." },
              },
              {
                title: "Probabilitas",
                field: "probability",
                headerHozAlign: "center",
                hozAlign: "center",
              }
            );
          }

          // Panggil API untuk mendapatkan data tabel
          $.ajax({
            type: "POST",
            url: url + "/pipeline/monitoring/getdata",
            data: {
              thn: tahun_mon,
              bln: bulan_mon,
              sales_marketing: sales_marketing,
            },
            dataType: "json",
            success: function (data) {
              table = new Tabulator("#tabel_monitoring_pipeline", {
                // Gunakan var table
                data: data,
                height: "350px",
                pagination: "local",
                paginationSize: 50,
                paginationSizeSelector: [25, 50, 75],
                layout: "fitColumns",
                columns: columns,
              });
            },
          });
        },
      });
    }

    // Fungsi AJAX untuk mengupdate verifikasi ke server dan menghapus baris dari tabel
    // function updateVerifPipeline(
    //   id,
    //   status,
    //   reason,
    //   cell = null,
    //   callbacks = {}
    // ) {
    //   $.ajax({
    //     type: "POST",
    //     url: url + "/pipeline/verifikasi/update",
    //     data: { id: id, flg_approve: status, reason_reject: reason },
    //     dataType: "json",
    //     success: function (response) {
    //       if (response.status === "success") {
    //         let message = status
    //           ? `Pipeline ID ${id} disetujui`
    //           : `Pipeline ID ${id} ditolak dengan alasan: ${reason}`;
    //         toastr.success(message);

    //         // Jika update dilakukan per baris, hapus barisnya
    //         if (cell) {
    //           cell.getRow().delete();
    //         }

    //         // Jika ada callback (misalnya dari Select All), jalankan
    //         if (callbacks.onSuccess) {
    //           callbacks.onSuccess();
    //         }
    //       } else {
    //         toastr.error("Gagal memperbarui data.");
    //       }
    //     },
    //     error: function (xhr) {
    //       toastr.error("Terjadi kesalahan saat memperbarui data.");
    //       console.error("Error:", xhr.responseText);
    //     },
    //   });
    // }
  }
});
