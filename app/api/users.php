<?php

use App\Config\Validator;

if (isset($_GET['action'])) {
    session_start();
    $result = array(
        'status' => 0,
        'message' => null,
        'exception' => null
    );

    switch ($_GET['action']) {
        case 'logIn':
            $_POST = Validator::validateForm($_POST);

            if ($_POST['username']) {
                if ($_POST['password']) {
                    $result['message'] = "Login with {$_POST['username']} and {$_POST['password']}";
                } else {
                    $result['exception'] = 'Password is required';
                }
            } else {
                $result['exception'] = 'Username is required';
            }
            break;

        default:
            $result['message'] = 'Invalid endpoint';
            break;
    }

    header('content-type: application/json; charset=utf-8');
    print(json_encode($result));
}
