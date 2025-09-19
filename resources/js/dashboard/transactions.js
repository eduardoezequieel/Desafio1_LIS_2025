/**
 * Vista unificada de transacciones:
 * - Tabla con filtro (todas / ingresos / gastos).
 * - Dos modales independientes (ingreso / gasto).
 * - Reutiliza mismo endpoint (transaction_type define comportamiento).
 * - Manejo de archivos por transacción (factura).
 * Ventajas:
 *  - Menos duplicación que incomes.js / expenses.js separados.
 *  - UX consistente.
 */

const API_TRANSACTIONS = "/Desafio1_LIS_2025/app/api/transactions.php?action=";

// UI Elements
const newIncomeBtn = document.getElementById("new-income-btn");
const newExpenseBtn = document.getElementById("new-expense-btn");
const incomeForm = document.getElementById("income-form");
const expenseForm = document.getElementById("expense-form");
const incomeModal = new bootstrap.Modal(document.getElementById("incomeModal"));
const expenseModal = new bootstrap.Modal(
  document.getElementById("expenseModal")
);
const saveIncomeBtn = document.getElementById("save-income-btn");
const saveExpenseBtn = document.getElementById("save-expense-btn");
const incomeLabel = document.getElementById("income-modal-label");
const expenseLabel = document.getElementById("expense-modal-label");

// Filter buttons
const filterAllBtn = document.getElementById("filter-all");
const filterIncomeBtn = document.getElementById("filter-income");
const filterExpenseBtn = document.getElementById("filter-expense");

// File upload elements
const incomeInvoicePreview = document.getElementById("income-invoice-preview");
const expenseInvoicePreview = document.getElementById(
  "expense-invoice-preview"
);
const incomeInvoiceInput = document.getElementById("income-invoice");
const expenseInvoiceInput = document.getElementById("expense-invoice");

// Category selects
const incomeCategorySelect = document.getElementById("income-category");
const expenseCategorySelect = document.getElementById("expense-category");

// State variables
let incomeModalMode = "create";
let expenseModalMode = "create";
let currentIncomeInvoicePath = null;
let currentExpenseInvoicePath = null;
let currentFilter = "all";
let allTransactions = [];

// Event Listeners
newIncomeBtn.addEventListener("click", () => {
  resetIncomeForm();
  incomeModal.show();
  incomeModalMode = "create";
  incomeLabel.textContent = "Registrar entrada";
});

newExpenseBtn.addEventListener("click", () => {
  resetExpenseForm();
  expenseModal.show();
  expenseModalMode = "create";
  expenseLabel.textContent = "Registrar gasto";
});

// Filter event listeners
filterAllBtn.addEventListener("click", () => setFilter("all"));
filterIncomeBtn.addEventListener("click", () => setFilter("income"));
filterExpenseBtn.addEventListener("click", () => setFilter("expense"));

// Form submissions
saveIncomeBtn.addEventListener("click", () => incomeForm.requestSubmit());
saveExpenseBtn.addEventListener("click", () => expenseForm.requestSubmit());

incomeForm.addEventListener("submit", (e) => handleFormSubmit(e, "income"));
expenseForm.addEventListener("submit", (e) => handleFormSubmit(e, "expense"));

// File upload handlers
setupFileUpload(incomeInvoiceInput, incomeInvoicePreview, "income");
setupFileUpload(expenseInvoiceInput, expenseInvoicePreview, "expense");

document.addEventListener("DOMContentLoaded", () => {
  fetchCategories();
  fetchTransactions();
});

/**
 * Filter selector and table re-render.
 * @param {'all'|'income'|'expense'} filter
 */
function setFilter(filter) {
  /**
   * Cambia el filtro activo y repinta la tabla.
   * @param {'all'|'income'|'expense'} filter
   */
  currentFilter = filter;

  // Update button states
  document
    .querySelectorAll(".btn-group .btn")
    .forEach((btn) => btn.classList.remove("active"));
  document.getElementById(`filter-${filter}`).classList.add("active");

  // Filter and display transactions
  displayTransactions(allTransactions);
}

/**
 * Generic submit handler for both forms.
 * @param {SubmitEvent} e
 * @param {'income'|'expense'} type
 */
