$(document).ready(function () {
  if (window.location.pathname == "/beranda") {
    const ctx = document.getElementById("pencapaian_cab").getContext("2d");

    // Fetch data from API
    $.ajax({
      type: "POST",
      url: url + "beranda/data/getdatapencapaiancab",
      async: true,
      data: {},
      dataType: "json",
      success: function (response) {
        console.log("Data dari API:", response);

        // Extract values from API response with defaults
        const targetValue = response.tot_amt_pipeline; // Default to 1,000,000
        const actualValue = response.tot_amt_real; // Default to 750,000
        const percentage = response.prs_achiev; // Default to 75%

        // Data for doughnut chart
        const data = {
          labels: ["Realisasi", "Sisa"],
          datasets: [
            {
              data: [actualValue, targetValue - actualValue],
              backgroundColor: ["#36A2EB", "#E0E0E0"],
              borderWidth: 0,
              borderRadius: 10, // Rounded ends for the arc
              cutout: "90%", // Thin doughnut for gauge effect
              circumference: 180, // Semi-circle
              rotation: 270, // Start from top
            },
          ],
        };

        // Plugin to draw the needle
        const gaugeNeedle = {
          id: "gaugeNeedle",
          afterDatasetDraw(chart) {
            const {
              ctx,
              chartArea: { width, height },
            } = chart;
            ctx.save();

            const needleValue = actualValue;
            const dataTotal = targetValue;
            const angle = Math.PI + (Math.PI * needleValue) / dataTotal;

            const cx = width / 2;
            const cy = chart.getDatasetMeta(0).data[0].y;

            // Draw needle with smooth styling
            ctx.translate(cx, cy);
            ctx.rotate(angle);
            ctx.beginPath();
            ctx.moveTo(0, -3);
            ctx.lineTo(height / 2 - 10, 0);
            ctx.lineTo(0, 3);
            ctx.closePath();

            const gradient = ctx.createLinearGradient(
              0,
              -3,
              height / 2 - 10,
              0
            );
            gradient.addColorStop(0, "#FF5555");
            gradient.addColorStop(1, "#FF0000");
            ctx.fillStyle = gradient;
            ctx.fill();

            ctx.strokeStyle = "#000000";
            ctx.lineWidth = 0.5;
            ctx.stroke();
            ctx.restore();

            // Draw center point with 3D effect
            ctx.beginPath();
            ctx.arc(cx, cy, 6, 0, Math.PI * 2);
            ctx.fillStyle = "#333";
            ctx.fill();

            // Add highlight
            ctx.beginPath();
            ctx.arc(cx - 2, cy - 2, 2, 0, Math.PI * 2);
            ctx.fillStyle = "rgba(255, 255, 255, 0.7)";
            ctx.fill();
            ctx.restore();

            // Add shadow
            ctx.save();
            ctx.beginPath();
            ctx.arc(cx, cy, 6, 0, Math.PI * 2);
            ctx.shadowColor = "rgba(0, 0, 0, 0.3)";
            ctx.shadowBlur = 5;
            ctx.shadowOffsetX = 2;
            ctx.shadowOffsetY = 2;
            ctx.fillStyle = "#333";
            ctx.fill();
            ctx.restore();

            // Display percentage text
            ctx.font = "bold 16px Arial";
            ctx.fillStyle = "#000";
            ctx.textAlign = "center";
            ctx.fillText(percentage + "%", cx, cy + 30);
          },
        };

        // Chart configuration
        const config = {
          type: "doughnut",
          data: data,
          options: {
            plugins: {
              legend: { display: false },
              tooltip: { enabled: false },
            },
            aspectRatio: 2,
            animation: {
              duration: 1000,
              easing: "easeOutQuart",
            },
            layout: {
              padding: {
                bottom: 20, // Space for percentage text
              },
            },
          },
          plugins: [gaugeNeedle],
        };

        // Initialize chart
        const chart = new Chart(ctx, config);
        chart.canvas.style.height = "250px"; // Set height to 250px
      },
      error: function (xhr, status, err) {
        console.error("Error fetching gauge chart data:", err);
      },
    });

    // Call data_verifikasi_tertunda
    data_verifikasi_tertunda();

    // Function to display verification table
    function data_verifikasi_tertunda() {
      $.ajax({
        type: "POST",
        url: url + "beranda/data/getdaftarverifcab",
        async: true,
        data: {},
        dataType: "json",
        success: function (response) {
          console.log("Data dari API:", response);
          var table = new Tabulator("#tabel_daftar_verifikasi_tertunda", {
            layout: "fitColumns",
            height: "250px",
            responsiveLayout: "collapse",
            pagination: "local",
            paginationSize: 15,
            paginationSizeSelector: [10, 15, 25],
            data: response,
            columns: [
              {
                title: "Nama",
                field: "emp_name",
                headerHozAlign: "center",
                formatter: function (cell, formatterParams, onRendered) {
                  const rowData = cell.getRow().getData();
                  const hasPendingJobs =
                    rowData.tot_job_pipeline > 0 ||
                    rowData.tot_job_plan > 0 ||
                    rowData.tot_job_real > 0;
                  return hasPendingJobs
                    ? `<span style="color: #FF5677;">${cell.getValue()}</span>`
                    : cell.getValue();
                },
              },
              {
                title: "Pipeline",
                field: "tot_job_pipeline",
                headerHozAlign: "center",
                hozAlign: "center",
              },
              {
                title: "Rencana",
                field: "tot_job_plan",
                headerHozAlign: "center",
                hozAlign: "center",
              },
              {
                title: "Realisasi",
                field: "tot_job_real",
                headerHozAlign: "center",
                hozAlign: "center",
              },
            ],
          });
        },
        error: function (xhr, status, err) {
          console.error("Error tabel daftar verifikasi tertunda:", err);
        },
      });
    }
  }
});
