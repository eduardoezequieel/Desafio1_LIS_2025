<?php

require_once __DIR__ . '/../config/Validator.php';
require_once __DIR__ . '/../models/GenericType.php';
require_once __DIR__ . '/../models/Income.php';

use App\Config\Validator;
use App\Models\Income;
use App\Models\GenericType;

if (isset($_GET['action'])) {
    session_start();
    $result = array(
        'message' => null,
        'exception' => null
    );

    if (isset($_SESSION['user'])) {
        switch ($_GET['action']) {
            case 'getIncomeTypes':
                $genericType = GenericType::empty();
                $incomeTypes = $genericType->getTypes($genericType::TYPE_INCOME);

                if ($incomeTypes) {
                    $result['message'] = 'Tipos de entradas obtenidos exitosamente';
                    $result['data'] = $incomeTypes;
                    http_response_code(200);
                } else {
                    $result['exception'] = 'No hay tipos de entradas registrados';
                    http_response_code(404);
                }
                break;
            case 'getIncomes':
                $income = Income::empty();
                $incomes = $income->getIncomes();

                if ($incomes) {
                    $result['message'] = 'Entradas obtenidas exitosamente';
                    $result['data'] = $incomes;
                    http_response_code(200);
                } else {
                    $result['exception'] = 'No hay entradas registradas';
                    http_response_code(404);
                }
                break;

            case 'createIncome':
                $currentUserId = $_SESSION['user']->getId();
                $_POST = Validator::validateForm($_POST);

                $income = Income::fromForm($currentUserId, $_POST, '');
                $errors = $income->validateFields();

                if (empty($errors)) {
                    if (isset($_FILES['invoiceImage']) && $_FILES['invoiceImage']['error'] === UPLOAD_ERR_OK) {
                        // Create upload directory if it doesn't exist
                        $uploadDir = __DIR__ . '/../../public/uploads/invoices/';
                        if (!file_exists($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }

                        // Generate unique filename
                        $fileExtension = pathinfo($_FILES['invoiceImage']['name'], PATHINFO_EXTENSION);
                        $fileName = 'invoice_' . $incomeId . '_' . time() . '.' . $fileExtension;
                        $targetPath = $uploadDir . $fileName;


                        // Move the uploaded file
                        if (move_uploaded_file($_FILES['invoiceImage']['tmp_name'], $targetPath)) {
                            // File uploaded successfully
                            $income->setInvoicePath('uploads/invoices/' . $fileName);
                            if ($income->createIncome()) {
                                $result['message'] = 'Entrada creada exitosamente';
                                http_response_code(201);
                            } else {
                                $result['exception'] = 'Error al crear la entrada';
                                http_response_code(500);
                            }
                        } else {
                            $result['exception'] = 'Error al subir el archivo de la factura';
                            http_response_code(500);
                            break;
                        }
                        http_response_code(201);
                        if ($income->createIncome()) {
                            $result['message'] = 'Entrada creada exitosamente';
                            http_response_code(201);
                        } else {
                            $result['exception'] = 'Error al crear la entrada';
                            http_response_code(500);
                        }
                    } else {
                        $result['exception'] = 'Archivo de factura no encontrado';
                        http_response_code(400);
                    }
                } else {
                    $result['exception'] = $errors;
                    $result['message'] = 'Existen campos inválidos';
                    http_response_code(400);
                }
                break;
            default:
                $result['exception'] = 'Acción no disponible';
                print(json_encode($result));
                http_response_code(403);
                exit();
        }
    } else {
        $result['exception'] = 'Recurso no disponible';
        http_response_code(404);
    }

    header('content-type: application/json; charset=utf-8');
    print(json_encode($result));
} else {
    print(json_encode('Recurso no disponible'));
    http_response_code(404);
}