async function handleFormSubmit(e, type) {
  /**
   * Manejo genérico de formularios (ingreso/gasto).
   * - Añade transaction_type según modal invocante.
   * - Conserva factura anterior si no se sube una nueva.
   * - Muestra mensajes según resultado.
   */
  e.preventDefault();

  const form = type === "income" ? incomeForm : expenseForm;
  const modal = type === "income" ? incomeModal : expenseModal;
  const modalMode = type === "income" ? incomeModalMode : expenseModalMode;
  const currentInvoicePath =
    type === "income" ? currentIncomeInvoicePath : currentExpenseInvoicePath;

  const formData = new FormData(form);
  formData.append("transaction_type", type);

  if (formData.get("id") === "" && modalMode === "edit") {
    showMessage(
      "error",
      "Error",
      `No se ha proporcionado un ID de ${
        type === "income" ? "entrada" : "gasto"
      } válido para la edición.`
    );
    return;
  }

  if (
    modalMode === "edit" &&
    !formData.get("invoiceImage").size &&
    currentInvoicePath
  ) {
    formData.append("current_invoice_path", currentInvoicePath);
  }

  try {
    const response = await fetch(
      API_TRANSACTIONS +
        (modalMode === "create" ? "createTransaction" : "updateTransaction"),
      {
        method: "POST",
        body: formData,
      }
    );

    const data = await response.json();

    if (response.status !== 200 && response.status !== 201) {
      const { exception, message } = data;
      showMessage("error", "Error", exception || message);
      return;
    }

    modal.hide();
    type === "income" ? resetIncomeForm() : resetExpenseForm();
    fetchTransactions();

    showMessage(
      "success",
      "Éxito",
      `${type === "income" ? "Entrada" : "Gasto"} ${
        modalMode === "create" ? "creada" : "actualizada"
      } correctamente`
    );
  } catch (error) {
    showMessage("error", "Error", "Error de conexión");
  }
}

/**
 * Configure drag/drop + preview for invoice upload.
 */
function setupFileUpload(input, preview, type) {
  /**
   * Configura drag & drop + validaciones + vista previa.
   * @param {HTMLInputElement} input
   * @param {HTMLElement} preview
   * @param {'income'|'expense'} type
   */
  const handleUpdateImage = (file) => {
    const maxFileSize = 5 * 1024 * 1024; // 5MB
    if (!file || !(file instanceof File)) return;

    const allowedFormats = [
      "image/jpeg",
      "image/png",
      "image/gif",
      "image/webp",
    ];
    if (!allowedFormats.includes(file.type)) {
      showMessage(
        "error",
        "Error",
        "Formato de imagen no válido. Solo se permiten JPG, PNG, GIF y WEBP."
      );
      input.value = "";
      return;
    }

    if (file.size > maxFileSize) {
      showMessage(
        "error",
        "Error",
        "El tamaño del archivo excede el límite de 5MB."
      );
      input.value = "";
      return;
    }

    preview.innerHTML = `
      <div class="w-100 h-25 d-flex justify-content-between align-items-center px-2">
        <span class="text-white">${file.name}</span>
        <div class="d-flex gap-2">
          <button type="button" class="btn btn-sm btn-secondary preview-invoice-btn">Ver</button>
          <button type="button" class="btn btn-danger btn-sm remove-invoice-btn">X</button>
        </div>
      </div>
    `;

    const previewBtn = preview.querySelector(".preview-invoice-btn");
    previewBtn.addEventListener("click", () => handleOpenInvoicePreview(file));

    const deleteBtn = preview.querySelector(".remove-invoice-btn");
    deleteBtn.addEventListener("click", () => handleDeleteImage(type));
  };

  const handleDeleteImage = (type) => {
    const targetPreview =
      type === "income" ? incomeInvoicePreview : expenseInvoicePreview;
    const targetInput =
      type === "income" ? incomeInvoiceInput : expenseInvoiceInput;

    targetPreview.innerHTML = `<span>Haz clic para subir o arrastra para subir una imagen</span>`;
    targetInput.value = "";

    if (type === "income") {
      currentIncomeInvoicePath = null;
    } else {
      currentExpenseInvoicePath = null;
    }
  };

  input.addEventListener("change", (e) => handleUpdateImage(e.target.files[0]));

  preview.addEventListener("drop", (e) => {
    e.preventDefault();
    preview.classList.remove("dragover");
    handleUpdateImage(e.dataTransfer.files[0]);
  });

  preview.addEventListener("click", () => input.click());
  preview.addEventListener("dragover", (e) => {
    e.preventDefault();
    preview.classList.add("dragover");
  });
  preview.addEventListener("dragleave", () =>
    preview.classList.remove("dragover")
  );
}

/**
 * Fetch all transactions; local filtering done client-side.
 */
