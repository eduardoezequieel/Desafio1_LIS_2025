<?php

require_once __DIR__ . '/../config/Validator.php';
require_once __DIR__ . '/../models/user.php';

use App\Config\Validator;
use App\Models\User;

if (isset($_GET['action'])) {
    session_start();
    $result = array(
        'message' => null,
        'exception' => null
    );

    switch ($_GET['action']) {
        case 'logIn':
            $_POST = Validator::validateForm($_POST);

            $user = User::fromCredentials($_POST['username'], $_POST['password']);
            $errors = $user->validateFields();

            error_log("Usuario recibido: " . $user->getUsername());

            if (empty($errors)) {
                $validLogin = $user->checkCredentials();

                error_log("Usuario: " . $user->getUsername());

                if ($validLogin) {
                    $_SESSION['user'] = $user;
                    $result['message'] = 'Autenticación exitosa';
                    http_response_code(200);
                } else {
                    error_log("Error de autenticación para el usuario: " . $user->getUsername());
                    $result['exception'] = 'Credenciales inválidas';
                    http_response_code(401);
                }
            } else {
                $result['exception'] = $errors;
                $result['message'] = 'Existen campos inválidos';
                http_response_code(400);
            }
            break;

        default:
            $result['message'] = 'Invalid endpoint';
            http_response_code(404);
            break;
    }

    header('content-type: application/json; charset=utf-8');
    print(json_encode($result));
} else {
    print(json_encode('Recurso no disponible'));
    http_response_code(404);
}
