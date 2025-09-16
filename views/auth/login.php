<?php
session_start();

if (isset($_SESSION['user'])) {
    header('Location: /Desafio1_LIS_2025/public/dashboard');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="/Desafio1_LIS_2025/resources/css/globals.css">
	<link rel="stylesheet" href="/Desafio1_LIS_2025/resources/css/login.css">
	<link href="/Desafio1_LIS_2025/resources/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
	<title>Iniciar sesión</title>
</head>

<body>
	<div class="d-flex flex-column align-items-center justify-content-center vh-100">
		<h2 class="heading text-center">Iniciar sesión</h2>
		<form id="login-form">
			<div class="mb-3">
				<label for="username" class="form-label">Usuario</label>
				<input type="text" name="username" id="username-input" class="form-control">
			</div>
			<div class="mb-4">
				<label for="password" class="form-label">Contraseña</label>
				<input type="password" name="password" id="password-input" class="form-control">
			</div>
			<div class="d-grid gap-2">
				<button class="btn btn-primary" type="submit">Iniciar sesión</button>
			</div>
		</form>
	</div>
	<script src="/Desafio1_LIS_2025/resources/lib/bootstrap/bootstrap.bundle.min.js"></script>
	<script src="/Desafio1_LIS_2025/resources/lib/swal/sweetalert2@11.js"></script>
	<script src="/Desafio1_LIS_2025/resources/js/components.js"></script>
	<script src="/Desafio1_LIS_2025/resources/js/auth/login.js"></script>
</body>

</html>