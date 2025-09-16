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

    if (isset($_SESSION['user'])) {
        switch ($_GET['action']) {
            case 'getUsers':
                $currentId = $_SESSION['user']['id'];
                $user = User::empty();
                $data = $user->getUsers($currentId);
                if ($data) {
                    $result['message'] = 'Existen ' . count($data) . ' usuarios registrados';
                    $result['data'] = $data;
                    http_response_code(200);
                } else {
                    $result['exception'] = 'Se ha producido un error desconocido';
                    http_response_code(404);
                }
                break;
            case 'logOut':
                session_destroy();
                $result['message'] = 'Sesión cerrada exitosamente';
                http_response_code(200);
                break;
            default:
                $result['message'] = 'Acción no disponible fuera de la sesión';
                print(json_encode($result));
                http_response_code(403);
                exit();
        }
    } else {
        switch ($_GET['action']) {
            case 'logIn':
                $_POST = Validator::validateForm($_POST);

                $user = User::fromCredentials($_POST['username'], $_POST['password']);
                $errors = $user->validateFields();

                error_log("Usuario recibido: " . $user->getUsername());

                if (empty($errors)) {
                    $returnedUser = $user->checkCredentials();

                    error_log("Usuario: " . $user->getUsername());

                    if ($returnedUser) {
                        $_SESSION['user'] = $returnedUser;
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
            case 'seed':
                $user = User::fromCredentials('admin', 'admin123');
                if ($user->generateAdmin()) {
                    $result['message'] = 'Admin user created successfully';
                    http_response_code(201);
                } else {
                    $result['exception'] = 'Error creating admin user';
                    http_response_code(500);
                }
                break;
            default:
                $result['message'] = 'Invalid endpoint';
                http_response_code(404);
                break;
        }
    }

    header('content-type: application/json; charset=utf-8');
    print(json_encode($result));
} else {
    print(json_encode('Recurso no disponible'));
    http_response_code(404);
}
