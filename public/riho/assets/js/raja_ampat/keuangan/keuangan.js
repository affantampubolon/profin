$(document).ready(function () {
  if (window.location.pathname === "/keuangan/anggaran/index") {
    // Fetch data proyek untuk dropdown
    $.ajax({
      url: url + "proyek/dataanggaranfilter",
      method: "GET",
      dataType: "json",
      success: function (data) {
        $("#proyekfilter")
          .empty()
          .append('<option value="" selected>Pilih No. Proyek</option>');
        data.forEach((proyek) => {
          $("#proyekfilter").append(
            `<option value="${proyek.id}">${proyek.wbs_no}</option>`
          );
        });
      },
      error: function (xhr, status, error) {
        console.error("Error fetching proyek data:", error);
        alert("Gagal memuat data proyek: " + error);
      },
    });

    // Inisialisasi tabel Tabulator
    // Tambahkan kelas CSS ke elemen #tabel_rencana_anggaran
    $("#tabel_rencana_anggaran").addClass("table-bordered table-sm");
    var table = new Tabulator("#tabel_rencana_anggaran", {
      height: "500px",
      layout: "fitColumns",
      responsiveLayout: "collapse",
      pagination: "local",
      paginationSize: 8,
      columns: [
        {
          title: "COA",
          field: "coa",
          headerHozAlign: "center",
          formatter: function (cell) {
            // Ambil data COA dari server untuk menampilkan value - label
            var rowData = cell.getRow().getData();
            var coaValue = rowData.coa;
            // Cari label yang sesuai dari data COA (diasumsikan disimpan di variabel global)
            var coaLabel =
              coaData.find((item) => item.value === coaValue)?.label || "";
            return coaValue + " - " + coaLabel;
          },
          editor: false, // Tidak dapat diedit
        },
        {
          title: "Uraian",
          field: "description",
          headerHozAlign: "center",
          editor: "input",
          cssClass: "highlight-column",
        },
        {
          title: "Nilai Anggaran",
          field: "budget_amt",
          headerHozAlign: "center",
          hozAlign: "right",
          formatter: "money",
          formatterParams: { decimal: ",", thousand: "." },
          editor: "input",
          bottomCalc: "sum",
          bottomCalcFormatter: "money",
          bottomCalcFormatterParams: { decimal: ",", thousand: "." },
          cssClass: "highlight-column",
        },
      ],
    });

    // Variabel untuk menyimpan data COA dari server
    var coaData = [];

    // Event listener untuk dropdown proyek
    $("#proyekfilter").on("change", function () {
      var proyekId = $(this).val();
      if (proyekId) {
        // Ambil data COA dari server
        $.ajax({
          url: url + "master/coa/datafilter",
          method: "GET",
          dataType: "json",
          success: function (data) {
            coaData = data; // Simpan data COA untuk digunakan di formatter
            // Format data untuk tabel
            var tableData = data.map((coa) => ({
              coa: coa.value, // Hanya simpan value (coa_code)
              description: "", // Kosong untuk diisi pengguna
              budget_amt: "", // Kosong untuk diisi pengguna
            }));
            table.setData(tableData);
          },
          error: function (xhr, status, error) {
            console.error("Error fetching COA data:", error);
            alert("Gagal memuat data COA: " + error);
          },
        });
      } else {
        table.setData([]);
        coaData = []; // Reset data COA jika tidak ada proyek
      }
    });

    // Event listener untuk tombol "Simpan"
    $("button[type='submit']").on("click", function (e) {
      e.preventDefault();
      Swal.fire({
        title: "Konfirmasi",
        text: "Apakah Anda yakin ingin menyimpan data anggaran?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya, simpan!",
        cancelButtonText: "Batal",
      }).then((result) => {
        if (result.isConfirmed) {
          var proyekId = $("#proyekfilter").val();
          if (!proyekId) {
            alert("Pilih proyek terlebih dahulu.");
            return;
          }

          var tableData = table.getData();
          var valid = true;
          tableData.forEach((row) => {
            if (!row.budget_amt || row.budget_amt === "") {
              valid = false;
            }
          });

          if (!valid) {
            alert("Semua Nilai Anggaran harus diisi.");
            return;
          }

          // Kirim data ke server
          $.ajax({
            url: url + "keuangan/anggaran/insertdataanggaran",
            method: "POST",
            data: {
              proyekId: proyekId,
              data: JSON.stringify(tableData),
            },
            dataType: "json",
            success: function (response) {
              if (response.status === "success") {
                Swal.fire({
                  title: "Sukses",
                  text: "Data anggaran berhasil disimpan",
                  icon: "success",
                  confirmButtonText: "OK",
                }).then(() => {
                  window.location.href = "/keuangan/anggaran/index";
                });
              } else {
                alert("Gagal menyimpan data: " + response.message);
              }
            },
            error: function (xhr, status, error) {
              console.error("Error saving data:", error);
              alert("Terjadi kesalahan saat menyimpan data: " + error);
            },
          });
        }
      });
    });
  } else if (window.location.pathname === "/keuangan/realisasi/index") {
    // Variabel untuk menyimpan data COA dan dokumen dari server
    var coaData = [];
    var docData = [];

    // Inisialisasi tabel Tabulator
    // Tambahkan kelas CSS ke elemen #tabel_realisasi_biaya
    $("#tabel_realisasi_biaya").addClass("table-bordered table-sm");
    var table = new Tabulator("#tabel_realisasi_biaya", {
      height: "500px",
      layout: "fitColumns",
      responsiveLayout: "collapse",
      pagination: "local",
      paginationSize: 20,
      paginationSizeSelector: [10, 20, 30],
      data: [], // Mulai dengan tabel kosong
      columns: [
        {
          title: "Aksi",
          hozAlign: "center",
          width: 75,
          headerHozAlign: "center",
          formatter: "buttonCross",
          cellClick: function (e, cell) {
            cell.getRow().delete(); // Menghapus baris langsung dari tabel
          },
        },
        {
          title: "COA",
          field: "coa",
          headerHozAlign: "center",
          hozAlign: "center",
          formatter: function (cell) {
            var coaValue = cell.getValue();
            var coaLabel =
              coaData.find((item) => item.value === coaValue)?.label || "";
            return coaValue ? coaValue + " - " + coaLabel : "";
          },
          editor: "list",
          editorParams: {
            valuesURL: url + "/master/coa/datafilter",
            placeholderLoading: "Menunggu Data...",
            itemFormatter: function (label, value) {
              return "<strong>" + value + " - " + label + "</strong>";
            },
            autocomplete: true,
            filterRemote: true, // Filtering dilakukan di server
          },
        },
        {
          title: "No. WBS",
          field: "id_ref",
          headerHozAlign: "center",
          formatter: function (cell) {
            var docValue = cell.getValue();
            var docLabel =
              docData.find((item) => item.value === docValue)?.label || "";
            return docLabel;
          },
          editor: "list",
          editorParams: {
            valuesURL: url + "/proyek/datarealisasifilter",
            placeholderLoading: "Menunggu Data...",
            itemFormatter: function (label, value) {
              return "<strong>" + label + "</strong>";
            },
            autocomplete: true,
            filterRemote: true, // Filtering di server
          },
        },
        {
          title: "Nilai Sisa",
          field: "diff_amt", // Gunakan field valid agar bisa dirender
          headerHozAlign: "center",
          hozAlign: "right",
          formatter: "money",
          formatterParams: { decimal: ",", thousand: "." },
        },
        {
          title: "Uraian",
          field: "description",
          headerHozAlign: "center",
          editor: "input",
          cssClass: "highlight-column",
        },
        {
          title: "Realisasi Biaya",
          field: "real_amt",
          headerHozAlign: "center",
          hozAlign: "right",
          formatter: "money",
          formatterParams: { decimal: ",", thousand: "." },
          editor: "input",
          bottomCalc: "sum",
          bottomCalcFormatter: "money",
          bottomCalcFormatterParams: { decimal: ",", thousand: "." },
          cssClass: "highlight-column",
        },
      ],
    });

    table.on("cellEdited", function (cell) {
      const field = cell.getField();
      const row = cell.getRow();

      // Hanya trigger jika field coa atau id_ref yang diubah
      if (field === "coa" || field === "id_ref") {
        updateDiffAmt(row);
      }
    });

    // Memuat data COA dari server saat halaman dimuat
    $.ajax({
      url: url + "/master/coa/datafilter",
      method: "GET",
      dataType: "json",
      success: function (data) {
        coaData = data; // Simpan data COA untuk formatter
      },
      error: function (xhr, status, error) {
        console.error("Error fetching COA data:", error);
        alert("Gagal memuat data COA: " + error);
      },
    });

    // Memuat data No. Dokumen dari server saat halaman dimuat
    $.ajax({
      url: url + "/proyek/datarealisasifilter",
      method: "GET",
      dataType: "json",
      success: function (data) {
        docData = data; // Simpan data No. Dokumen untuk formatter
      },
      error: function (xhr, status, error) {
        console.error("Error fetching document data:", error);
        alert("Gagal memuat data No. Dokumen: " + error);
      },
    });

    // Fungsi untuk memperbarui nilai 'Nilai Sisa' berdasarkan COA dan id_ref
    function updateDiffAmt(row) {
      const rowData = row.getData();
      const coa = rowData.coa;
      const id_ref = rowData.id_ref;

      if (coa && id_ref) {
        $.ajax({
          url: url + "/master/coa/datanilai",
          method: "POST",
          data: { coa: coa, id_ref: id_ref },
          dataType: "json",
          success: function (response) {
            // Ambil item pertama dari array jika ada
            if (Array.isArray(response) && response.length > 0) {
              const item = response[0];
              row.update({ diff_amt: parseFloat(item.diff_amt) });
            } else {
              row.update({ diff_amt: 0 });
            }
          },

          error: function (xhr, status, error) {
            console.error("Gagal memuat nilai sisa:", error);
            row.update({ diff_amt: 0 }); // fallback kalau error
          },
        });
      }
    }

    // Fungsi untuk menambahkan baris kosong
    function addBlankRow() {
      table.addRow({});
    }

    // Event listener untuk tombol "Tambah Baris"
    $("#tambahbaris").on("click", addBlankRow);

    // Event listener untuk tombol "Simpan"
    $("button[type='submit']").on("click", function (e) {
      e.preventDefault();
      Swal.fire({
        title: "Konfirmasi",
        text: "Apakah Anda yakin ingin menyimpan data realisasi biaya?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya, simpan!",
        cancelButtonText: "Batal",
      }).then((result) => {
        if (result.isConfirmed) {
          var tableData = table.getData();
          var valid = true;
          tableData.forEach((row) => {
            if (!row.real_amt || row.real_amt === "") {
              valid = false;
            }
          });

          if (!valid) {
            Swal.fire({
              title: "Peringatan",
              text: "Semua Realisasi Biaya harus diisi.",
              icon: "warning",
              confirmButtonText: "OK",
            });
            return;
          }

          // Kirim data ke server
          $.ajax({
            url: url + "keuangan/realisasi/insertdatarealisasi",
            method: "POST",
            data: {
              data: JSON.stringify(tableData),
            },
            dataType: "json",
            success: function (response) {
              if (response.status === "success") {
                Swal.fire({
                  title: "Sukses",
                  text: response.message,
                  icon: "success",
                  confirmButtonText: "OK",
                }).then(() => {
                  window.location.href = "/keuangan/realisasi/index";
                });
              } else {
                // Tampilkan pesan error dari server
                Swal.fire({
                  title: "Error",
                  text: response.message,
                  icon: "error",
                  confirmButtonText: "OK",
                });
              }
            },
            error: function (xhr) {
              let msg = "Terjadi kesalahan saat menyimpan data.";
              if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
              }
              Swal.fire("Error", msg, "error");
            },
          });
        }
      });
    });
  } else if (window.location.pathname === "/keuangan/dropping/index") {
    // Variabel untuk menyimpan data COA dan dokumen dari server
    var coaData = [];
    var docData = [];

    // Inisialisasi tabel Tabulator
    // Tambahkan kelas CSS ke elemen #tabel_realisasi_biaya
    $("#tabel_dropping_biaya").addClass("table-bordered table-sm");
    var table = new Tabulator("#tabel_dropping_biaya", {
      height: "500px",
      layout: "fitColumns",
      responsiveLayout: "collapse",
      pagination: "local",
      paginationSize: 20,
      paginationSizeSelector: [10, 20, 30],
      data: [], // Mulai dengan tabel kosong
      columns: [
        {
          title: "Aksi",
          hozAlign: "center",
          width: 75,
          headerHozAlign: "center",
          formatter: "buttonCross",
          cellClick: function (e, cell) {
            cell.getRow().delete(); // Menghapus baris langsung dari tabel
          },
        },
        {
          title: "COA",
          field: "coa",
          headerHozAlign: "center",
          hozAlign: "center",
          formatter: function (cell) {
            var coaValue = cell.getValue();
            var coaLabel =
              coaData.find((item) => item.value === coaValue)?.label || "";
            return coaValue ? coaValue + " - " + coaLabel : "";
          },
          editor: "list",
          editorParams: {
            valuesURL: url + "/master/coa/datafilter",
            placeholderLoading: "Menunggu Data...",
            itemFormatter: function (label, value) {
              return "<strong>" + value + " - " + label + "</strong>";
            },
            autocomplete: true,
            filterRemote: true, // Filtering dilakukan di server
          },
        },
        {
          title: "No. WBS",
          field: "id_ref",
          headerHozAlign: "center",
          formatter: function (cell) {
            var docValue = cell.getValue();
            var docLabel =
              docData.find((item) => item.value === docValue)?.label || "";
            return docLabel;
          },
          editor: "list",
          editorParams: {
            valuesURL: url + "/proyek/datarealisasifilter",
            placeholderLoading: "Menunggu Data...",
            itemFormatter: function (label, value) {
              return "<strong>" + label + "</strong>";
            },
            autocomplete: true,
            filterRemote: true, // Filtering di server
          },
        },
        {
          title: "Nilai Sisa",
          field: "net_plan_amt", // Gunakan field valid agar bisa dirender
          headerHozAlign: "center",
          hozAlign: "right",
          formatter: "money",
          formatterParams: { decimal: ",", thousand: "." },
        },
        {
          title: "Uraian",
          field: "description",
          headerHozAlign: "center",
          editor: "input",
          cssClass: "highlight-column",
        },
        {
          title: "Biaya Dropping",
          field: "real_drop_amt",
          headerHozAlign: "center",
          hozAlign: "right",
          formatter: "money",
          formatterParams: { decimal: ",", thousand: "." },
          editor: "input",
          bottomCalc: "sum",
          bottomCalcFormatter: "money",
          bottomCalcFormatterParams: { decimal: ",", thousand: "." },
          cssClass: "highlight-column",
        },
      ],
    });

    table.on("cellEdited", function (cell) {
      const field = cell.getField();
      const row = cell.getRow();

      // Hanya trigger jika field coa atau id_ref yang diubah
      if (field === "coa" || field === "id_ref") {
        updateDiffAmt(row);
      }
    });

    // Memuat data COA dari server saat halaman dimuat
    $.ajax({
      url: url + "/master/coa/datafilter",
      method: "GET",
      dataType: "json",
      success: function (data) {
        coaData = data; // Simpan data COA untuk formatter
      },
      error: function (xhr, status, error) {
        console.error("Error fetching COA data:", error);
        alert("Gagal memuat data COA: " + error);
      },
    });

    // Memuat data No. Dokumen dari server saat halaman dimuat
    $.ajax({
      url: url + "/proyek/datarealisasifilter",
      method: "GET",
      dataType: "json",
      success: function (data) {
        docData = data; // Simpan data No. Dokumen untuk formatter
      },
      error: function (xhr, status, error) {
        console.error("Error fetching document data:", error);
        alert("Gagal memuat data No. Dokumen: " + error);
      },
    });

    // Fungsi untuk memperbarui nilai 'Nilai Sisa' berdasarkan COA dan id_ref
    function updateDiffAmt(row) {
      const rowData = row.getData();
      const coa = rowData.coa;
      const id_ref = rowData.id_ref;

      if (coa && id_ref) {
        $.ajax({
          url: url + "/master/coa/datanilai",
          method: "POST",
          data: { coa: coa, id_ref: id_ref },
          dataType: "json",
          success: function (response) {
            // Ambil item pertama dari array jika ada
            if (Array.isArray(response) && response.length > 0) {
              const item = response[0];
              row.update({ net_plan_amt: parseFloat(item.net_plan_amt) });
            } else {
              row.update({ net_plan_amt: 0 });
            }
          },

          error: function (xhr, status, error) {
            console.error("Gagal memuat nilai sisa:", error);
            row.update({ net_plan_amt: 0 }); // fallback kalau error
          },
        });
      }
    }

    // Fungsi untuk menambahkan baris kosong
    function addBlankRow() {
      table.addRow({});
    }

    // Event listener untuk tombol "Tambah Baris"
    $("#tambahbaris").on("click", addBlankRow);

    // Event listener untuk tombol "Simpan"
    $("button[type='submit']").on("click", function (e) {
      e.preventDefault();
      Swal.fire({
        title: "Konfirmasi",
        text: "Apakah Anda yakin ingin menyimpan data biaya dropping?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya, simpan!",
        cancelButtonText: "Batal",
      }).then((result) => {
        if (result.isConfirmed) {
          var tableData = table.getData();
          var valid = true;
          tableData.forEach((row) => {
            if (!row.real_drop_amt || row.real_drop_amt === "") {
              valid = false;
            }
          });

          if (!valid) {
            Swal.fire({
              title: "Peringatan",
              text: "Semua Biaya Dropping harus diisi.",
              icon: "warning",
              confirmButtonText: "OK",
            });
            return;
          }

          // Kirim data ke server
          $.ajax({
            url: url + "keuangan/dropping/insertdatadropping",
            method: "POST",
            data: {
              data: JSON.stringify(tableData),
            },
            dataType: "json",
            success: function (response) {
              if (response.status === "success") {
                Swal.fire({
                  title: "Sukses",
                  text: response.message,
                  icon: "success",
                  confirmButtonText: "OK",
                }).then(() => {
                  window.location.href = "/keuangan/dropping/index";
                });
              } else {
                // Tampilkan pesan error dari server
                Swal.fire({
                  title: "Error",
                  text: response.message,
                  icon: "error",
                  confirmButtonText: "OK",
                });
              }
            },
            error: function (xhr) {
              let msg = "Terjadi kesalahan saat menyimpan data.";
              if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
              }
              Swal.fire("Error", msg, "error");
            },
          });
        }
      });
    });
  } else if (window.location.pathname === "/keuangan/pembayaran/index") {
    var docData = [];

    $("#tabel_pembayaran").addClass("table-bordered table-sm");
    var table = new Tabulator("#tabel_pembayaran", {
      height: "500px",
      frozenColumns: true,
      pagination: "local",
      paginationSize: 20,
      paginationSizeSelector: [10, 20, 30],
      data: [],
      columns: [
        {
          title: "Aksi",
          hozAlign: "center",
          headerHozAlign: "center",
          frozen: true,
          formatter: "buttonCross",
          cellClick: function (e, cell) {
            cell.getRow().delete();
          },
        },
        {
          title: "Unggah",
          headerHozAlign: "center",
          hozAlign: "center",
          frozen: true,
          formatter: function (cell) {
            var rowData = cell.getRow().getData();
            if (rowData.file_invoice) {
              return `<span>${rowData.file_invoice}</span>`;
            } else {
              return `<a class="badge rounded-circle p-2 badge-info upload-btn" href="#"><i class="fa fa-upload" style="cursor: pointer;"></i></a>`;
            }
          },
          cellClick: function (e, cell) {
            if (!cell.getRow().getData().file_invoice) {
              // Hanya buka modal jika belum ada file
              var row = cell.getRow();
              $("#uploadButton").data("row", row); // Simpan referensi baris
              $("#unggahInvoiceModal").modal("show");
            }
          },
        },
        {
          title: "Tgl Terbit Invoice",
          field: "invoice_date",
          headerHozAlign: "center",
          hozAlign: "center",
          frozen: true,
          editor: "date",
          editorParams: { format: "dd-MM-yyyy" },
        },
        {
          title: "Tgl Pembayaran",
          field: "payment_date",
          headerHozAlign: "center",
          hozAlign: "center",
          frozen: true,
          editor: "date",
          editorParams: { format: "dd-MM-yyyy" },
        },
        {
          title: "No. WBS",
          field: "id_ref",
          headerHozAlign: "center",
          minWidth: 250,
          formatter: function (cell) {
            var docValue = cell.getValue();
            var docLabel =
              docData.find((item) => item.value === docValue)?.label || "";
            return docLabel;
          },
          editor: "list",
          editorParams: {
            valuesURL: url + "/proyek/datapembayaranfilter",
            placeholderLoading: "Menunggu Data...",
            itemFormatter: function (label, value) {
              return "<strong>" + label + "</strong>";
            },
            autocomplete: true,
            filterRemote: true,
          },
        },
        {
          title: "Kendala",
          field: "reason",
          headerHozAlign: "center",
          minWidth: 150,
          editor: "input",
          cssClass: "highlight-column",
        },
        {
          title: "Jml Pembayaran",
          field: "payment_amt",
          headerHozAlign: "center",
          hozAlign: "right",
          minWidth: 200,
          formatter: "money",
          formatterParams: { decimal: ",", thousand: "." },
          editor: "input",
          bottomCalc: "sum",
          bottomCalcFormatter: "money",
          bottomCalcFormatterParams: { decimal: ",", thousand: "." },
          cssClass: "highlight-column",
        },
      ],
    });

    // Memuat data No. Dokumen
    $.ajax({
      url: url + "/proyek/datapembayaranfilter",
      method: "GET",
      dataType: "json",
      success: function (data) {
        docData = data;
      },
      error: function (xhr, status, error) {
        console.error("Error fetching document data:", error);
        alert("Gagal memuat data No. Dokumen: " + error);
      },
    });

    // Event listener untuk unggah file
    $("#uploadButton").on("click", function () {
      var row = $(this).data("row");
      var formData = new FormData($("#uploadForm")[0]);

      $.ajax({
        url: url + "/keuangan/pembayaran/unggahinvoice",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function (response) {
          if (response.status === "success") {
            row.update({ file_invoice: response.fileName }); // Simpan nama file ke baris
            $("#unggahInvoiceModal").modal("hide");
            $("#uploadForm")[0].reset(); // Reset form
            Swal.fire("Berhasil!", "File berhasil diunggah.", "success");
          } else {
            Swal.fire("Gagal!", response.message, "error");
          }
        },
        error: function (xhr, status, err) {
          Swal.fire(
            "Error!",
            "Terjadi kesalahan saat mengunggah file.",
            "error"
          );
        },
      });
    });

    // Fungsi untuk menambahkan baris kosong
    function addBlankRow() {
      table.addRow({});
    }

    $("#tambahbaris").on("click", addBlankRow);

    // Event listener untuk tombol "Simpan"
    $("button[type='submit']").on("click", function (e) {
      e.preventDefault();
      Swal.fire({
        title: "Konfirmasi",
        text: "Apakah Anda yakin ingin menyimpan data pembayaran?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya, simpan!",
        cancelButtonText: "Batal",
      }).then((result) => {
        if (result.isConfirmed) {
          var tableData = table.getData();
          var valid = true;
          tableData.forEach((row) => {
            if (!row.payment_amt || row.payment_amt === "") {
              valid = false;
            }
          });

          if (!valid) {
            Swal.fire({
              title: "Peringatan",
              text: "Semua Data Pembayaran harus diisi.",
              icon: "warning",
              confirmButtonText: "OK",
            });
            return;
          }

          $.ajax({
            url: url + "keuangan/pembayaran/insertdatapembayaran",
            method: "POST",
            data: { data: JSON.stringify(tableData) },
            dataType: "json",
            success: function (response) {
              if (response.status === "success") {
                Swal.fire({
                  title: "Sukses",
                  text: response.message,
                  icon: "success",
                  confirmButtonText: "OK",
                }).then(() => {
                  window.location.href = "/keuangan/pembayaran/index";
                });
              } else {
                Swal.fire({
                  title: "Error",
                  text: response.message,
                  icon: "error",
                  confirmButtonText: "OK",
                });
              }
            },
            error: function (xhr) {
              let msg = "Terjadi kesalahan saat menyimpan data.";
              if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
              }
              Swal.fire("Error", msg, "error");
            },
          });
        }
      });
    });
  }
});
