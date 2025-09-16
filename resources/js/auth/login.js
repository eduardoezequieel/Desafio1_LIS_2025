const API_USERS = "/Desafio1_LIS_2025/app/api/users.php?action=";

document.querySelector("#login-form").addEventListener("submit", async (e) => {
  e.preventDefault();

  const response = await fetch(API_USERS + "logIn", {
    method: "POST",
    body: new FormData(e.target),
  });

  const data = await response.json();

  if (await !response.ok) {
    showMessage("error", "Error", data.exception);
    return;
  }

  showMessage("success", "Éxito", "¡Has iniciado sesión correctamente!").then(
    () => {
      window.location.href = "/Desafio1_LIS_2025/public/dashboard";
    }
  );
});