async function fetchTransactions() {
  /**
   * Trae todas las transacciones (sin filtro en servidor).
   * Filtrado posterior en cliente para mayor fluidez.
   */
  try {
    const response = await fetch(API_TRANSACTIONS + "getTransactions");

    if (response.ok || response.status === 404) {
      const result = await response.json();
      allTransactions = result.data || [];
      displayTransactions(allTransactions);
    } else {
      const { exception } = await response.json();
      showMessage("error", "Error", exception);
    }
  } catch (error) {
    showMessage("error", "Error", "Error cargando transacciones");
  }
}

/**
 * Paint table with filtered transactions.
 */
function displayTransactions(transactions) {
  /**
   * Renderiza la tabla según filtro activo.
   * - Aplica color según tipo.
   * - Muestra estado vacío si no hay registros.
   */
  const tbody = document.querySelector("#transactions-table-body");

  // Filter transactions based on current filter
  let filteredTransactions = transactions;
  if (currentFilter !== "all") {
    filteredTransactions = transactions.filter(
      (t) => t.transaction_type === currentFilter
    );
  }

  let html = "";

  if (!filteredTransactions || filteredTransactions.length === 0) {
    html = `
      <tr>
        <td colspan="6" class="text-center">No hay transacciones registradas</td>
      </tr>
    `;
  } else {
    filteredTransactions.forEach((transaction) => {
      const dateTimeOptions = {
        year: "numeric",
        month: "numeric",
        day: "numeric",
      };
      const formattedAmount = new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD",
      }).format(transaction.amount);

      // Fix date handling - create date in local timezone
      const dateParts = transaction.date.split("-");
      const date = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]);

      const typeLabel =
        transaction.transaction_type === "income" ? "Entrada" : "Gasto";
      const typeClass =
        transaction.transaction_type === "income"
          ? "text-success"
          : "text-danger";

      html += `
        <tr>
          <th scope="row">${transaction.id}</th>
          <td class="pt-2 ${typeClass}">${typeLabel}</td>
          <td class="pt-2">${formattedAmount}</td>
          <td class="pt-2">${transaction.category_name}</td>
          <td class="pt-2">${date.toLocaleString(
            undefined,
            dateTimeOptions
          )}</td>
          <td>
            <button class="btn btn-sm btn-secondary edit-transaction-btn" data-id="${
              transaction.id
            }" data-type="${transaction.transaction_type}">Editar</button>
            <button class="btn btn-sm btn-danger delete-transaction-btn" data-id="${
              transaction.id
            }" data-type="${transaction.transaction_type}">Eliminar</button>
          </td>
        </tr>
      `;
    });
  }

  tbody.innerHTML = html;
  setupTableEventListeners(filteredTransactions);
}

function setupTableEventListeners(transactions) {
  document.querySelectorAll(".edit-transaction-btn").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      const transactionId = e.target.dataset.id;
      const transactionType = e.target.dataset.type;
      const transaction = transactions.find((t) => t.id == transactionId);

      if (transaction) {
        editTransaction(transaction, transactionType);
      }
    });
  });

  document.querySelectorAll(".delete-transaction-btn").forEach((btn) => {
    btn.addEventListener("click", async (e) => {
      const transactionId = e.target.dataset.id;
      const transactionType = e.target.dataset.type;
      const transaction = transactions.find((t) => t.id == transactionId);

      if (transaction) {
        await deleteTransaction(transaction, transactionType);
      }
    });
  });
}

function editTransaction(transaction, type) {
  /**
   * Carga datos en el modal adecuado.
   * - Resetea primero para evitar 'data bleed'.
   * - Prepara previsualización de factura si existe.
   */
  if (type === "income") {
    resetIncomeForm();
    incomeLabel.textContent = "Editar entrada";

    incomeForm.id.value = transaction.id;
    incomeForm.amount.value = transaction.amount;
    incomeForm.date.value = transaction.date.split(" ")[0];
    incomeForm.category_id.value = transaction.category_id;
    incomeForm.description.value = transaction.description || "";

    currentIncomeInvoicePath = transaction.invoice_path;
    setupExistingInvoice(
      incomeInvoicePreview,
      incomeInvoiceInput,
      transaction.invoice_path,
      "income"
    );

    incomeModal.show();
    incomeModalMode = "edit";
  } else {
    resetExpenseForm();
    expenseLabel.textContent = "Editar gasto";

    expenseForm.id.value = transaction.id;
    expenseForm.amount.value = transaction.amount;
    expenseForm.date.value = transaction.date.split(" ")[0];
    expenseForm.category_id.value = transaction.category_id;
    expenseForm.description.value = transaction.description || "";

    currentExpenseInvoicePath = transaction.invoice_path;
    setupExistingInvoice(
      expenseInvoicePreview,
      expenseInvoiceInput,
      transaction.invoice_path,
      "expense"
    );

    expenseModal.show();
    expenseModalMode = "edit";
  }
}

