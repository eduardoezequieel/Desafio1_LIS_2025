const API_USERS = "/DesafioLIS_Ciclo2_2025/app/api/users.php?action=";

document.querySelector("#login-form").addEventListener("submit", async (e) => {
  e.preventDefault();

  const response = await fetch(API_USERS + "logIn", {
    method: "POST",
    body: new FormData(e.target),
  });

  const data = await response.json();

  console.log(data);
});
