<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="/Desafio1_LIS_2025/resources/css/globals.css">
	<link rel="stylesheet" href="/Desafio1_LIS_2025/resources/css/login.css">
	<link href="/Desafio1_LIS_2025/resources/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
	<title>Document</title>
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
				<button class="btn btn-primary" type="button">Iniciar sesión</button>
			</div>
		</form>
	</div>
	<script src="/Desafio1_LIS_2025/resources/lib/bootstrap/bootstrap.bundle.min.js"></script>
	<script src="/Desafio1_LIS_2025/resources/js/auth/login.js"></script>
</body>

</html>