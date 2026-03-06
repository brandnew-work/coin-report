import Chart from "chart.js/auto";

const toYmd = (ts) => {
  const d = new Date(ts);
  const y = d.getFullYear();
  const m = String(d.getMonth() + 1).padStart(2, "0");
  const day = String(d.getDate()).padStart(2, "0");
  return `${y}-${m}-${day}`;
};

const fetchMarketChart = async ({ coinId, vs, days }) => {
  // WordPress admin-ajax.php 経由でプロキシ（CORS回避）
  const ajaxUrl =
    (typeof wpApiSettings !== "undefined" && wpApiSettings.ajaxUrl) ||
    "/wp-admin/admin-ajax.php";

  const url = new URL(ajaxUrl, window.location.origin);
  url.searchParams.set("action", "coingecko_proxy");
  url.searchParams.set("coin_id", coinId);
  url.searchParams.set("vs", vs);
  url.searchParams.set("days", days);

  const res = await fetch(url.toString());

  if (!res.ok) {
    throw new Error(`Proxy API error: ${res.status} ${res.statusText}`);
  }

  return res.json();
};

const formatYAxis = (vs) => {
  if (vs === "jpy") return (v) => `¥${Number(v).toLocaleString()}`;
  return (v) => `$${Number(v).toLocaleString()}`;
};

const renderLineChart = (canvas, { labels, values, label, vs }) => {
  const ctx = canvas.getContext("2d");

  // 再初期化対策
  if (canvas._chartInstance) {
    canvas._chartInstance.destroy();
  }

  canvas._chartInstance = new Chart(ctx, {
    type: "line",
    data: {
      labels,
      datasets: [
        {
          label: undefined,
          data: values,
          pointRadius: 0,
          borderWidth: 2,
          tension: 0.1,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: { mode: "index", intersect: false },
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: (ctx) => {
              const y = ctx.parsed.y;
              if (vs === "jpy") return `¥${Number(y).toLocaleString()}`;
              return `$${Number(y).toLocaleString()}`;
            },
          },
        },
      },
      scales: {
        x: { ticks: { maxTicksLimit: 12 } },
        y: { ticks: { callback: formatYAxis(vs) } },
      },
    },
  });
};

export const initCryptoCharts = async (root = document) => {
  const canvases = [...root.querySelectorAll(".js-crypto-chart")];
  if (!canvases.length) return;

  await Promise.all(
    canvases.map(async (canvas) => {
      const coinId = canvas.dataset.coin;
      const label = canvas.dataset.label || coinId;
      const days = canvas.dataset.days || "365";
      const vs = (canvas.dataset.vs || "usd").toLowerCase();

      try {
        const json = await fetchMarketChart({ coinId, vs, days });
        const prices = json.prices || [];
        if (!prices.length) throw new Error(`No price data for ${coinId}`);

        const labels = prices.map(([ts]) => toYmd(ts));
        const values = prices.map(([, price]) => price);

        renderLineChart(canvas, { labels, values, label, vs });
      } catch (e) {
        console.error(e);
        const p = document.createElement("p");
        p.textContent = "チャートデータを取得できませんでした。";
        canvas.replaceWith(p);
      }
    }),
  );
};
