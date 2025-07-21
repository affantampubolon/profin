$(document).ready(function () {
  if (window.location.pathname === "/proyek/registrasi/index") {
    // Fetch pminspector untuk Project Manager
    $.ajax({
      url: url + "master/pminspector/datapminspector",
      method: "GET",
      dataType: "json",
      success: function (data) {
        $("#projectmanager")
          .empty()
          .append(
            '<option value="" selected>Pilih Project Manager(PM)</option>'
          );
        data.forEach((pminspector) => {
          $("#projectmanager").append(
            `<option value="${pminspector.nik}">${pminspector.emp_name}</option>`
          );
        });
      },
      error: function () {
        alert("Gagal memuat data pm/inspector");
      },
    });

    // Fetch pminspector untuk Inspector
    $.ajax({
      url: url + "master/pminspector/datapminspector",
      method: "GET",
      dataType: "json",
      success: function (data) {
        $("#inspector")
          .empty()
          .append('<option value="" selected>Pilih Inspector</option>');
        data.forEach((pminspector) => {
          $("#inspector").append(
            `<option value="${pminspector.nik}">${pminspector.emp_name}</option>`
          );
        });
      },
      error: function () {
        alert("Gagal memuat data pm/inspector");
      },
    });

    // Fetch pelanggan
    $.ajax({
      url: url + "master/pelanggan/datafiltermstpelanggan",
      method: "GET",
      dataType: "json",
      success: function (data) {
        $("#companyname").empty();
        $("#companyname").append(
          '<option value="" selected>Pilih Perusahaan</option>'
        );
        data.forEach((pelanggan) => {
          $("#companyname").append(
            `<option value="${pelanggan.cust_id}" 
                                   alamat="${pelanggan.address}" 
                                   namapic="${pelanggan.pic_name}"
                                   email="${pelanggan.email}"
                                   notelp="${pelanggan.phone_no}">
                                   ${pelanggan.cust_name}
                              </option>`
          );
        });
        $("#companyname").select2({
          placeholder: "Pilih Perusahaan",
          allowClear: true,
        });

        $("#companyname").on("change", function () {
          const selectedOption = $(this).find("option:selected");
          const alamat = selectedOption.attr("alamat");
          const namapic = selectedOption.attr("namapic");
          const email = selectedOption.attr("email");
          const notelp = selectedOption.attr("notelp");

          $("#companyaddress").val(alamat);
          $("#companypic").val(namapic);
          $("#email").val(email);
          $("#telpno").val(notelp);
        });
      },
      error: function (xhr, status, error) {
        console.error("Error fetching data pelanggan:", error);
        alert("Gagal memuat data pelanggan.");
      },
    });

    // Fungsi untuk membatasi input hanya angka
    function allowOnlyNumbers(event) {
      const charCode = event.which ? event.which : event.keyCode;
      if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        event.preventDefault();
      }
    }

    // Terapkan pembatasan pada field yang memerlukan input angka
    $("#telpno").on("keypress", allowOnlyNumbers);
    $("#contractamt").on("keypress", allowOnlyNumbers);
    $("#revenueamt").on("keypress", allowOnlyNumbers);
    $("#costplanamt").on("keypress", allowOnlyNumbers);

    // Mencegah paste teks yang tidak valid
    $("#telpno, #contractamt, #revenueamt, #costplanamt").on(
      "paste",
      function (event) {
        event.preventDefault();
      }
    );

    document
      .getElementById("formProyek")
      .addEventListener("submit", function (e) {
        e.preventDefault(); // Mencegah submit langsung
        Swal.fire({
          title: "Anda yakin?",
          text: "Data proyek akan disimpan!",
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
  } else if (window.location.pathname === "/proyek/pembaruandata/index") {
    data_proyek();

    // Fungsi untuk menampilkan tabel proyek
    function data_proyek() {
      // Tambahkan kelas CSS ke elemen #tabel_daftar_proyek
      $("#tabel_daftar_proyek").addClass("table-bordered table-sm");
      $.ajax({
        type: "POST",
        url: url + "proyek/pembaruandata/dataproyek",
        async: true,
        data: {},
        dataType: "json",
        success: function (data) {
          // Inisialisasi Tabulator
          var table = new Tabulator("#tabel_daftar_proyek", {
            data: data,
            // movableColumns: true,
            // layout: "fitColumns",
            height: "500px",
            frozenColumns: true,
            pagination: "local",
            paginationSize: 20,
            paginationSizeSelector: [10, 20, 30],
            columns: [
              {
                title: "Aksi",
                headerHozAlign: "center",
                hozAlign: "center",
                frozen: true,
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
              {
                title: "No. WBS",
                field: "wbs_no",
                headerHozAlign: "center",
                hozAlign: "center",
                headerFilter: "input",
                frozen: true,
              },
              {
                title: "Nama Pekerjaan",
                field: "job_name",
                headerHozAlign: "center",
                headerFilter: "input",
              },
              {
                title: "Perusahaan",
                field: "company_name",
                headerHozAlign: "center",
                headerFilter: "input",
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
    function showUpdateModal(id) {
      // Fetch data proyek berdasarkan id
      $.getJSON(url + `proyek/pembaruandata/getproyek/${id}`, function (data) {
        if (data) {
          // Isi form dengan data awal
          $("#jobstartdate").val(data.job_start_date).trigger("change");
          $("#jobenddate").val(data.job_finish_date).trigger("change");
          $("#jobtotaltime").val(data.job_tot_time).trigger("change");
          $("#progressjob").val(data.progress).trigger("change"); // Set nilai awal
          $("#progressjob").trigger("select2:select"); // Sinkronkan dengan Select2
          $("#invoicesenddate").val(data.invoice_send_date).trigger("change");
          $("#invoicereceivedate")
            .val(data.invoice_receive_date)
            .trigger("change");
          $("#invoicereceivename")
            .val(data.invoice_receive_name)
            .trigger("change");
          $("#revenueamt").val(data.revenue_amt).trigger("change");

          // Fungsi untuk menghitung total hari
          function calculateTotalDays() {
            const startDate = new Date(
              document.getElementById("jobstartdate").value
            );
            const endDate = new Date(
              document.getElementById("jobenddate").value
            );

            if (!isNaN(startDate) && !isNaN(endDate) && startDate <= endDate) {
              const timeDiff = endDate - startDate;
              let dayDiff = Math.floor(timeDiff / (1000 * 60 * 60 * 24)); // Hitung hari penuh
              if (timeDiff === 0) {
                dayDiff = 1; // Jika sama, set ke 1 hari
              } else {
                dayDiff += 1; // Tambah 1 hari untuk menyertakan hari terakhir
              }
              $("#jobtotaltime").val(dayDiff);
            } else {
              $("#jobtotaltime").val(""); // Kosongkan jika tanggal tidak valid
            }
          }

          // Tambahkan event listener untuk perubahan tanggal
          $("#jobstartdate").on("change", calculateTotalDays);
          $("#jobenddate").on("change", calculateTotalDays);

          // Tambahkan input tersembunyi untuk id
          $("#formUpdateProyek").append(
            `<input type="hidden" name="id" value="${id}">`
          );

          // Tampilkan modal
          $("#updateProyekModal").modal("show");

          // Event submit form
          $("#formUpdateProyek")
            .off("submit")
            .on("submit", function (e) {
              e.preventDefault();
              Swal.fire({
                title: "Anda yakin?",
                text: "Data Proyek akan diperbarui!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, simpan!",
              }).then((result) => {
                if (result.isConfirmed) {
                  $.ajax({
                    type: "POST",
                    url: url + "proyek/pembaruandata/updatedataproyek",
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function (response) {
                      if (response.status === "success") {
                        Swal.fire(
                          "Berhasil!",
                          response.message,
                          "success"
                        ).then(() => {
                          $("#updateProyekModal").modal("hide");
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
  }
});
