<?php

/**
 * Controlador API de transacciones (Incomes + Expenses en una tabla).
 * Principios:
 *  - Acciones definidas por ?action=
 *  - Respuesta JSON uniforme: { message, exception, data? }
 *  - Códigos HTTP coherentes (200, 201, 400, 403, 404, 500)
 *  - Manejo de archivo (factura) opcional con eliminación segura al borrar.
 * Mejoras futuras:
 *  - Paginación / filtros avanzados (rango fechas, monto).
 *  - Validación MIME real del archivo (finfo).
 *  - Control de tamaño desde php.ini.
 */

require_once __DIR__ . '/../config/Validator.php';
require_once __DIR__ . '/../models/TransactionCategory.php';
require_once __DIR__ . '/../models/Transaction.php';
require_once __DIR__ . '/../models/User.php';

use App\Config\Validator;
use App\Models\Transaction;
use App\Models\User;
use App\Models\TransactionCategory;

if (isset($_GET['action'])) {
    session_start();
    $result = array(
        'message' => null,
        'exception' => null
    );

    if (isset($_SESSION['user'])) {
        switch ($_GET['action']) {
            case 'getCategories':
                // Lista categorías (opcionalmente filtradas por tipo).
                $type = isset($_GET['type']) ? $_GET['type'] : null;
                $transactionCategory = TransactionCategory::empty();
                $categories = $transactionCategory->getCategories($type);

                if ($categories) {
                    $result['message'] = 'Categorías obtenidas exitosamente';
                    $result['data'] = $categories;
                    http_response_code(200);
                } else {
                    $result['exception'] = 'No hay categorías registradas';
                    http_response_code(404);
                }
                break;

            case 'getTransactions':
                // Devuelve transacciones (todas o filtradas por tipo).
                $type = isset($_GET['type']) ? $_GET['type'] : null;
                $transaction = Transaction::empty();
                $transactions = $transaction->getTransactions($type);

                if ($transactions) {
                    $result['message'] = 'Transacciones obtenidas exitosamente';
                    $result['data'] = $transactions;
                    http_response_code(200);
                } else {
                    $result['exception'] = 'No hay transacciones registradas';
                    http_response_code(404);
                }
                break;

            case 'createTransaction':
                // Alta de transacción:
                //  - transaction_type requerido (income|expense)
                //  - Validación de campos en modelo.
                //  - Manejo de archivo si existe
                $currentUserId = $_SESSION['user']->getId();
                $_POST = Validator::validateForm($_POST);

                // Set transaction type from the request
                if (!isset($_POST['transaction_type']) ||
                    ($_POST['transaction_type'] !== Transaction::TYPE_INCOME &&
                     $_POST['transaction_type'] !== Transaction::TYPE_EXPENSE)) {
                    $result['exception'] = 'Tipo de transacción inválido';
                    http_response_code(400);
                    break;
                }

                $transaction = Transaction::fromForm($currentUserId, $_POST, '');
                $errors = $transaction->validateFields();

                if (empty($errors)) {
                    // Handle file upload if provided
                    $invoicePath = '';
                    if (isset($_FILES['invoiceImage']) && $_FILES['invoiceImage']['error'] === UPLOAD_ERR_OK) {
                        // Create upload directory if it doesn't exist
                        $uploadDir = __DIR__ . '/../../public/uploads/invoices/';
                        if (!file_exists($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }

                        // Generate unique filename
                        $fileExtension = pathinfo($_FILES['invoiceImage']['name'], PATHINFO_EXTENSION);
                        $fileName = 'invoice_' . uniqid() . '_' . time() . '.' . $fileExtension;
                        $targetPath = $uploadDir . $fileName;

                        // Move the uploaded file
                        if (move_uploaded_file($_FILES['invoiceImage']['tmp_name'], $targetPath)) {
                            // File uploaded successfully
                            $invoicePath = 'uploads/invoices/' . $fileName;
                            $transaction->setInvoicePath($invoicePath);
                        } else {
                            $result['exception'] = 'Error al subir el archivo de la factura';
                            http_response_code(500);
                            break;
                        }
                    }

                    if ($transaction->createTransaction()) {
                        $result['message'] = 'Transacción creada exitosamente';
                        http_response_code(201);
                    } else {
                        $result['exception'] = 'Error al crear la transacción';
                        http_response_code(500);
                    }
                } else {
                    $result['exception'] = $errors;
                    $result['message'] = 'Existen campos inválidos';
                    http_response_code(400);
                }
                break;

            case 'updateTransaction':
                // Actualización:
                //  - Conserva tipo (no editable).
                //  - Si sube nueva factura elimina la anterior.
                // Preserves old invoice if no new file uploaded
                $currentUserId = $_SESSION['user']->getId();
                $_POST = Validator::validateForm($_POST);

                // Check if transaction ID exists and is valid
                if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
                    $result['exception'] = 'ID de transacción inválido';
                    http_response_code(400);
                    break;
                }

                $transactionId = $_POST['id'];

                // Get current transaction data to preserve invoice path if no new file
                $transaction = Transaction::empty();
                $currentTransaction = $transaction->getTransactionById($transactionId);

                if (!$currentTransaction) {
                    $result['exception'] = 'Transacción no encontrada';
                    http_response_code(404);
                    break;
                }

                // Keep transaction type from the database
                $_POST['transaction_type'] = $currentTransaction['transaction_type'];

                // Check for current_invoice_path from the frontend form
                $invoicePath = isset($_POST['current_invoice_path']) ? $_POST['current_invoice_path'] : $currentTransaction['invoice_path'];

                // Create transaction object from form
                $transaction = Transaction::fromForm($currentUserId, $_POST, $invoicePath);
                $errors = $transaction->validateFields();

                if (empty($errors)) {
                    // Check if new invoice image is provided
                    if (isset($_FILES['invoiceImage']) && $_FILES['invoiceImage']['error'] === UPLOAD_ERR_OK) {
                        // Create upload directory if it doesn't exist
                        $uploadDir = __DIR__ . '/../../public/uploads/invoices/';
                        if (!file_exists($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }

                        // Generate unique filename
                        $fileExtension = pathinfo($_FILES['invoiceImage']['name'], PATHINFO_EXTENSION);
                        $fileName = 'invoice_' . uniqid() . '_' . time() . '.' . $fileExtension;
                        $targetPath = $uploadDir . $fileName;

                        // Move the uploaded file
                        if (move_uploaded_file($_FILES['invoiceImage']['tmp_name'], $targetPath)) {
                            // Delete old file if exists
                            if (!empty($currentTransaction['invoice_path'])) {
                                $oldFilePath = __DIR__ . '/../../public/' . $currentTransaction['invoice_path'];
                                if (file_exists($oldFilePath)) {
                                    unlink($oldFilePath);
                                }
                            }

                            // Update invoice path
                            $transaction->setInvoicePath('uploads/invoices/' . $fileName);
                        } else {
                            $result['exception'] = 'Error al subir el archivo de la factura';
                            http_response_code(500);
                            break;
                        }
                    }

                    // Update transaction in database
                    if ($transaction->updateTransaction($transactionId)) {
                        $result['message'] = 'Transacción actualizada exitosamente';
                        http_response_code(200);
                    } else {
                        $result['exception'] = 'Error al actualizar la transacción';
                        http_response_code(500);
                    }
                } else {
                    $result['exception'] = $errors;
                    $result['message'] = 'Existen campos inválidos';
                    http_response_code(400);
                }
                break;

            case 'deleteTransaction':
                // Eliminación lógica + file unlink si aplica.
                // Deletes transaction and attached file (model method)
                $_GET = Validator::validateForm($_GET);
                $transaction = Transaction::empty();
                $transactionId = $_GET['id'] ?? null;
                if (!isset($transactionId) || !is_numeric($transactionId)) {
                    $result['exception'] = 'ID de transacción inválido';
                    http_response_code(400);
                    break;
                }

                if ($transaction->deleteTransaction($transactionId)) {
                    $result['message'] = 'Transacción eliminada exitosamente';
                    http_response_code(200);
                } else {
                    $result['exception'] = 'Error al eliminar la transacción';
                    http_response_code(500);
                }
                break;

            default:
                // Acción no soportada.
                $result['exception'] = 'Acción no disponible';
                http_response_code(403);
                break;
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
