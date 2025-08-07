$(document).ready(function () {
  if (window.location.pathname == "/beranda") {
    // Deklarasi elemen filter
    const $pmfilter = $("#pmfilter");
    const $tahun = $("#tahunfilter");

    // Mengisi dropdown Project Manager dengan data dari server
    $.getJSON(url + "master/pminspector/filterpminspector", (response) => {
      $pmfilter.empty();

      if (Array.isArray(response) && response.length > 0) {
        let hasSemuaPersonil = response.some(
          (pm) => pm.nik === null && pm.emp_name === "SEMUA PERSONIL"
        );
        if (!hasSemuaPersonil) {
          response.unshift({ nik: null, emp_name: "SEMUA PERSONIL" });
        }

        $pmfilter.append(
          response.map((pm) => {
            const label =
              pm.nik !== null ? `${pm.nik} - ${pm.emp_name}` : pm.emp_name;
            const value = pm.nik !== null ? pm.nik : "";
            return `<option value="${value}">${label}</option>`;
          })
        );

        $pmfilter.select2({
          allowClear: true,
        });

        $pmfilter.val("").trigger("change.select2");
      } else {
        console.warn("Data dari server tidak valid atau kosong:", response);
        $pmfilter.append('<option value="">SEMUA PERSONIL</option>');
        $pmfilter.select2({
          allowClear: true,
        });
        $pmfilter.val("").trigger("change.select2");
      }
    }).fail(function (jqXHR, textStatus, errorThrown) {
      console.error("Error fetching pmfilter data:", textStatus, errorThrown);
      $pmfilter.empty().append('<option value="">Error memuat data</option>');
      $pmfilter.select2({
        allowClear: true,
      });
    });

    // Variabel untuk menyimpan instance grafik
    let revenueChartInstance = null;
    let budgetChartInstance = null;
    //
    let countProjectChartInstance = null;
    let prsamtRealChartInstance = null;
    //
    let prsPaymentChartInstance = null;

    // Fungsi untuk mendapatkan semua nilai filter saat ini
    function getFilterValues() {
      var pmfilter = $pmfilter.val() || ""; // Default ke "" jika undefined
      var tahun = $tahun.val() || date("Y"); // Default ke tahun berjalan jika undefined
      console.log("Filters:", { pmfilter, tahun }); // Debugging
      return {
        pmfilter,
        tahun,
      };
    }

    // Fungsi untuk membuat banner dashboard
    function banner_total_dashboard(filters) {
      $.ajax({
        url: url + "beranda/data/getdatagrafikproyek",
        type: "POST",
        data: filters,
        dataType: "json",
        success: function (data) {
          // Fungsi untuk memformat angka dengan separator ribuan (titik) dan desimal (koma)
          function formatNumber(num) {
            if (!num && num !== 0) return "N/A";
            num = Number(num);
            if (isNaN(num)) return "N/A";
            const parts = num.toString().split(".");
            let integerPart = parts[0];
            let decimalPart = parts[1] ? parts[1].padEnd(2, "0") : "00";
            integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            return decimalPart ? `${integerPart},${decimalPart}` : integerPart;
          }

          $("#totalcontract").text(formatNumber(data[0].contract_tot_year));
          $("#totalrevenue").text(formatNumber(data[0].revenue_tot_year));
          $("#totalproject").text(data[0].project_tot_year);
        },
        error: function (xhr, status, error) {
          console.error("Error fetching chart data:", {
            status,
            error,
            response: xhr.responseText,
          });
        },
      });
    }

    // Fungsi untuk membuat banner anggaran dashboard
    function banner_anggaran_dashboard(filters) {
      $.ajax({
        url: url + "beranda/data/getdatagrafikanggaran",
        type: "POST",
        data: filters,
        dataType: "json",
        success: function (data) {
          // Fungsi untuk memformat angka dengan separator ribuan (titik) dan desimal (koma)
          function formatNumber(num) {
            if (!num && num !== 0) return "N/A";
            num = Number(num);
            if (isNaN(num)) return "N/A";
            const parts = num.toString().split(".");
            let integerPart = parts[0];
            let decimalPart = parts[1] ? parts[1].padEnd(2, "0") : "00";
            integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            return decimalPart ? `${integerPart},${decimalPart}` : integerPart;
          }

          $("#totalbudget").text(formatNumber(data[0].budget_tot_year));
          $("#totalrealbudget").text(formatNumber(data[0].real_tot_year));
        },
        error: function (xhr, status, error) {
          console.error("Error fetching chart data:", {
            status,
            error,
            response: xhr.responseText,
          });
        },
      });
    }

    // Fungsi untuk membuat grafik persentase pembayaran invoice proyek
    function grafik_prs_pembayaran(filters) {
      // Hancurkan grafik yang ada jika ada
      if (prsPaymentChartInstance) {
        prsPaymentChartInstance.destroy();
      }

      // Atur ulang tinggi canvas secara eksplisit
      const canvas = document.getElementById("prcPaymentChart");
      canvas.height = 225; // Tetapkan tinggi canvas

      $.ajax({
        url: url + "beranda/data/getdatapembayaran",
        type: "POST",
        data: filters,
        dataType: "json",
        success: function (data) {
          console.log("Data received:", data); // Debugging
          // Asumsikan data adalah array dengan total agregat, ambil baris pertama
          const item = data[0] || {}; // Ambil data pertama jika ada
          const prsPayment = item.prs_payment || 0; // Persentase pembayaran
          const prsNotPayment = item.prs_not_payment || 0; // Persentase tidak dibayar
          const paymentTot = item.payment_tot || 0; // Nominal pembayaran
          const notPaymentTot = item.not_payment_tot || 0; // Nominal tidak dibayar

          // Dapatkan konteks canvas
          const ctx = canvas.getContext("2d");

          // Buat grafik baru
          prsPaymentChartInstance = new Chart(ctx, {
            type: "doughnut", // Ubah ke tipe doughnut
            data: {
              labels: ["Pembayaran", "Belum Dibayar"], // Label untuk setiap segmen
              datasets: [
                {
                  data: [prsPayment, prsNotPayment], // Data persentase
                  backgroundColor: [
                    "rgba(75, 192, 192, 0.6)",
                    "rgba(255, 99, 132, 0.6)",
                  ], // Warna segmen
                  borderWidth: 1, // Ketebalan border segmen
                },
              ],
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              cutout: "80%", // Mengurangi ketebalan dengan meningkatkan ukuran lubang (0.7 atau 70%)
              scales: {
                // Skala tidak diperlukan untuk doughnut
              },
              plugins: {
                tooltip: {
                  callbacks: {
                    label: function (context) {
                      let label = context.label || "";
                      if (context.label === "Pembayaran") {
                        label += `: ${
                          context.parsed
                        }% (Rp ${paymentTot.toLocaleString("id-ID")})`;
                      } else if (context.label === "Belum Dibayar") {
                        label += `: ${
                          context.parsed
                        }% (Rp ${notPaymentTot.toLocaleString("id-ID")})`;
                      }
                      return label;
                    },
                  },
                },
                legend: {
                  position: "top", // Posisi legenda
                },
              },
            },
          });
        },
        error: function (xhr, status, error) {
          console.error("Error fetching chart data:", {
            status,
            error,
            response: xhr.responseText,
          });
        },
      });
    }

    // Fungsi untuk membuat total grafik line proyek
    function grafik_total_proyek(filters) {
      // Hancurkan grafik yang ada jika ada
      if (countProjectChartInstance) {
        countProjectChartInstance.destroy();
      }

      // Atur ulang tinggi canvas secara eksplisit
      const canvas = document.getElementById("countProjectChart");
      canvas.height = 200; // Tetapkan tinggi canvas

      $.ajax({
        url: url + "beranda/data/getdatagrafikproyek",
        type: "POST",
        data: filters,
        dataType: "json",
        success: function (data) {
          console.log("Data received:", data); // Debugging
          // Ekstrak label dan data dari respons
          const labels = data.map((item) => item.month);
          const projectData = data.map((item) => item.project_tot || 0);

          // Dapatkan konteks canvas
          const ctx = canvas.getContext("2d");

          // Buat grafik baru
          countProjectChartInstance = new Chart(ctx, {
            type: "line",
            data: {
              labels: labels,
              datasets: [
                {
                  type: "line",
                  label: "Jumlah Proyek",
                  data: projectData,
                  borderColor: "rgb(75, 192, 192)",
                  // tension: 0.1,
                  lineTension: 0.5,
                  borderWidth: 3,
                  borderRadius: 10,
                  borderSkipped: false,
                  fill: false, // Set
                },
              ],
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              scales: {
                y: {
                  beginAtZero: true,
                },
              },
              plugins: {
                tooltip: {
                  callbacks: {
                    label: function (context) {
                      let label = "";
                      context.dataset.type === "line";
                      label = "jumlah proyek: " + (context.parsed.y || 0);
                      return label;
                    },
                  },
                },
              },
            },
          });
        },
        error: function (xhr, status, error) {
          console.error("Error fetching chart data:", {
            status,
            error,
            response: xhr.responseText,
          });
        },
      });
    }

    // Fungsi untuk membuat realisasi grafik line
    function grafik_prs_real(filters) {
      // Hancurkan grafik yang ada jika ada
      if (prsamtRealChartInstance) {
        prsamtRealChartInstance.destroy();
      }

      // Atur ulang tinggi canvas secara eksplisit
      const canvas = document.getElementById("prsRealAmtChart");
      canvas.height = 200; // Tetapkan tinggi canvas

      $.ajax({
        url: url + "beranda/data/getdatagrafikanggaran",
        type: "POST",
        data: filters,
        dataType: "json",
        success: function (data) {
          console.log("Data received:", data); // Debugging
          // Ekstrak label dan data dari respons
          const labels = data.map((item) => item.month);
          const prsRealData = data.map((item) => item.prs_real || 0);

          // Dapatkan konteks canvas
          const ctx = canvas.getContext("2d");

          // Buat grafik baru
          prsamtRealChartInstance = new Chart(ctx, {
            type: "line",
            data: {
              labels: labels,
              datasets: [
                {
                  type: "line",
                  label: "% Realisasi",
                  data: prsRealData,
                  borderColor: "rgb(255, 131, 0)",
                  // tension: 0.1,
                  lineTension: 0.5,
                  borderWidth: 3,
                  borderRadius: 10,
                  borderSkipped: false,
                  fill: false, // Set
                },
              ],
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              scales: {
                y: {
                  beginAtZero: true,
                },
              },
              plugins: {
                tooltip: {
                  callbacks: {
                    label: function (context) {
                      let label = "";
                      context.dataset.type === "line";
                      label = "% Realisasi: " + (context.parsed.y || 0) + "%";
                      return label;
                    },
                  },
                },
              },
            },
          });
        },
        error: function (xhr, status, error) {
          console.error("Error fetching chart data:", {
            status,
            error,
            response: xhr.responseText,
          });
        },
      });
    }

    // Fungsi untuk membuat grafik proyek
    function grafik_proyek(filters) {
      // Hancurkan grafik yang ada jika ada
      if (revenueChartInstance) {
        revenueChartInstance.destroy();
      }

      // Atur ulang tinggi canvas secara eksplisit
      const canvas = document.getElementById("revenueChart");
      canvas.height = 350; // Tetapkan tinggi canvas

      $.ajax({
        url: url + "beranda/data/getdatagrafikproyek",
        type: "POST",
        data: filters,
        dataType: "json",
        success: function (data) {
          console.log("Data received:", data); // Debugging
          // Ekstrak label dan data dari respons
          const labels = data.map((item) => item.month);
          const revenueData = data.map((item) => item.revenue_tot || 0);
          const paymentData = data.map((item) => item.payment_tot || 0);

          // Dapatkan konteks canvas
          const ctx = canvas.getContext("2d");

          // Buat grafik baru
          revenueChartInstance = new Chart(ctx, {
            type: "bar",
            data: {
              labels: labels,
              datasets: [
                {
                  type: "bar",
                  label: "Pendapatan",
                  data: revenueData,
                  backgroundColor: "rgba(105, 255, 71, 0.2)",
                  borderColor: "rgb(105, 255, 71)",
                  borderWidth: 1,
                },
                {
                  type: "bar",
                  label: "Pembayaran",
                  data: paymentData,
                  backgroundColor: "rgba(255, 131, 0, 0.2)",
                  borderColor: "rgb(255, 131, 0)",
                  borderWidth: 1,
                },
              ],
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              scales: {
                y: {
                  beginAtZero: true,
                },
              },
              plugins: {
                tooltip: {
                  callbacks: {
                    label: function (context) {
                      let label = "";
                      if (context.dataset.type === "bar") {
                        if (context.dataset.label === "Pendapatan") {
                          label =
                            "pendapatan : Rp " +
                            (context.parsed.y || 0).toLocaleString();
                        } else if (context.dataset.label === "Pembayaran") {
                          label =
                            "pembayaran : Rp " +
                            (context.parsed.y || 0).toLocaleString();
                        }
                      }
                      return label;
                    },
                  },
                },
              },
            },
          });
        },
        error: function (xhr, status, error) {
          console.error("Error fetching chart data:", {
            status,
            error,
            response: xhr.responseText,
          });
        },
      });
    }

    // Fungsi untuk membuat grafik anggaran proyek
    function grafik_anggaran_proyek(filters) {
      // Hancurkan grafik yang ada jika ada
      if (budgetChartInstance) {
        budgetChartInstance.destroy();
      }

      // Atur ulang tinggi canvas secara eksplisit
      const canvas = document.getElementById("budgetChart");
      canvas.height = 350; // Tetapkan tinggi canvas

      $.ajax({
        url: url + "beranda/data/getdatagrafikanggaran",
        type: "POST",
        data: filters,
        dataType: "json",
        success: function (data) {
          console.log("Data received:", data); // Debugging
          // Ekstrak label dan data dari respons
          const labels = data.map((item) => item.month);
          const budgetData = data.map((item) => item.budget_tot || 0);
          const realData = data.map((item) => item.real_tot || 0);
          const realDropData = data.map((item) => item.real_drop_tot || 0);

          // Dapatkan konteks canvas
          const ctx = canvas.getContext("2d");

          // Buat grafik baru
          budgetChartInstance = new Chart(ctx, {
            type: "bar",
            data: {
              labels: labels,
              datasets: [
                {
                  type: "bar",
                  label: "Anggaran",
                  data: budgetData,
                  backgroundColor: "rgba(105, 255, 71, 0.2)",
                  borderColor: "rgb(105, 255, 71)",

                  borderWidth: 1,
                },
                {
                  type: "bar",
                  label: "Realisasi Biaya",
                  data: realData,
                  backgroundColor: "rgba(255, 99, 132, 0.2)",
                  borderColor: "rgb(255, 99, 132)",
                  borderWidth: 1,
                },
                {
                  type: "bar",
                  label: "Dropping",
                  data: realDropData,
                  backgroundColor: "rgba(54, 162, 235, 0.2)",
                  borderColor: "rgb(54, 162, 235)",
                  borderWidth: 1,
                },
              ],
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              scales: {
                y: {
                  beginAtZero: true,
                },
              },
              plugins: {
                tooltip: {
                  callbacks: {
                    label: function (context) {
                      let label = "";
                      if (context.dataset.type === "bar") {
                        if (context.dataset.label === "Anggaran") {
                          label =
                            "anggaran : Rp " +
                            (context.parsed.y || 0).toLocaleString();
                        } else if (
                          context.dataset.label === "Realisasi Biaya"
                        ) {
                          label =
                            "realisasi biaya : Rp " +
                            (context.parsed.y || 0).toLocaleString();
                        } else if (context.dataset.label === "Dropping") {
                          label =
                            "dropping : Rp " +
                            (context.parsed.y || 0).toLocaleString();
                        }
                      }
                      return label;
                    },
                  },
                },
              },
            },
          });
        },
        error: function (xhr, status, error) {
          console.error("Error fetching chart data:", {
            status,
            error,
            response: xhr.responseText,
          });
        },
      });
    }

    // Event handler untuk tahun
    $tahun.on("change", function () {
      var filters = getFilterValues();
      banner_total_dashboard(filters);
      banner_anggaran_dashboard(filters);
      grafik_prs_pembayaran(filters);
      grafik_total_proyek(filters);
      grafik_prs_real(filters);
      grafik_proyek(filters);
      grafik_anggaran_proyek(filters);
    });

    // Event handler untuk pmfilter
    $pmfilter.on("change", function () {
      var filters = getFilterValues();
      banner_total_dashboard(filters);
      banner_anggaran_dashboard(filters);
      grafik_prs_pembayaran(filters);
      grafik_total_proyek(filters);
      grafik_prs_real(filters);
      grafik_proyek(filters);
      grafik_anggaran_proyek(filters);
    });

    // Inisialisasi awal dengan kedua filter
    var filters = getFilterValues();
    banner_total_dashboard(filters);
    banner_anggaran_dashboard(filters);
    grafik_prs_pembayaran(filters);
    grafik_total_proyek(filters);
    grafik_prs_real(filters);
    grafik_proyek(filters);
    grafik_anggaran_proyek(filters);

    // Set interval untuk memperbarui data setiap 10 menit
    setInterval(function () {
      var filters = getFilterValues();
      banner_total_dashboard(filters);
      banner_anggaran_dashboard(filters);
      grafik_prs_pembayaran(filters);
      grafik_total_proyek(filters);
      grafik_prs_real(filters);
      grafik_proyek(filters);
      grafik_anggaran_proyek(filters);
    }, 600000);
  }
});
