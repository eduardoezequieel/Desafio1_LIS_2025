/**
 * Dashboard (index):
 * - Obtiene todas las transacciones.
 * - Calcula totales (entradas, salidas, balance).
 * - Muestra gráfico (pie) y tablas resumidas.
 * - Genera PDF con resumen, gráfico e información detallada.
 * Detalles:
 *  - Ajusta fechas manualmente para evitar desfase por zona horaria.
 *  - Generación de PDF asíncrona si el gráfico está presente.
 */

const API_TRANSACTIONS = "/Desafio1_LIS_2025/app/api/transactions.php?action=";

let transactionChart = null;
let dashboardData = {
  incomes: [],
  expenses: [],
  totalIncome: 0,
  totalExpenses: 0,
  balance: 0,
};

document.addEventListener("DOMContentLoaded", () => {
  fetchDashboardData();
  setupPDFGeneration();
});

async function fetchDashboardData() {
  try {
    const response = await fetch(API_TRANSACTIONS + "getTransactions");

    if (response.ok || response.status === 404) {
      const result = await response.json();
      const transactions = result.data || [];

      processDashboardData(transactions);
      updateSummaryCards();
      updateChart();
      updateTables();
    } else {
      showMessage("error", "Error", "Error cargando datos del dashboard");
    }
  } catch (error) {
    showMessage("error", "Error", "Error de conexión");
  }
}

function processDashboardData(transactions) {
  dashboardData.incomes = transactions.filter(
    (t) => t.transaction_type === "income"
  );
  dashboardData.expenses = transactions.filter(
    (t) => t.transaction_type === "expense"
  );

  dashboardData.totalIncome = dashboardData.incomes.reduce(
    (sum, income) => sum + parseFloat(income.amount),
    0
  );
  dashboardData.totalExpenses = dashboardData.expenses.reduce(
    (sum, expense) => sum + parseFloat(expense.amount),
    0
  );
  dashboardData.balance =
    dashboardData.totalIncome - dashboardData.totalExpenses;
}

function updateSummaryCards() {
  const formatter = new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
  });

  document.getElementById("total-income").textContent = formatter.format(
    dashboardData.totalIncome
  );
  document.getElementById("total-expenses").textContent = formatter.format(
    dashboardData.totalExpenses
  );

  const balanceElement = document.getElementById("balance");
  balanceElement.textContent = formatter.format(dashboardData.balance);

  // Update balance color based on positive/negative
  balanceElement.className = "card-value";
  if (dashboardData.balance > 0) {
    balanceElement.classList.add("text-success");
  } else if (dashboardData.balance < 0) {
    balanceElement.classList.add("text-danger");
  } else {
    balanceElement.classList.add("text-info");
  }
}

/**
 * Build pie chart for on-screen display.
 */
function updateChart() {
  /**
   * Construye gráfico en canvas principal.
   * - Si no hay datos: muestra "Sin datos".
   * - Colores alineados al esquema (verde entradas, rojo salidas).
   */
  const ctx = document.getElementById("transactionChart").getContext("2d");

  if (transactionChart) {
    transactionChart.destroy();
  }

  const hasData =
    dashboardData.totalIncome > 0 || dashboardData.totalExpenses > 0;

  transactionChart = new Chart(ctx, {
    type: "pie",
    data: {
      labels: hasData ? ["Entradas", "Salidas"] : ["Sin datos"],
      datasets: [
        {
          data: hasData
            ? [dashboardData.totalIncome, dashboardData.totalExpenses]
            : [1],
          backgroundColor: hasData
            ? ["rgba(25, 135, 84, 0.8)", "rgba(220, 53, 69, 0.8)"]
            : ["rgba(108, 117, 125, 0.8)"],
          borderColor: hasData
            ? ["rgba(25, 135, 84, 1)", "rgba(220, 53, 69, 1)"]
            : ["rgba(108, 117, 125, 1)"],
          borderWidth: 2,
        },
      ],
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: "bottom",
          labels: {
            color: "#ffffff",
            padding: 20,
          },
        },
        tooltip: {
          callbacks: {
            label: function (context) {
              if (!hasData) return "Sin transacciones";
              const formatter = new Intl.NumberFormat("en-US", {
                style: "currency",
                currency: "USD",
              });
              return context.label + ": " + formatter.format(context.raw);
            },
          },
        },
      },
    },
  });
}

