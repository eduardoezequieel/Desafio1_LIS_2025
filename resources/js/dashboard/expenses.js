const API_TRANSACTIONS = "/Desafio1_LIS_2025/app/api/transactions.php?action=";

const addBtn = document.getElementById("new-expense-btn");
const form = document.getElementById("expense-form");
const expenseLabel = document.getElementById("expense-modal-label");
const saveExpenseBtn = document.getElementById("save-expense-btn");
const expenseModal = new bootstrap.Modal(
  document.getElementById("expenseModal")
);
const invoicePreviewFileContainer = document.getElementById(
  "invoice-preview-file-container"
);
const expenseTypesSelect = document.getElementById("expense-type");
const invoicePreviewFileInput = document.getElementById("invoiceImage");

let expenseModalMode = "create"; // 'create' o 'edit'
let currentInvoicePath = null; // Add this variable to track the existing invoice path

addBtn.addEventListener("click", () => {
  resetForm();
  expenseModal.show();
  expenseModalMode = "create";
  expenseLabel.textContent = "Registrar gasto";
});

const handleDeleteImage = (e) => {
  e.stopPropagation();
  invoicePreviewFileContainer.innerHTML = `
    <span>Haz clic para subir o arrastra para subir una imagen</span>
  `;
  invoicePreviewFileInput.value = "";
  currentInvoicePath = null; // Clear the current invoice path when image is deleted
};

const handleUpdateImage = (file) => {
  const maxFileSize = 5 * 1024 * 1024; // 5MB
  if (!file || !(file instanceof File)) return;

  const allowedFormats = ["image/jpeg", "image/png", "image/gif", "image/webp"];
  if (!allowedFormats.includes(file.type)) {
    showMessage(
      "error",
      "Error",
      "Formato de imagen no válido. Solo se permiten JPG, PNG, GIF y WEBP."
    );
    invoicePreviewFileInput.value = "";
    return;
  }

  if (file.size > maxFileSize) {
    showMessage(
      "error",
      "Error",
      "El tamaño del archivo excede el límite de 5MB."
    );
    invoicePreviewFileInput.value = "";
    return;
  }

  invoicePreviewFileContainer.innerHTML = `
    <div class="w-100 h-25 d-flex justify-content-between align-items-center px-2">
      <span class="text-white">${file.name}</span>
      <div class="d-flex gap-2">
        <button type="button" class="btn btn-sm btn-secondary preview-invoice-btn">Ver</button>
        <button type="button" class="btn btn-danger btn-sm remove-invoice-btn">X</button>
      </div>
    </div>
  `;

  const previewBtn = invoicePreviewFileContainer.querySelector(
    ".preview-invoice-btn"
  );
  previewBtn.addEventListener("click", () => handleOpenInvoicePreview(file));

  const deleteBtn = invoicePreviewFileContainer.querySelector(
    ".remove-invoice-btn"
  );
  deleteBtn.addEventListener("click", handleDeleteImage);
};

invoicePreviewFileInput.addEventListener("change", (e) =>
  handleUpdateImage(e.target.files[0])
);

invoicePreviewFileContainer.addEventListener("drop", (e) => {
  e.preventDefault();
  invoicePreviewFileContainer.classList.remove("dragover");
  handleUpdateImage(e.dataTransfer.files[0]);
});

invoicePreviewFileContainer.addEventListener("click", () => {
  invoicePreviewFileInput.click();
});

invoicePreviewFileContainer.addEventListener("dragover", (e) => {
  e.preventDefault();
  invoicePreviewFileContainer.classList.add("dragover");
});

invoicePreviewFileContainer.addEventListener("dragleave", () => {
  invoicePreviewFileContainer.classList.remove("dragover");
});

saveExpenseBtn.addEventListener("click", () => form.requestSubmit());

form.addEventListener("submit", async (e) => {
  e.preventDefault();

  const formData = new FormData(form);

  // Set transaction type to expense
  formData.append("transaction_type", "expense");

  if (formData.get("id") === "" && expenseModalMode === "edit") {
    showMessage(
      "error",
      "Error",
      "No se ha proporcionado un ID de gasto válido para la edición."
    );
    return;
  }

  // Add the current invoice path to the form data if in edit mode and no new file selected
  if (
    expenseModalMode === "edit" &&
    !formData.get("invoiceImage").size &&
    currentInvoicePath
  ) {
    formData.append("current_invoice_path", currentInvoicePath);
  }

  const response = await fetch(
    API_TRANSACTIONS +
      (expenseModalMode === "create"
        ? "createTransaction"
        : "updateTransaction"),
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

  expenseModal.hide();
  resetForm();
  fetchExpenses();

  showMessage(
    "success",
    "Éxito",
    `Gasto ${
      expenseModalMode === "create" ? "creado" : "actualizado"
    } correctamente`
  );
});

document.addEventListener("DOMContentLoaded", () => {
  fetchExpenseTypes();
  fetchExpenses();
});

const deleteExpense = async (id) => {
  const formData = new FormData();
  formData.append("id", id);

  const response = await fetch(
    API_TRANSACTIONS + "deleteTransaction&" + new URLSearchParams({ id }),
    {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
      },
    }
  );
  if (response.ok) {
    const { message } = await response.json();
    fetchExpenses();
    showMessage("success", "Éxito", message);
  } else {
    const { exception } = await response.json();
    showMessage("error", "Error", exception);
  }
};

