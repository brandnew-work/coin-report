import Chart from "chart.js/auto";
import { initCryptoCharts } from "./lib/cryptoCharts";

document.addEventListener("DOMContentLoaded", () => {
  initCryptoCharts();
});

try {
  const ctx = document.querySelector(".js-chart");

  if (ctx && typeof labels !== "undefined" && typeof values !== "undefined") {
    const data = {
      labels: labels,
      datasets: [
        {
          label: "実質利益（累計）",
          data: values,
          borderColor: "rgba(255, 159, 64, 1)",
          backgroundColor: "rgba(255, 159, 64, 0.1)",
          borderWidth: 2,
          pointRadius: 3,
          tension: 0.1,
          fill: true,
        },
      ],
    };

    const options = {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false,
        },
      },
      scales: {
        x: {},
        y: {
          ticks: {
            callback: (value) => {
              return "$" + value.toLocaleString();
            },
          },
        },
      },
    };

    new Chart(ctx, {
      type: "line",
      data: data,
      options: options,
    });
  }
} catch (e) {
  console.error("Chart initialization error:", e);
}