function updateTables() {
  updateIncomeTable();
  updateExpenseTable();
}

function updateIncomeTable() {
  const tbody = document.getElementById("income-table-body");

  if (dashboardData.incomes.length === 0) {
    tbody.innerHTML =
      '<tr><td colspan="3" class="text-center">No hay entradas registradas</td></tr>';
    return;
  }

  // Sort by date (most recent first) and take last 10
  const recentIncomes = dashboardData.incomes
    .sort((a, b) => new Date(b.date) - new Date(a.date))
    .slice(0, 10);

  const formatter = new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
  });

  let html = "";
  recentIncomes.forEach((income) => {
    const dateParts = income.date.split("-");
    const date = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]);

    html += `
            <tr>
                <td>${date.toLocaleDateString()}</td>
                <td>${income.category_name}</td>
                <td class="text-success">${formatter.format(income.amount)}</td>
            </tr>
        `;
  });

  tbody.innerHTML = html;
}

function updateExpenseTable() {
  const tbody = document.getElementById("expense-table-body");

  if (dashboardData.expenses.length === 0) {
    tbody.innerHTML =
      '<tr><td colspan="3" class="text-center">No hay salidas registradas</td></tr>';
    return;
  }

  // Sort by date (most recent first) and take last 10
  const recentExpenses = dashboardData.expenses
    .sort((a, b) => new Date(b.date) - new Date(a.date))
    .slice(0, 10);

  const formatter = new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
  });

  let html = "";
  recentExpenses.forEach((expense) => {
    const dateParts = expense.date.split("-");
    const date = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]);

    html += `
            <tr>
                <td>${date.toLocaleDateString()}</td>
                <td>${expense.category_name}</td>
                <td class="text-danger">${formatter.format(expense.amount)}</td>
            </tr>
        `;
  });

  tbody.innerHTML = html;
}

function setupPDFGeneration() {
  const generatePDFBtn = document.getElementById("generate-pdf-btn");
  generatePDFBtn.addEventListener("click", generatePDFReport);
}

/**
 * Generate PDF report (async path if chart present).
 */
