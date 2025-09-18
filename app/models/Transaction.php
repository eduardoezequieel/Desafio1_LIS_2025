<?php

namespace App\Models;

require_once __DIR__ . '/../config/Database.php';
use App\Config\Database;

class Transaction
{
    public const TYPE_INCOME = 'income';
    public const TYPE_EXPENSE = 'expense';

    private $id;
    private $user_id;
    private $transaction_type;
    private $amount;
    private $date;
    private $category_id;
    private $invoice_path;
    private $description;
    private $created_at;
    private $updated_at;

    public function __construct($id, $user_id, $transaction_type, $amount, $date, $category_id, $invoice_path, $description, $created_at, $updated_at)
    {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->transaction_type = $transaction_type;
        $this->amount = $amount;
        $this->date = $date;
        $this->category_id = $category_id;
        $this->invoice_path = $invoice_path;
        $this->description = $description;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }
    public function getUserId()
    {
        return $this->user_id;
    }
    public function getTransactionType()
    {
        return $this->transaction_type;
    }
    public function getAmount()
    {
        return $this->amount;
    }
    public function getDate()
    {
        return $this->date;
    }
    public function getCategoryId()
    {
        return $this->category_id;
    }
    public function getInvoicePath()
    {
        return $this->invoice_path;
    }
    public function getDescription()
    {
        return $this->description;
    }
    public function getCreatedAt()
    {
        return $this->created_at;
    }
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    // Setters
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }
    public function setTransactionType($transaction_type)
    {
        $this->transaction_type = $transaction_type;
    }
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }
    public function setDate($date)
    {
        $this->date = $date;
    }
    public function setCategoryId($category_id)
    {
        $this->category_id = $category_id;
    }
    public function setInvoicePath($invoice_path)
    {
        $this->invoice_path = $invoice_path;
    }
    public function setDescription($description)
    {
        $this->description = $description;
    }
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
    }

    public static function empty()
    {
        return new self(null, null, null, null, null, null, null, null, null, null);
    }

    public static function fromForm(int $user_id, array $data, string $file_route): self
    {
        return new self(
            null,
            $user_id,
            $data['transaction_type'] ?? null,
            $data['amount'] ?? null,
            $data['date'] ?? null,
            $data['category_id'] ?? null,
            $file_route,
            $data['description'] ?? null,
            null,
            null
        );
    }

    public function validateFields(): array
    {
        $errors = [];

        if (empty($this->transaction_type) ||
            ($this->transaction_type !== self::TYPE_INCOME && $this->transaction_type !== self::TYPE_EXPENSE)) {
            $errors[] = 'Tipo de transacción inválido';
        }
        if (empty($this->amount) || !is_numeric($this->amount) || $this->amount <= 0) {
            $errors[] = 'Monto inválido';
        }
        if (empty($this->date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->date)) {
            $errors[] = 'Fecha inválida';
        }
        if (empty($this->category_id) || !is_numeric($this->category_id) || $this->category_id <= 0) {
            $errors[] = 'Categoría inválida';
        }
        if (!empty($this->invoice_path) && !is_string($this->invoice_path)) {
            $errors[] = 'Ruta de factura inválida';
        }

        return $errors;
    }

    public function getTransactions($type = null)
    {
        $sql = "SELECT t.id, t.user_id, t.transaction_type, t.amount, t.date, t.category_id, 
                tc.name AS category_name, t.invoice_path, t.description 
                FROM transactions t 
                INNER JOIN transaction_categories tc ON t.category_id = tc.id";
        $params = array();

        if ($type === self::TYPE_INCOME || $type === self::TYPE_EXPENSE) {
            $sql .= " WHERE t.transaction_type = ?";
            $params[] = $type;
        }

        $sql .= " ORDER BY t.date DESC";
        return Database::getRows($sql, $params);
    }

    public function deleteTransaction(int $id)
    {
        // First, get the transaction record to check if there's an invoice file
        $transaction = $this->getTransactionById($id);

        if ($transaction && !empty($transaction['invoice_path'])) {
            // If there's an invoice file, delete it
            $filePath = __DIR__ . '/../../public/' . $transaction['invoice_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // Then delete the database record
        $sql = "DELETE FROM transactions WHERE id = ?";
        $params = array($id);
        return Database::executeRow($sql, $params);
    }

    public function createTransaction()
    {
        $sql = "INSERT INTO transactions (user_id, transaction_type, amount, date, category_id, invoice_path, description) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $params = array(
            $this->user_id,
            $this->transaction_type,
            $this->amount,
            $this->date,
            $this->category_id,
            $this->invoice_path,
            $this->description
        );
        return Database::executeRow($sql, $params);
    }

    public function updateTransaction(int $id)
    {
        $sql = "UPDATE transactions SET amount = ?, date = ?, category_id = ?, invoice_path = ?, description = ? WHERE id = ?";
        $params = array($this->amount, $this->date, $this->category_id, $this->invoice_path, $this->description, $id);
        return Database::executeRow($sql, $params);
    }

    public function getTransactionById(int $id)
    {
        $sql = "SELECT * FROM transactions WHERE id = ?";
        $params = array($id);
        return Database::getRow($sql, $params);
    }
}
