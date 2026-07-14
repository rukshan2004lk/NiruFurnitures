/**
 * NiRu-Furnitures — admin.js
 * Admin panel interactivity:
 *  - Sidebar toggle + persist
 *  - Sales & revenue charts (Chart.js)
 *  - Image preview for product upload
 *  - Confirm delete dialogs
 *  - DataTable search/sort
 *  - Admin toast helper
 */

"use strict";

document.addEventListener("DOMContentLoaded", () => {
  initSidebar();
  initAdminImagePreview();
  initConfirmDelete();
  initAdminTableSearch();
  initTooltips();
  initAdminCharts();
});

// ── Sidebar Toggle ────────────────────────────────────────────
function initSidebar() {
  const toggle = document.getElementById("sidebarToggle");
  const sidebar = document.getElementById("adminSidebar");
  const overlay = document.getElementById("sidebarOverlay");
  const layout = document.getElementById("adminLayout");
  if (!toggle || !sidebar) return;

  const COLLAPSED_KEY = "niru_sidebar_collapsed";
  const isMobile = () => window.innerWidth < 992;

  // Restore collapsed state on desktop
  if (!isMobile() && localStorage.getItem(COLLAPSED_KEY) === "1") {
    sidebar.classList.add("collapsed");
    layout && layout.classList.add("sidebar-collapsed");
  }

  toggle.addEventListener("click", () => {
    if (isMobile()) {
      // Mobile: slide in/out
      const isOpen = sidebar.classList.toggle("open");
      overlay && overlay.classList.toggle("active", isOpen);
    } else {
      // Desktop: collapse/expand
      const isCollapsed = sidebar.classList.toggle("collapsed");
      layout && layout.classList.toggle("sidebar-collapsed", isCollapsed);
      localStorage.setItem(COLLAPSED_KEY, isCollapsed ? "1" : "0");
    }
  });

  // Close on overlay click (mobile)
  overlay &&
    overlay.addEventListener("click", () => {
      sidebar.classList.remove("open");
      overlay.classList.remove("active");
    });

  // Close sidebar when a nav link is clicked on mobile
  sidebar.querySelectorAll(".sidebar-link").forEach((link) => {
    link.addEventListener("click", () => {
      if (isMobile()) {
        sidebar.classList.remove("open");
        overlay && overlay.classList.remove("active");
      }
    });
  });

  // Recalculate on resize
  window.addEventListener("resize", () => {
    if (!isMobile()) {
      sidebar.classList.remove("open");
      overlay && overlay.classList.remove("active");
    }
  });
}

// ── Product Image Preview ─────────────────────────────────────
function initAdminImagePreview() {
  document
    .querySelectorAll('input[type="file"][data-preview]')
    .forEach((input) => {
      const previewId = input.dataset.preview;
      const preview = document.getElementById(previewId);
      if (!preview) return;

      input.addEventListener("change", () => {
        const file = input.files[0];
        if (!file || !file.type.startsWith("image/")) return;

        const reader = new FileReader();
        reader.onload = (e) => {
          preview.src = e.target.result;
          preview.style.display = "block";
          preview.closest(".img-preview-box")?.classList.add("has-image");
        };
        reader.readAsDataURL(file);
      });
    });
}

// ── Confirm Delete ────────────────────────────────────────────
function initConfirmDelete() {
  document.querySelectorAll("[data-confirm]").forEach((el) => {
    el.addEventListener("click", (e) => {
      const msg = el.dataset.confirm || "Are you sure you want to delete this?";
      if (!window.confirm(msg)) e.preventDefault();
    });
  });
}

// ── Admin Table Live Search ───────────────────────────────────
function initAdminTableSearch() {
  document.querySelectorAll("[data-table-search]").forEach((input) => {
    const tableId = input.dataset.tableSearch;
    const table = document.getElementById(tableId);
    if (!table) return;

    input.addEventListener("input", () => {
      const query = input.value.toLowerCase();
      table.querySelectorAll("tbody tr").forEach((row) => {
        row.style.display = row.textContent.toLowerCase().includes(query)
          ? ""
          : "none";
      });
    });
  });
}

// ── Tooltips ──────────────────────────────────────────────────
function initTooltips() {
  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((el) => {
    new bootstrap.Tooltip(el, { trigger: "hover" });
  });
}