const resetForm = () => {
  form.reset();
  invoicePreviewFileContainer.innerHTML = `
    <span>Haz clic para subir o arrastra para subir una imagen</span>
  `;
  invoicePreviewFileInput.value = "";
  currentInvoicePath = null; // Reset the current invoice path when form is reset
};

const fetchExpenseTypes = async () => {
  const response = await fetch(
    API_TRANSACTIONS + "getCategories&type=expense",
    {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
      },
    }
  );

  if (!response.ok) {
    const { exception } = await response.json();
    showMessage("error", "Error", exception);
    return;
  }

  const { data } = await response.json();

  let html = "";

  data.forEach((category) => {
    html += `<option value="${category.id}">${category.name}</option>`;
  });

  expenseTypesSelect.innerHTML = html;
};

const handleOpenInvoicePreview = (image) => {
  if (image instanceof File) {
    const reader = new FileReader();
    reader.onload = (e) => {
      const img = document.createElement("img");
      const anchorTag = document.createElement("a");
      img.src = e.target.result;
      img.style.maxWidth = "100%";
      img.style.height = "auto";
      anchorTag.href = e.target.result;
      anchorTag.target = "_blank";
      anchorTag.rel = "noopener noreferrer";
      anchorTag.appendChild(img);
      anchorTag.click();
    };
    reader.readAsDataURL(image);
  } else if (typeof image === "string" && image.trim() !== "") {
    const anchorTag = document.createElement("a");
    anchorTag.href = "/Desafio1_LIS_2025/public/" + image;
    anchorTag.target = "_blank";
    anchorTag.rel = "noopener noreferrer";
    anchorTag.textContent = "Ver factura";
    anchorTag.click();
  }
};

const fetchExpenses = async () => {
  const response = await fetch(
    API_TRANSACTIONS + "getTransactions&type=expense",
    {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
      },
    }
  );

  if (response.ok || response.status === 201 || response.status === 404) {
    const { data } = await response.json();

    const tbody = document.querySelector("#expenses-table-body");

    let html = "";

    if (!data) {
      html = `
        <tr>
          <td colspan="5" class="text-center">No hay gastos registrados</td>
        </tr>
      `;
    }

    if (data?.length > 0 && data instanceof Array) {
      data.forEach((expense) => {
        const dateTimeOptions = {
          year: "numeric",
          month: "numeric",
          day: "numeric",
        };

        const formattedAmount = new Intl.NumberFormat("en-US", {
          style: "currency",
          currency: "USD",
        }).format(expense.amount);

        // Fix date handling - create date in local timezone
        const dateParts = expense.date.split("-");
        const date = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]);

        html += `
            <tr>
                <th scope="row">${expense.id}</th>
                <td class="pt-2">${formattedAmount}</td>
                <td class="pt-2">${expense.category_name}</td>
                <td class="pt-2">${date.toLocaleString(
                  undefined,
                  dateTimeOptions
                )}</td>
                <td>
                    <button class="btn btn-sm btn-secondary edit-expense-btn">Editar</button>
                    <button class="btn btn-sm btn-danger delete-expense-btn">Eliminar</button>
                </td>
            </tr>
        `;
      });
    }

    tbody.innerHTML = html;

    document.querySelectorAll(".edit-expense-btn").forEach((btn, index) => {
      btn.addEventListener("click", () => {
        resetForm();
        expenseLabel.textContent = "Editar gasto";

        const expense = data[index];

        form.id.value = expense.id;
        form.amount.value = expense.amount;
        form.date.value = expense.date.split(" ")[0];
        form["category_id"].value = expense.category_id;
        form["description"].value = expense.description || "";

        // Store the current invoice path
        currentInvoicePath = expense.invoice_path;

        invoicePreviewFileContainer.innerHTML = expense.invoice_path
          ? `
          <div class="w-100 h-25 d-flex justify-content-between align-items-center px-2">
            <span class="text-white">${expense.invoice_path
              .split("/")
              .pop()}</span>
            <div class="d-flex gap-2">
              <button type="button" class="btn btn-sm btn-secondary preview-invoice-btn">Ver</button>
              <button type="button" class="btn btn-danger btn-sm remove-invoice-btn">X</button>
            </div>
          </div>
        `
          : `
            <span>Haz clic para subir o arrastra para subir una imagen</span>
          `;
        invoicePreviewFileInput.value = "";

        const removeInvoiceBtn = invoicePreviewFileContainer.querySelector(
          ".remove-invoice-btn"
        );

        const previewInvoiceBtn = invoicePreviewFileContainer.querySelector(
          ".preview-invoice-btn"
        );
        if (previewInvoiceBtn) {
          previewInvoiceBtn.addEventListener("click", () =>
            handleOpenInvoicePreview(expense.invoice_path)
          );
        }

        if (removeInvoiceBtn) {
          removeInvoiceBtn.addEventListener("click", handleDeleteImage);
        }

        expenseModal.show();
        expenseModalMode = "edit";
      });
    });

    document.querySelectorAll(".delete-expense-btn").forEach((btn, index) => {
      btn.addEventListener("click", async () => {
        const expense = data[index];
        const formattedAmount = new Intl.NumberFormat("en-US", {
          style: "currency",
          currency: "USD",
        }).format(expense.amount);

        const { isConfirmed } = await confirmAction(
          "Confirmar eliminación",
          `¿Estás seguro de que deseas eliminar el gasto con un valor de ${formattedAmount}? Esta acción no se puede deshacer.`
        );

        if (isConfirmed) {
          deleteExpense(expense.id);
        }
      });
    });
  } else {
    const { exception } = await response.json();
    showMessage("error", "Error", exception);
  }
};