function generatePDFReport() {
  /**
   * Genera el PDF:
   *  1. Encabezado (color corporativo).
   *  2. Cajas resumen (tema claro para mejor impresión).
   *  3. Gráfico (renderizado en canvas temporal).
   *  4. Tablas de ingresos / gastos.
   *  5. Pie con paginación.
   * Usa setTimeout para asegurar que el gráfico se renderice antes de extraer imagen.
   */
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF();

  const formatter = new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
  });

  // Header with logo/title section
  doc.setFillColor(235, 92, 145); // Primary color from CSS
  doc.rect(0, 0, 210, 40, "F");

  doc.setTextColor(255, 255, 255);
  doc.setFontSize(24);
  doc.setFont(undefined, "bold");
  doc.text("REPORTE FINANCIERO", 20, 25);

  doc.setFontSize(12);
  doc.setFont(undefined, "normal");
  doc.text(
    `Generado el: ${new Date().toLocaleDateString("es-ES", {
      year: "numeric",
      month: "long",
      day: "numeric",
    })}`,
    20,
    35
  );

  // Reset text color for content
  doc.setTextColor(0, 0, 0);

  // Summary section with boxes - Light theme for PDF
  let yPosition = 60;
  doc.setFontSize(18);
  doc.setFont(undefined, "bold");
  doc.text("RESUMEN EJECUTIVO", 20, yPosition);

  yPosition += 15;

  // Summary boxes with light theme
  const boxWidth = 55;
  const boxHeight = 25;
  const spacing = 10;

  // Income box - Light green theme
  doc.setFillColor(220, 252, 231); // Light green background
  doc.rect(20, yPosition, boxWidth, boxHeight, "F");
  doc.setDrawColor(34, 197, 94); // Green border
  doc.setLineWidth(1);
  doc.rect(20, yPosition, boxWidth, boxHeight);

  doc.setFontSize(10);
  doc.setTextColor(21, 128, 61); // Dark green text
  doc.setFont(undefined, "bold");
  doc.text("TOTAL ENTRADAS", 22, yPosition + 8);
  doc.setFontSize(14);
  doc.setFont(undefined, "bold");
  doc.text(formatter.format(dashboardData.totalIncome), 22, yPosition + 18);

  // Expense box - Light red theme
  const expenseX = 20 + boxWidth + spacing;
  doc.setFillColor(254, 226, 226); // Light red background
  doc.rect(expenseX, yPosition, boxWidth, boxHeight, "F");
  doc.setDrawColor(239, 68, 68); // Red border
  doc.rect(expenseX, yPosition, boxWidth, boxHeight);

  doc.setTextColor(185, 28, 28); // Dark red text
  doc.setFontSize(10);
  doc.setFont(undefined, "bold");
  doc.text("TOTAL SALIDAS", expenseX + 2, yPosition + 8);
  doc.setFontSize(14);
  doc.setFont(undefined, "bold");
  doc.text(
    formatter.format(dashboardData.totalExpenses),
    expenseX + 2,
    yPosition + 18
  );

  // Balance box - Light blue or red theme based on value
  const balanceX = expenseX + boxWidth + spacing;
  const isPositive = dashboardData.balance >= 0;

  if (isPositive) {
    doc.setFillColor(239, 246, 255); // Light blue background
    doc.setDrawColor(59, 130, 246); // Blue border
    doc.setTextColor(30, 64, 175); // Dark blue text
  } else {
    doc.setFillColor(254, 226, 226); // Light red background
    doc.setDrawColor(239, 68, 68); // Red border
    doc.setTextColor(185, 28, 28); // Dark red text
  }

  doc.rect(balanceX, yPosition, boxWidth, boxHeight, "F");
  doc.rect(balanceX, yPosition, boxWidth, boxHeight);

  doc.setFontSize(10);
  doc.setFont(undefined, "bold");
  doc.text("BALANCE", balanceX + 2, yPosition + 8);
  doc.setFontSize(14);
  doc.setFont(undefined, "bold");
  doc.text(
    formatter.format(dashboardData.balance),
    balanceX + 2,
    yPosition + 18
  );

  yPosition += 40;

  // Declare chartSize variable outside the if block
  let chartSize = 0;

  // Add pie chart with proper styling for PDF
  if (dashboardData.totalIncome > 0 || dashboardData.totalExpenses > 0) {
    doc.setTextColor(0, 0, 0);
    doc.setFontSize(16);
    doc.setFont(undefined, "bold");
    doc.text("DISTRIBUCIÓN DE TRANSACCIONES", 20, yPosition);

    yPosition += 10;

    // Set chartSize here
    chartSize = 80;

    // Create a temporary chart for PDF with proper colors - use existing chart data
    const canvas = document.getElementById("transactionChart");

    // Create a new temporary canvas with light theme
    const tempCanvas = document.createElement("canvas");
    tempCanvas.width = 400;
    tempCanvas.height = 400;
    tempCanvas.style.backgroundColor = "white";

    const tempCtx = tempCanvas.getContext("2d");

    // Create chart with light theme colors
    const tempChart = new Chart(tempCtx, {
      type: "pie",
      data: {
        labels: ["Entradas", "Salidas"],
        datasets: [
          {
            data: [dashboardData.totalIncome, dashboardData.totalExpenses],
            backgroundColor: [
              "rgba(34, 197, 94, 0.8)", // Green for income
              "rgba(239, 68, 68, 0.8)", // Red for expense
            ],
            borderColor: ["rgba(34, 197, 94, 1)", "rgba(239, 68, 68, 1)"],
            borderWidth: 2,
          },
        ],
      },
      options: {
        responsive: false,
        animation: {
          duration: 0, // Disable animation for immediate rendering
        },
        plugins: {
          legend: {
            position: "bottom",
            labels: {
              color: "#000000", // Black text for PDF
              padding: 20,
              font: {
                size: 16,
                weight: "bold",
              },
            },
          },
          tooltip: {
            enabled: false, // Disable tooltips for PDF
          },
        },
      },
    });

    // Wait a moment for chart to render, then capture and continue
    setTimeout(() => {
      try {
        const chartImageData = tempCanvas.toDataURL("image/png", 1.0);

        // Add chart to PDF
        const chartX = (210 - chartSize) / 2; // Center the chart
        doc.addImage(
          chartImageData,
          "PNG",
          chartX,
          yPosition,
          chartSize,
          chartSize
        );

        // Clean up temporary chart
        tempChart.destroy();
        tempCanvas.remove();

        // Continue with the rest of the PDF generation
        continuePDFGeneration(doc, yPosition + chartSize + 20, formatter);
      } catch (error) {
        console.error("Error generating chart for PDF:", error);
        // Continue without chart if there's an error
        tempChart.destroy();
        tempCanvas.remove();
        continuePDFGeneration(doc, yPosition + 20, formatter);
      }
    }, 500);

    return; // Exit here to wait for chart rendering
  }

  // If no chart, continue immediately
  continuePDFGeneration(doc, yPosition + chartSize + 20, formatter);
}

