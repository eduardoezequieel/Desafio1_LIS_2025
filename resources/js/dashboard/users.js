document.addEventListener("DOMContentLoaded", () => {
  fetchUsers();
});

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

    console.log(tbody);

    data.forEach((user) => {
      const lastUpdated = user.updated_at
        ? new Date(user.updated_at).toLocaleDateString()
        : new Date(user.created_at).toLocaleDateString();
      html += `
            <tr>
                <th scope="row">${user.id}</th>
                <td class="pt-2">${user.username}</td>
                <td class="pt-2">${lastUpdated}</td>
                <td>
                    <button class="btn btn-sm btn-secondary">Editar</button>
                    <button class="btn btn-sm btn-danger">Eliminar</button>
                </td>
            </tr>
        `;
    });

    tbody.innerHTML = html;
  } else {
    const { exception } = await response.json();
    showMessage("error", "Error", exception);
  }
};
