<?php

$url = $_GET['url'] ?? 'home';

switch ($url) {
    case 'login':
        require_once __DIR__ . '/../views/auth/login.php';
        break;
    default:
        http_response_code(404);
        echo 'Pagina no encontrada';
}
