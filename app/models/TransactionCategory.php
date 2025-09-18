<?php

namespace App\Models;

require_once __DIR__ . '/../config/Database.php';
use App\Config\Database;

class TransactionCategory
{
    public const TYPE_INCOME = 'income';
    public const TYPE_EXPENSE = 'expense';

    private $id;
    private $name;
    private $type;
    private $created_at;
    private $updated_at;

    public function __construct($id, $name, $type, $created_at, $updated_at)
    {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function getUpdatedAt()
    {
        return $this->updated_at;
    }
    public function setName($name)
    {
        $this->name = $name;
    }

    public function setType($type)
    {
        if ($type !== self::TYPE_INCOME && $type !== self::TYPE_EXPENSE) {
            throw new \InvalidArgumentException('Type must be either "income" or "expense"');
        }
        $this->type = $type;
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
        return new self(null, null, null, null, null);
    }

    public function getCategories(string $type)
    {
        $sql = "SELECT * FROM transaction_categories";
        $params = [];

        if ($type === self::TYPE_INCOME || $type === self::TYPE_EXPENSE) {
            $sql .= " WHERE type = ?";
            $params[] = $type;
        } elseif ($type !== null) {
            throw new \InvalidArgumentException('Type must be either "income" or "expense"');
        }

        return Database::getRows($sql, $params);
    }

    public function getCategoryById(int $id)
    {
        $sql = "SELECT * FROM transaction_categories WHERE id = ?";
        $params = [$id];
        return Database::getRow($sql, $params);
    }
}
