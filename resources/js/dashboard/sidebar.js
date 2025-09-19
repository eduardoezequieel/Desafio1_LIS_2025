/**
 * Controla acciones del sidebar.
 * Actualmente solo:
 *  - Cerrar sesión con confirmación.
 * Mejorable:
 *  - Resaltar ítem activo por JS (ahora lo hace el backend).
 *  - Añadir listeners condicionales según permisos.
 */
const API_USERS = "/Desafio1_LIS_2025/app/api/users.php?action=";

const logOutItem = document.getElementById("logout-link");

logOutItem.addEventListener("click", async (e) => {
  /**
   * Flujo logout:
   * 1. Pregunta confirmación.
   * 2. POST al backend (mantiene semántica).
   * 3. Redirección tras éxito.
   * 4. Manejo de error genérico si algo falla.
   */
  e.preventDefault();

  const { isConfirmed } = await confirmAction(
    "¿Cerrar sesión?",
    "¿Estás seguro de que deseas cerrar sesión?"
  );

  if (!isConfirmed) return;

  const response = await fetch(API_USERS + "logOut", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
  });

  if (response.ok) {
    showMessage("success", "Éxito", "Sesión cerrada correctamente.").then(
      () => {
        window.location.href = "/Desafio1_LIS_2025/public/login";
      }
    );
  } else {
    showMessage("error", "Error", "No se pudo cerrar sesión.");
  }
});
