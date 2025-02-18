$(document).ready(function () {
  if (window.location.pathname == "/master/pelanggan/registrasi") {
    // Fetch data kategori pelanggan
    $.ajax({
      url: url + "master/kategoripelanggan",
      method: "GET",
      dataType: "json",
      success: function (data) {
        // Reset options di dropdown
        $("#kategoriPelanggan").empty();

        // Tambahkan opsi default
        $("#kategoriPelanggan").append(
          '<option value="" selected>Pilih Kategori Pelanggan</option>'
        );

        // Tambahkan opsi pelanggan berdasarkan hasil dari backend
        data.forEach((kategoripelanggan) => {
          $("#kategoriPelanggan").append(
            `<option value="${kategoripelanggan.category_id}" 
                 data-flg-pharmacist="${kategoripelanggan.flg_pharmacist}">
                 ${kategoripelanggan.category_name}
        </option>`
          );
        });

        // Inisialisasi Select2 untuk dropdown
        $("#kategoriPelanggan").select2({
          placeholder: "Pilih Kategori Pelanggan",
          allowClear: true,
        });
      },
      error: function (xhr, status, error) {
        console.error("Error fetching data kategori pelanggan:", error);
        alert("Gagal memuat data kategori pelanggan.");
      },
    });

    // Event listener untuk menangkap perubahan pilihan
    $("#kategoriPelanggan").on("change", function () {
      const selectedValue = $(this).val(); // Value dari kategori pelanggan
      const selectedText = $("#kategoriPelanggan option:selected").text(); // Nama kategori pelanggan
      const flgPharmacist = $("#kategoriPelanggan option:selected").data(
        "flg-pharmacist"
      ); // Status pharmacist

      console.log(
        `Value: ${selectedValue}, Text: ${selectedText}, Pharmacist: ${flgPharmacist}`
      );

      // Tampilkan atau sembunyikan form apoteker berdasarkan flg_pharmacist
      if (flgPharmacist === "t") {
        $("#formApoteker").show(); // Tampilkan form apoteker
      } else {
        $("#formApoteker").hide(); // Sembunyikan form apoteker
      }
    });

    // DATA WILAYAH
    // Fetch Provinsi
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

    // Fetch Kota/Kabupaten
    $("#masterprovinsi").on("change", function () {
      const provinceId = $(this).val();
      console.log("Provinsi terpilih: ", provinceId); // Log provinsi terpilih
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

    // Fetch Kecamatan
    $("#masterkota").on("change", function () {
      const provinceId = $("#masterprovinsi").val();
      const cityId = $(this).val();
      console.log("Kota/Kabupaten terpilih: ", cityId); // Log kota/kabupaten terpilih
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

    // Fetch Kelurahan
    $("#masterkecamatan").on("change", function () {
      const provinceId = $("#masterprovinsi").val();
      const cityId = $("#masterkota").val();
      const districtId = $(this).val();
      console.log("Kecamatan terpilih: ", districtId); // Log kecamatan terpilih
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

    // Fetch Kode Pos
    $("#masterkelurahan").on("change", function () {
      const provinceId = $("#masterprovinsi").val();
      const cityId = $("#masterkota").val();
      const districtId = $("#masterkecamatan").val();
      const subdistrictId = $(this).val();
      console.log("Kelurahan terpilih: ", subdistrictId); // Log kelurahan terpilih

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
              console.log("Kode Pos: ", data[0].zip_code); // Log kode pos
            } else {
              $("#kodepos").val("");
            }
          },
        });
      }
    });
  }
});
