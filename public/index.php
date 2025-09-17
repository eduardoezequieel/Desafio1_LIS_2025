<?php

$url = $_GET['url'] ?? 'home';

switch ($url) {
    case 'login':
        require_once __DIR__ . '/../views/auth/login.php';
        break;
    case 'dashboard':
        require_once __DIR__ . '/../views/dashboard/index.php';
        break;
    case 'dashboard/users':
        require_once __DIR__ . '/../views/dashboard/users.php';
        break;
    case 'dashboard/incomes':
        require_once __DIR__ . '/../views/dashboard/incomes.php';
        break;
    default:
        http_response_code(404);
        require_once __DIR__ . '/../views/404.php';
        break;
}
