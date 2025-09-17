const API_INCOMES = "/Desafio1_LIS_2025/app/api/incomes.php?action=";

const addBtn = document.getElementById("new-income-btn");
const form = document.getElementById("income-form");
const incomeLabel = document.getElementById("income-modal-label");
const saveIncomeBtn = document.getElementById("save-income-btn");
const incomeModal = new bootstrap.Modal(document.getElementById("incomeModal"));
const invoicePreviewFileContainer = document.getElementById(
  "invoice-preview-file-container"
);
const incomeTypesSelect = document.getElementById("income-type");
const invoicePreviewFileInput = document.getElementById("invoiceImage");

let incomeModalMode = "create"; // 'create' o 'edit'

addBtn.addEventListener("click", () => {
  form.reset();
  incomeModal.show();
  incomeModalMode = "create";
  incomeLabel.textContent = "Registrar entrada";
});

const handleDeleteImage = (e) => {
  e.stopPropagation();
  invoicePreviewFileContainer.innerHTML = `
    <span>Haz clic para subir o arrastra para subir una imagen</span>
  `;
  invoicePreviewFileInput.value = "";
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
      <button type="button" class="btn btn-danger btn-sm">X</button>
    </div>
  `;

  const deleteBtn = invoicePreviewFileContainer.querySelector("button");
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

saveIncomeBtn.addEventListener("click", () => form.requestSubmit());

form.addEventListener("submit", async (e) => {
  e.preventDefault();

  const formData = new FormData(form);

  if (formData.get("id") === "" && incomeModalMode === "edit") {
    showMessage(
      "error",
      "Error",
      "No se ha proporcionado un ID de entrada válido para la edición."
    );
    return;
  }

  const response = await fetch(
    API_incomeS +
      (incomeModalMode === "create" ? "createincome" : "updateincome"),
    {
      method: "POST",
      body: formData,
    }
  );

  if (!response.ok) {
    const { exception } = await response.json();
    showMessage("error", "Error", exception);
  }

  showMessage(
    "success",
    "Éxito",
    `Usuario ${
      incomeModalMode === "create" ? "creado" : "actualizado"
    } correctamente`
  ).then(() => {
    incomeModal.hide();
    form.reset();
    fetchIncomes();
  });
});

document.addEventListener("DOMContentLoaded", () => {
  fetchIncomeTypes();
  //   fetchIncomes();
});

const deleteincome = async (id) => {
  const formData = new FormData();
  formData.append("id", id);

  const response = await fetch(
    API_incomeS + "deleteincome&" + new URLSearchParams(formData),
    {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
      },
    }
  );
  if (response.ok) {
    const { message } = await response.json();
    showMessage("success", "Éxito", message).then(() => fetchIncomes());
  } else {
    const { exception } = await response.json();
    showMessage("error", "Error", exception);
  }
};

const fetchIncomeTypes = async () => {
  const response = await fetch(API_INCOMES + "getIncomeTypes", {
    method: "GET",
    headers: {
      "Content-Type": "application/json",
    },
  });

  if (!response.ok) {
    const { exception } = await response.json();
    showMessage("error", "Error", exception);
    return;
  }

  const { data } = await response.json();

  let html = "";

  data.forEach((type) => {
    html += `<option value="${type.id}">${type.name}</option>`;
  });

  incomeTypesSelect.innerHTML = html;
};

const fetchIncomes = async () => {
  const response = await fetch(API_incomeS + "getIncomes", {
    method: "GET",
    headers: {
      "Content-Type": "application/json",
    },
  });

  if (response.ok || response.status === 201 || response.status === 404) {
    const { data } = await response.json();

    const tbody = document.querySelector("#incomes-table-body");

    let html = "";

    data.forEach((income) => {
      const dateTimeOptions = {
        year: "numeric",
        month: "numeric",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
      };

      const lastUpdated = income.updated_at
        ? new Date(income.updated_at).toLocaleString(undefined, dateTimeOptions)
        : new Date(income.created_at).toLocaleString(
            undefined,
            dateTimeOptions
          );
      html += `
            <tr>
                <th scope="row">${income.id}</th>
                <td class="pt-2">${income.incomename}</td>
                <td class="pt-2">${lastUpdated}</td>
                <td>
                    <button class="btn btn-sm btn-secondary edit-income-btn">Editar</button>
                    <button class="btn btn-sm btn-danger delete-income-btn">Eliminar</button>
                </td>
            </tr>
        `;
    });

    tbody.innerHTML = html;

    document.querySelectorAll(".edit-income-btn").forEach((btn, index) => {
      btn.addEventListener("click", () => {
        form.reset();
        incomeLabel.textContent = "Editar usuario";

        const income = data[index];

        form.incomename.value = income.incomename;
        form.id.value = income.id;

        incomeModal.show();
        incomeModalMode = "edit";
      });
    });

    document.querySelectorAll(".delete-income-btn").forEach((btn, index) => {
      btn.addEventListener("click", async () => {
        const income = data[index];

        const { isConfirmed } = await confirmAction(
          "Confirmar eliminación",
          `¿Estás seguro de que deseas eliminar al usuario ${income.incomename}? Esta acción no se puede deshacer.`
        );

        if (isConfirmed) {
          deleteincome(income.id);
        }
      });
    });
  } else {
    const { exception } = await response.json();
    showMessage("error", "Error", exception);
  }
};
