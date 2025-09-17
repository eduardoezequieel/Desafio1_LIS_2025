const addBtn = document.getElementById("new-user-btn");
const form = document.getElementById("user-form");
const userLabel = document.getElementById("user-modal-label");
const saveUserBtn = document.getElementById("save-user-btn");
const userModal = new bootstrap.Modal(document.getElementById("userModal"));
let userModalMode = "create"; // 'create' o 'edit'

addBtn.addEventListener("click", () => {
  form.reset();
  userModal.show();
  userModalMode = "create";
  userLabel.textContent = "Crear usuario";
});

saveUserBtn.addEventListener("click", () => form.requestSubmit());

form.addEventListener("submit", async (e) => {
  e.preventDefault();

  const formData = new FormData(form);

  if (formData.get("id") === "" && userModalMode === "edit") {
    showMessage(
      "error",
      "Error",
      "No se ha proporcionado un ID de usuario válido para la edición."
    );
    return;
  }

  const response = await fetch(
    API_USERS + (userModalMode === "create" ? "createUser" : "updateUser"),
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
      userModalMode === "create" ? "creado" : "actualizado"
    } correctamente`
  ).then(() => {
    userModal.hide();
    form.reset();
    fetchUsers();
  });
});

document.addEventListener("DOMContentLoaded", () => {
  fetchUsers();
});

const deleteUser = async (id) => {
  const formData = new FormData();
  formData.append("id", id);

  const response = await fetch(
    API_USERS + "deleteUser&" + new URLSearchParams(formData),
    {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
      },
    }
  );
  if (response.ok) {
    const { message } = await response.json();
    showMessage("success", "Éxito", message).then(() => fetchUsers());
  } else {
    const { exception } = await response.json();
    showMessage("error", "Error", exception);
  }
};

const fetchUsers = async () => {
  const response = await fetch(API_USERS + "getUsers", {
    method: "GET",
    headers: {
      "Content-Type": "application/json",
    },
  });

  if (response.ok || response.status === 201 || response.status === 404) {
    const { data } = await response.json();

    const tbody = document.querySelector("#users-table-body");

    let html = "";

    data.forEach((user) => {
      const dateTimeOptions = {
        year: "numeric",
        month: "numeric",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
      };

      const lastUpdated = user.updated_at
        ? new Date(user.updated_at).toLocaleString(undefined, dateTimeOptions)
        : new Date(user.created_at).toLocaleString(undefined, dateTimeOptions);
      html += `
            <tr>
                <th scope="row">${user.id}</th>
                <td class="pt-2">${user.username}</td>
                <td class="pt-2">${lastUpdated}</td>
                <td>
                    <button class="btn btn-sm btn-secondary edit-user-btn">Editar</button>
                    <button class="btn btn-sm btn-danger delete-user-btn">Eliminar</button>
                </td>
            </tr>
        `;
    });

    tbody.innerHTML = html;

    document.querySelectorAll(".edit-user-btn").forEach((btn, index) => {
      btn.addEventListener("click", () => {
        form.reset();
        userLabel.textContent = "Editar usuario";

        const user = data[index];

        form.username.value = user.username;
        form.id.value = user.id;

        userModal.show();
        userModalMode = "edit";
      });
    });

    document.querySelectorAll(".delete-user-btn").forEach((btn, index) => {
      btn.addEventListener("click", () => {
        const user = data[index];
        if (
          confirm(
            `¿Estás seguro de que deseas eliminar al usuario ${user.username}?`
          )
        ) {
          deleteUser(user.id);
        }
      });
    });
  } else {
    const { exception } = await response.json();
    showMessage("error", "Error", exception);
  }
};
