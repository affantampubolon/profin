$(document).ready(function () {
  if (window.location.pathname == "/master/pelanggan/registrasi") {
    // Fetch data pelanggan baru (bagian ini tetap sama)
    $.ajax({
      url: url + "master/pelanggan/getdataregis",
      method: "GET",
      dataType: "json",
      success: function (data) {
        $("#masterpelangganbaru").empty();
        $("#masterpelangganbaru").append(
          '<option value="" selected>Pilih Pelanggan</option>'
        );
        data.forEach((pelangganbaru) => {
          $("#masterpelangganbaru").append(
            `<option value="${pelangganbaru.req_no}" 
                 data-cust-name="${pelangganbaru.cust_name}" 
                 data-category-id="${pelangganbaru.category_id}" 
                 data-category-name="${pelangganbaru.catcust_name}" 
                 data-flg-pharmacist="${pelangganbaru.flg_pharmacist}" 
                 data-id="${pelangganbaru.id}">
                 ${pelangganbaru.req_no} - ${pelangganbaru.cust_name}
          </option>`
          );
        });
        $("#masterpelangganbaru").select2({
          placeholder: "Pilih Pelanggan",
          allowClear: true,
        });

        $("#masterpelangganbaru").on("change", function () {
          const selectedOption = $(this).find("option:selected");
          const custName = selectedOption.data("cust-name");
          const categoryId = selectedOption.data("category-id");
          const categoryName = selectedOption.data("category-name");
          const flgPharmacist = selectedOption.data("flg-pharmacist");
          const id = selectedOption.data("id");

          const custCategory =
            categoryId && categoryName ? `${categoryId} - ${categoryName}` : "";
          $("#namaPelanggan").val(custName || "");
          $("#kategoriPelanggan").val(custCategory);
          $("#idPelanggan").val(id || "");

          if (flgPharmacist === true) {
            $("#formApoteker").show();
          } else {
            $("#formApoteker").hide();
          }
        });

        $("#namaPelanggan").val("");
        $("#kategoriPelanggan").val("");
        $("#formApoteker").hide();
      },
      error: function (xhr, status, error) {
        console.error("Error fetching data pelanggan:", error);
        alert("Gagal memuat data pelanggan.");
      },
    });

    // Fetch Provinsi (bagian ini tetap sama)
    $.ajax({
      url: url + "master/area/provinsi",
      method: "GET",
      dataType: "json",
      success: function (data) {
        $("#masterprovinsi")
          .empty()
          .append('<option value="" selected>Pilih Provinsi</option>');
        data.forEach((provinsi) => {
          $("#masterprovinsi").append(
            `<option value="${provinsi.province_id}">${provinsi.name}</option>`
          );
        });
      },
      error: function () {
        alert("Gagal memuat data provinsi");
      },
    });

    // Fetch Kota/Kabupaten (bagian ini tetap sama)
    $("#masterprovinsi").on("change", function () {
      const provinceId = $(this).val();
      $("#masterkota")
        .empty()
        .append('<option value="" selected>Pilih Kota/Kabupaten</option>');
      $("#masterkecamatan, #masterkelurahan")
        .empty()
        .append('<option value="">-</option>');
      $("#kodepos").val("");

      if (provinceId) {
        $.ajax({
          url: url + "master/area/kotakab",
          method: "POST",
          data: { province_id: provinceId },
          dataType: "json",
          success: function (data) {
            data.forEach((kota) => {
              $("#masterkota").append(
                `<option value="${kota.city_id}">${kota.name}</option>`
              );
            });
          },
        });
      }
    });

    // Fetch Kecamatan (bagian ini tetap sama)
    $("#masterkota").on("change", function () {
      const provinceId = $("#masterprovinsi").val();
      const cityId = $(this).val();
      $("#masterkecamatan")
        .empty()
        .append('<option value="" selected>Pilih Kecamatan</option>');
      $("#masterkelurahan").empty().append('<option value="">-</option>');
      $("#kodepos").val("");

      if (cityId) {
        $.ajax({
          url: url + "master/area/kecamatan",
          method: "POST",
          data: { province_id: provinceId, city_id: cityId },
          dataType: "json",
          success: function (data) {
            data.forEach((kecamatan) => {
              $("#masterkecamatan").append(
                `<option value="${kecamatan.district_id}">${kecamatan.name}</option>`
              );
            });
          },
        });
      }
    });

    // Fetch Kelurahan (bagian ini tetap sama)
    $("#masterkecamatan").on("change", function () {
      const provinceId = $("#masterprovinsi").val();
      const cityId = $("#masterkota").val();
      const districtId = $(this).val();
      $("#masterkelurahan")
        .empty()
        .append('<option value="" selected>Pilih Kelurahan</option>');
      $("#kodepos").val("");

      if (districtId) {
        $.ajax({
          url: url + "master/area/kelurahandesa",
          method: "POST",
          data: {
            province_id: provinceId,
            city_id: cityId,
            district_id: districtId,
          },
          dataType: "json",
          success: function (data) {
            data.forEach((kelurahan) => {
              $("#masterkelurahan").append(
                `<option value="${kelurahan.subdistrict_id}">${kelurahan.name}</option>`
              );
            });
          },
        });
      }
    });

    // Fetch Kode Pos (bagian ini tetap sama)
    $("#masterkelurahan").on("change", function () {
      const provinceId = $("#masterprovinsi").val();
      const cityId = $("#masterkota").val();
      const districtId = $("#masterkecamatan").val();
      const subdistrictId = $(this).val();

      if (subdistrictId) {
        $.ajax({
          url: url + "master/area/kodepos",
          method: "POST",
          data: {
            province_id: provinceId,
            city_id: cityId,
            district_id: districtId,
            subdistrict_id: subdistrictId,
          },
          dataType: "json",
          success: function (data) {
            if (data.length > 0) {
              $("#kodepos").val(data[0].zip_code);
            } else {
              $("#kodepos").val("");
            }
          },
        });
      }
    });

    // Submit form
    $("form").on("submit", function (e) {
      e.preventDefault();

      // Daftar field wajib
      const requiredFields = [
        "#alamatPelanggan",
        "#masterprovinsi",
        "#masterkota",
        "#masterkecamatan",
        "#masterkelurahan",
        "#statusPajak",
        "#ktpPelanggan",
        "#namaPemilik",
        "#cekVerifikasi",
      ];
      let isValid = true;

      // Validasi field wajib
      requiredFields.forEach((field) => {
        if (field === "#cekVerifikasi") {
          if (!$(field).is(":checked")) {
            isValid = false;
            $(field).addClass("is-invalid");
          } else {
            $(field).removeClass("is-invalid");
          }
        } else if ($(field).val() === "") {
          isValid = false;
          $(field).addClass("is-invalid");
        } else {
          $(field).removeClass("is-invalid");
        }
      });

      // Validasi tambahan untuk form apoteker jika ditampilkan
      if ($("#formApoteker").is(":visible")) {
        const apotekerFields = [
          "#namaApoteker",
          "#noSipa",
          "#noSia",
          "#edSipa",
          "#edSia",
        ];
        apotekerFields.forEach((field) => {
          if ($(field).val() === "") {
            isValid = false;
            $(field).addClass("is-invalid");
          } else {
            $(field).removeClass("is-invalid");
          }
        });
      }

      if (!isValid) {
        Swal.fire({
          title: "Peringatan",
          text: "Wajib diisi semua field yang bertanda *.",
          icon: "warning",
          confirmButtonText: "OK",
        });
        return;
      }

      // Konfirmasi SweetAlert
      Swal.fire({
        title: "Konfirmasi",
        text: "Apakah Anda yakin menyimpan data?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya, simpan!",
        cancelButtonText: "Batal",
      }).then((result) => {
        if (result.isConfirmed) {
          // Membuat objek formData
          const formData = {
            id: $("#idPelanggan").val(),
            cust_name: $("#namaPelanggan").val()
              ? $("#namaPelanggan").val().toUpperCase()
              : null,
            address: $("#alamatPelanggan").val()
              ? $("#alamatPelanggan").val().toUpperCase()
              : null,
            province_id: $("#masterprovinsi").val(),
            city_id: $("#masterkota").val(),
            district_id: $("#masterkecamatan").val(),
            subdistrict_id: $("#masterkelurahan").val(),
            zip_code: $("#kodepos").val() || null,
            category_id: $("#kategoriPelanggan").val().split(" - ")[0],
            email: $("#emailPelanggan").val() || null,
            phone_no: $("#notelpPelanggan").val() || null,
            tax_status: $("#statusPajak").val(),
            npwp: $("#npwpPelanggan").val() || null,
            siup: $("#siupPelanggan").val() || null,
            cust_name_tax: $("#namanpwpPelanggan").val()
              ? $("#namanpwpPelanggan").val().toUpperCase()
              : null,
            address_tax: $("#alamatnpwpPelanggan").val()
              ? $("#alamatnpwpPelanggan").val().toUpperCase()
              : null,
            id_card: $("#ktpPelanggan").val(),
            construction_type: $("#tipeBangunan").val() || null,
            status_building: $("#hakBangunan").val() || null,
            owner_name: $("#namaPemilik").val()
              ? $("#namaPemilik").val().toUpperCase()
              : null,
            flg_verify_noo: $("#cekVerifikasi").is(":checked") ? 1 : 0,
          };

          // Hanya tambahkan data apoteker jika form apoteker ditampilkan
          if ($("#formApoteker").is(":visible")) {
            formData.pharmacist = $("#namaApoteker").val()
              ? $("#namaApoteker").val().toUpperCase()
              : null;
            formData.sipa = $("#noSipa").val() || null;
            formData.sia = $("#noSia").val() || null;
            formData.exp_date_sia = $("#edSia").val() || null;
            formData.exp_date_sipa = $("#edSipa").val() || null;
          } else {
            formData.pharmacist = null;
            formData.sipa = null;
            formData.sia = null;
            formData.exp_date_sia = null;
            formData.exp_date_sipa = null;
          }

          // Debugging: Log formData untuk memeriksa nilai
          console.log("formData:", formData);

          $.ajax({
            url: url + "/master/pelanggan/updateregispelanggan",
            method: "POST",
            data: formData,
            dataType: "json",
            success: function (response) {
              if (response.success) {
                Swal.fire({
                  title: "Sukses",
                  text: "Data berhasil disimpan",
                  icon: "success",
                  confirmButtonText: "OK",
                }).then(() => {
                  window.location.reload();
                });
              } else {
                Swal.fire({
                  title: "Gagal",
                  text:
                    "Gagal menyimpan data: " +
                    (response.message || "Kesalahan tidak diketahui"),
                  icon: "error",
                  confirmButtonText: "OK",
                });
              }
            },
            error: function (xhr, status, error) {
              console.error("Error saving data:", error);
              let errorMessage = "Terjadi kesalahan saat menyimpan data.";
              if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
              }
              Swal.fire({
                title: "Error",
                text: errorMessage,
                icon: "error",
                confirmButtonText: "OK",
              });
            },
          });
        }
      });
    });
  }
});
