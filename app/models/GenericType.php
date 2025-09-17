<?php

namespace App\Models;

require_once __DIR__ . '/../config/Database.php';
use App\Config\Database;

class GenericType
{
    public const TYPE_INCOME = 'income';
    public const TYPE_EXPENSE = 'expense';

    private $id;
    private $name;
    private $created_at;
    private $updated_at;

    public function __construct($id, $name, $created_at, $updated_at)
    {
        $this->id = $id;
        $this->name = $name;
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
        return new self(null, null, null, null);
    }

    public function getTypes(string $type = self::TYPE_INCOME)
    {
        if ($type === self::TYPE_INCOME || $type === self::TYPE_EXPENSE) {
            $sql = "SELECT * FROM {$type}_types";
            $params = [];

            return Database::getRows($sql, $params);
        } else {
            throw new \InvalidArgumentException('Type must be either "income" or "expense"');
            return [];
        }
    }
}