// ── Admin Charts ──────────────────────────────────────────────
function initAdminCharts() {
  initRevenueChart();
  initOrderStatusChart();
  initTopProductsChart();
}

function initRevenueChart() {
  const canvas = document.getElementById("revenueChart");
  if (!canvas || typeof Chart === "undefined") return;

  const labels = JSON.parse(canvas.dataset.labels || "[]");
  const revenue = JSON.parse(canvas.dataset.revenue || "[]");
  const orders = JSON.parse(canvas.dataset.orders || "[]");

  new Chart(canvas, {
    type: "line",
    data: {
      labels,
      datasets: [
        {
          label: "Revenue (Rs.)",
          data: revenue,
          borderColor: "hsl(28, 65%, 42%)",
          backgroundColor: "hsla(28, 65%, 42%, .10)",
          fill: true,
          tension: 0.4,
          pointRadius: 4,
          pointHoverRadius: 7,
          borderWidth: 2.5,
          yAxisID: "y",
        },
        {
          label: "Orders",
          data: orders,
          borderColor: "hsl(210, 90%, 52%)",
          backgroundColor: "transparent",
          tension: 0.4,
          pointRadius: 4,
          pointHoverRadius: 7,
          borderWidth: 2,
          borderDash: [5, 3],
          yAxisID: "y1",
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: { mode: "index", intersect: false },
      plugins: {
        legend: {
          position: "top",
          labels: { font: { family: "Inter", size: 12 }, usePointStyle: true },
        },
        tooltip: {
          callbacks: {
            label: (ctx) => {
              if (ctx.dataset.yAxisID === "y")
                return " Rs. " + Number(ctx.parsed.y).toLocaleString();
              return " " + ctx.parsed.y + " orders";
            },
          },
        },
      },
      scales: {
        x: {
          grid: { display: false },
          ticks: { font: { family: "Inter", size: 11 } },
        },
        y: {
          position: "left",
          grid: { color: "hsl(220,12%,92%)" },
          ticks: {
            font: { family: "Inter", size: 11 },
            callback: (v) => "Rs. " + v.toLocaleString(),
          },
        },
        y1: {
          position: "right",
          grid: { drawOnChartArea: false },
          ticks: { font: { family: "Inter", size: 11 } },
        },
      },
    },
  });
}

function initOrderStatusChart() {
  const canvas = document.getElementById("adminOrderStatusChart");
  if (!canvas || typeof Chart === "undefined") return;

  const labels = JSON.parse(canvas.dataset.labels || "[]");
  const values = JSON.parse(canvas.dataset.values || "[]");

  new Chart(canvas, {
    type: "doughnut",
    data: {
      labels,
      datasets: [
        {
          data: values,
          backgroundColor: [
            "hsl(38,92%,50%)",
            "hsl(210,90%,52%)",
            "hsl(280,70%,55%)",
            "hsl(142,72%,38%)",
            "hsl(0,72%,50%)",
          ],
          borderWidth: 3,
          borderColor: "#fff",
          hoverOffset: 8,
        },
      ],
    },
    options: {
      cutout: "70%",
      plugins: {
        legend: {
          position: "bottom",
          labels: {
            font: { family: "Inter", size: 12 },
            usePointStyle: true,
            padding: 16,
          },
        },
      },
    },
  });
}

function initTopProductsChart() {
  const canvas = document.getElementById("topProductsChart");
  if (!canvas || typeof Chart === "undefined") return;

  const labels = JSON.parse(canvas.dataset.labels || "[]");
  const values = JSON.parse(canvas.dataset.values || "[]");

  new Chart(canvas, {
    type: "bar",
    data: {
      labels,
      datasets: [
        {
          label: "Units Sold",
          data: values,
          backgroundColor: "hsl(28, 65%, 70%)",
          borderRadius: 6,
          borderSkipped: false,
        },
      ],
    },
    options: {
      indexAxis: "y",
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
      },
      scales: {
        x: {
          grid: { color: "hsl(220,12%,92%)" },
          ticks: { font: { family: "Inter", size: 11 } },
        },
        y: {
          grid: { display: false },
          ticks: { font: { family: "Inter", size: 11 } },
        },
      },
    },
  });
}
