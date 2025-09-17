<?php

namespace App\Models;

require_once __DIR__ . '/../config/Database.php';
use App\Config\Database;

class Income
{
    private $id;
    private $user_id;
    private $amount;
    private $date;
    private $type_id;
    private $invoice_path;
    private $created_at;
    private $updated_at;

    public function __construct($id, $user_id, $amount, $date, $type_id, $invoice_path, $created_at, $updated_at)
    {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->amount = $amount;
        $this->date = $date;
        $this->type_id = $type_id;
        $this->invoice_path = $invoice_path;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getTypeId()
    {
        return $this->type_id;
    }

    public function getInvoicePath()
    {
        return $this->invoice_path;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }

    public function setTypeId($type_id)
    {
        $this->type_id = $type_id;
    }

    public function setInvoicePath($invoice_path)
    {
        $this->invoice_path = $invoice_path;
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
        return new self(null, null, null, null, null, null, null, null);
    }

    public static function fromForm(int $user_id, array $data, string $file_route): self
    {
        return new self(
            null,
            $user_id,
            $data['amount'] ?? null,
            $data['date'] ?? null,
            $data['type_id'] ?? null,
            $file_route,
            null,
            null
        );
    }

    public function validateFields(): array
    {
        $errors = [];

        if (empty($this->amount) || !is_numeric($this->amount) || $this->amount <= 0) {
            $errors[] = 'Monto inválido';
        }
        if (empty($this->date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->date)) {
            $errors[] = 'Fecha inválida';
        }
        if (empty($this->type_id) || !is_numeric($this->type_id) || $this->type_id <= 0) {
            $errors[] = 'ID de tipo inválido';
        }
        // La ruta de la factura puede ser opcional, pero si se proporciona, debe ser una cadena válida
        if (!empty($this->invoice_path) && !is_string($this->invoice_path)) {
            $errors[] = 'Ruta de factura inválida';
        }

        return $errors;
    }

    public function getIncomes()
    {
        $sql = "SELECT * FROM incomes INNER JOIN income_types ON incomes.type_id = income_types.id";
        $params = array();
        return Database::getRows($sql, $params);
    }

    public function createIncome()
    {
        $sql = "INSERT INTO incomes (user_id, amount, date, type_id, invoice_path) VALUES (?, ?, ?, ?, ?)";
        $params = array($this->user_id, $this->amount, $this->date, $this->type_id, $this->invoice_path);
        return Database::executeRow($sql, $params);
    }
}
