$(document).ready(function () {
  if (window.location.pathname == "/beranda") {
    const ctx = document.getElementById("pencapaian_cab").getContext("2d");

    // Nilai target dan realisasi
    const targetValue = 1000000;
    const actualValue = 750000;

    // Data untuk doughnut chart
    const data = {
      labels: ["Realisasi", "Sisa"],
      datasets: [
        {
          data: [actualValue, targetValue - actualValue],
          backgroundColor: ["#36A2EB", "#E0E0E0"],
          borderWidth: 0,
          cutout: "90%", // Membuat doughnut tipis seperti gauge
          circumference: 180, // Setengah lingkaran
          rotation: 270, // Memulai dari posisi atas
        },
      ],
    };

    // Plugin untuk menggambar jarum
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
        const cy = chart.getDatasetMeta(0).data[0].y; // Posisi pusat Y untuk Chart.js v4.x

        // Gambar jarum dengan gaya lebih smooth dan profesional
        ctx.translate(cx, cy);
        ctx.rotate(angle);
        ctx.beginPath();
        ctx.moveTo(0, -3); // Lebar pangkal lebih ramping
        ctx.lineTo(height / 2 - 10, 0); // Panjang jarum mendekati tepi chart
        ctx.lineTo(0, 3);
        ctx.closePath();

        // Tambahkan gradasi untuk jarum
        const gradient = ctx.createLinearGradient(0, -3, height / 2 - 10, 0);
        gradient.addColorStop(0, "#FF5555"); // Warna pangkal
        gradient.addColorStop(1, "#FF0000"); // Warna ujung
        ctx.fillStyle = gradient;
        ctx.fill();

        // Tambahkan efek garis tepi halus
        ctx.strokeStyle = "#000000";
        ctx.lineWidth = 0.5;
        ctx.stroke();
        ctx.restore();

        // Gambar titik tengah dengan efek 3D
        ctx.beginPath();
        ctx.arc(cx, cy, 6, 0, Math.PI * 2);
        ctx.fillStyle = "#333";
        ctx.fill();

        // Tambahkan highlight untuk efek kilau
        ctx.beginPath();
        ctx.arc(cx - 2, cy - 2, 2, 0, Math.PI * 2);
        ctx.fillStyle = "rgba(255, 255, 255, 0.7)";
        ctx.fill();
        ctx.restore();

        // Tambahkan bayangan untuk kesan 3D
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

        // Teks nilai realisasi
        ctx.font = "16px Arial";
        ctx.fillStyle = "#000";
        ctx.textAlign = "center";
        ctx.fillText(actualValue.toLocaleString(), cx, cy + 30);
      },
    };

    // Konfigurasi chart
    const config = {
      type: "doughnut",
      data: data,
      options: {
        plugins: {
          legend: { display: false },
          tooltip: { enabled: false },
        },
        aspectRatio: 2, // Mengatur rasio lebar-tinggi
        animation: {
          duration: 1000, // Animasi halus selama 1 detik
          easing: "easeOutQuart", // Efek animasi profesional
        },
      },
      plugins: [gaugeNeedle],
    };

    // Inisialisasi chart
    new Chart(ctx, config);
  }
});