function setupExistingInvoice(preview, input, invoicePath, type) {
  if (invoicePath) {
    preview.innerHTML = `
      <div class="w-100 h-25 d-flex justify-content-between align-items-center px-2">
        <span class="text-white">${invoicePath.split("/").pop()}</span>
        <div class="d-flex gap-2">
          <button type="button" class="btn btn-sm btn-secondary preview-invoice-btn">Ver</button>
          <button type="button" class="btn btn-danger btn-sm remove-invoice-btn">X</button>
        </div>
      </div>
    `;

    const previewBtn = preview.querySelector(".preview-invoice-btn");
    previewBtn.addEventListener("click", () =>
      handleOpenInvoicePreview(invoicePath)
    );

    const deleteBtn = preview.querySelector(".remove-invoice-btn");
    deleteBtn.addEventListener("click", () => {
      preview.innerHTML = `<span>Haz clic para subir o arrastra para subir una imagen</span>`;
      input.value = "";
      if (type === "income") {
        currentIncomeInvoicePath = null;
      } else {
        currentExpenseInvoicePath = null;
      }
    });
  }
  input.value = "";
}

/**
 * Delete transaction after confirmation.
 */
async function deleteTransaction(transaction, type) {
  const formattedAmount = new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
  }).format(transaction.amount);

  const { isConfirmed } = await confirmAction(
    "Confirmar eliminación",
    `¿Estás seguro de que deseas eliminar ${
      type === "income" ? "la entrada" : "el gasto"
    } con un valor de ${formattedAmount}? Esta acción no se puede deshacer.`
  );

  if (isConfirmed) {
    try {
      const response = await fetch(
        API_TRANSACTIONS +
          "deleteTransaction&" +
          new URLSearchParams({ id: transaction.id }),
        { method: "GET" }
      );

      if (response.ok) {
        const { message } = await response.json();
        fetchTransactions();
        showMessage("success", "Éxito", message);
      } else {
        const { exception } = await response.json();
        showMessage("error", "Error", exception);
      }
    } catch (error) {
      showMessage("error", "Error", "Error de conexión");
    }
  }
}

/**
 * Open invoice preview in a new tab/window.
 */
function handleOpenInvoicePreview(image) {
  if (image instanceof File) {
    const reader = new FileReader();
    reader.onload = (e) => {
      const anchorTag = document.createElement("a");
      anchorTag.href = e.target.result;
      anchorTag.target = "_blank";
      anchorTag.rel = "noopener noreferrer";
      anchorTag.click();
    };
    reader.readAsDataURL(image);
  } else if (typeof image === "string" && image.trim() !== "") {
    const anchorTag = document.createElement("a");
    anchorTag.href = "/Desafio1_LIS_2025/public/" + image;
    anchorTag.target = "_blank";
    anchorTag.rel = "noopener noreferrer";
    anchorTag.click();
  }
}

/**
 * Reset income form to default state.
 */
function resetIncomeForm() {
  incomeForm.reset();
  incomeInvoicePreview.innerHTML = `<span>Haz clic para subir o arrastra para subir una imagen</span>`;
  incomeInvoiceInput.value = "";
  currentIncomeInvoicePath = null;
}

/**
 * Reset expense form to default state.
 */
function resetExpenseForm() {
  expenseForm.reset();
  expenseInvoicePreview.innerHTML = `<span>Haz clic para subir o arrastra para subir una imagen</span>`;
  expenseInvoiceInput.value = "";
  currentExpenseInvoicePath = null;
}

/**
 * Fetch and populate category selects for income and expense.
 */
async function fetchCategories() {
  try {
    const [incomeResponse, expenseResponse] = await Promise.all([
      fetch(API_TRANSACTIONS + "getCategories&type=income"),
      fetch(API_TRANSACTIONS + "getCategories&type=expense"),
    ]);

    if (incomeResponse.ok) {
      const { data: incomeCategories } = await incomeResponse.json();
      let html = "";
      incomeCategories.forEach((category) => {
        html += `<option value="${category.id}">${category.name}</option>`;
      });
      incomeCategorySelect.innerHTML = html;
    }

    if (expenseResponse.ok) {
      const { data: expenseCategories } = await expenseResponse.json();
      let html = "";
      expenseCategories.forEach((category) => {
        html += `<option value="${category.id}">${category.name}</option>`;
      });
      expenseCategorySelect.innerHTML = html;
    }
  } catch (error) {
    showMessage("error", "Error", "Error cargando categorías");
  }
}