/**
 * Continue PDF generation after chart (if any).
 */
function continuePDFGeneration(doc, yPosition, formatter) {
  /**
   * Continúa la construcción del PDF tras insertar (o no) el gráfico.
   * Responsabilidades:
   *  - Dibujar cabeceras de tablas con color semántico.
   *  - Alternar filas con tono claro para mejorar lectura.
   *  - Cortar página cuando se supera el límite vertical (~270).
   */
  // Check if we need a new page
  if (yPosition > 200) {
    doc.addPage();
    yPosition = 20;
  }

  // Income Table with light theme
  doc.setTextColor(0, 0, 0);
  doc.setFontSize(16);
  doc.setFont(undefined, "bold");
  doc.text("DETALLE DE ENTRADAS", 20, yPosition);
  yPosition += 15;

  if (dashboardData.incomes.length > 0) {
    // Table header - Light green theme
    doc.setFillColor(220, 252, 231); // Light green
    doc.rect(20, yPosition - 5, 170, 10, "F");
    doc.setDrawColor(34, 197, 94);
    doc.setLineWidth(0.5);
    doc.rect(20, yPosition - 5, 170, 10);

    doc.setTextColor(21, 128, 61); // Dark green text
    doc.setFontSize(11);
    doc.setFont(undefined, "bold");
    doc.text("FECHA", 25, yPosition);
    doc.text("CATEGORÍA", 70, yPosition);
    doc.text("DESCRIPCIÓN", 120, yPosition);
    doc.text("MONTO", 160, yPosition);

    yPosition += 10;
    doc.setTextColor(0, 0, 0);
    doc.setFont(undefined, "normal");
    doc.setFontSize(9);

    dashboardData.incomes.forEach((income, index) => {
      if (yPosition > 270) {
        doc.addPage();
        yPosition = 20;
      }

      // Alternate row colors - Light theme
      if (index % 2 === 0) {
        doc.setFillColor(249, 250, 251); // Very light gray
        doc.rect(20, yPosition - 3, 170, 12, "F");
      }

      const dateParts = income.date.split("-");
      const date = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]);

      doc.text(date.toLocaleDateString("es-ES"), 25, yPosition + 3);
      doc.text(income.category_name.substring(0, 20), 70, yPosition + 3);
      doc.text(
        (income.description || "N/A").substring(0, 15),
        120,
        yPosition + 3
      );
      doc.setTextColor(21, 128, 61); // Dark green for amounts
      doc.text(formatter.format(income.amount), 160, yPosition + 3);
      doc.setTextColor(0, 0, 0);

      yPosition += 12;
    });

    // Total row - Light green theme
    doc.setFillColor(220, 252, 231);
    doc.rect(20, yPosition - 3, 170, 12, "F");
    doc.setDrawColor(34, 197, 94);
    doc.rect(20, yPosition - 3, 170, 12);
    doc.setFont(undefined, "bold");
    doc.text("TOTAL ENTRADAS:", 120, yPosition + 3);
    doc.setTextColor(21, 128, 61);
    doc.text(formatter.format(dashboardData.totalIncome), 160, yPosition + 3);
  } else {
    doc.setTextColor(75, 85, 99);
    doc.text("No hay entradas registradas", 25, yPosition);
  }

  yPosition += 25;

  // Check if we need a new page for expenses
  if (yPosition > 200) {
    doc.addPage();
    yPosition = 20;
  }

  // Expense Table with light theme
  doc.setTextColor(0, 0, 0);
  doc.setFontSize(16);
  doc.setFont(undefined, "bold");
  doc.text("DETALLE DE SALIDAS", 20, yPosition);
  yPosition += 15;

  if (dashboardData.expenses.length > 0) {
    // Table header - Light red theme
    doc.setFillColor(254, 226, 226); // Light red
    doc.rect(20, yPosition - 5, 170, 10, "F");
    doc.setDrawColor(239, 68, 68);
    doc.setLineWidth(0.5);
    doc.rect(20, yPosition - 5, 170, 10);

    doc.setTextColor(185, 28, 28); // Dark red text
    doc.setFontSize(11);
    doc.setFont(undefined, "bold");
    doc.text("FECHA", 25, yPosition);
    doc.text("CATEGORÍA", 70, yPosition);
    doc.text("DESCRIPCIÓN", 120, yPosition);
    doc.text("MONTO", 160, yPosition);

    yPosition += 10;
    doc.setTextColor(0, 0, 0);
    doc.setFont(undefined, "normal");
    doc.setFontSize(9);

    dashboardData.expenses.forEach((expense, index) => {
      if (yPosition > 270) {
        doc.addPage();
        yPosition = 20;
      }

      // Alternate row colors - Light theme
      if (index % 2 === 0) {
        doc.setFillColor(249, 250, 251); // Very light gray
        doc.rect(20, yPosition - 3, 170, 12, "F");
      }

      const dateParts = expense.date.split("-");
      const date = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]);

      doc.text(date.toLocaleDateString("es-ES"), 25, yPosition + 3);
      doc.text(expense.category_name.substring(0, 20), 70, yPosition + 3);
      doc.text(
        (expense.description || "N/A").substring(0, 15),
        120,
        yPosition + 3
      );
      doc.setTextColor(185, 28, 28); // Dark red for amounts
      doc.text(formatter.format(expense.amount), 160, yPosition + 3);
      doc.setTextColor(0, 0, 0);

      yPosition += 12;
    });

    // Total row - Light red theme
    doc.setFillColor(254, 226, 226);
    doc.rect(20, yPosition - 3, 170, 12, "F");
    doc.setDrawColor(239, 68, 68);
    doc.rect(20, yPosition - 3, 170, 12);
    doc.setFont(undefined, "bold");
    doc.text("TOTAL SALIDAS:", 120, yPosition + 3);
    doc.setTextColor(185, 28, 28);
    doc.text(formatter.format(dashboardData.totalExpenses), 160, yPosition + 3);
  } else {
    doc.setTextColor(75, 85, 99);
    doc.text("No hay salidas registradas", 25, yPosition);
  }

  // Footer
  const pageCount = doc.internal.getNumberOfPages();
  for (let i = 1; i <= pageCount; i++) {
    doc.setPage(i);
    doc.setFontSize(8);
    doc.setTextColor(75, 85, 99);
    doc.text(`Página ${i} de ${pageCount}`, 180, 290);
    doc.text("Sistema de Gestión Financiera", 20, 290);
  }

  // Save the PDF with timestamp
  const fileName = `reporte_financiero_${
    new Date().toISOString().split("T")[0]
  }_${new Date().getTime()}.pdf`;
  doc.save(fileName);

  showMessage(
    "success",
    "Éxito",
    "Reporte PDF generado exitosamente con gráfico incluido"
  );
}
