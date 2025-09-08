<?php

namespace App\Models;

require_once __DIR__ . '/../config/Database.php';
use App\Config\Database;

class User
{
    private int $id;
    private string $username;
    private string $password_hash;
    private string $created_at;
    private string $updated_at;

    public function __construct(
        int $id,
        string $username,
        string $password_hash,
        string $created_at,
        string $updated_at
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->password_hash = $password_hash;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPasswordHash(): string
    {
        return $this->password_hash;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): string
    {
        return $this->updated_at;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function setPasswordHash(string $password_hash): void
    {
        $this->password_hash = $password_hash;
    }

    public function setCreatedAt(string $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function setUpdatedAt(string $updated_at): void
    {
        $this->updated_at = $updated_at;
    }

    public function validateFields(): array
    {
        $errors = [];
        if (empty($this->username)) {
            $errors[] = 'Username is required.';
        }
        if (empty($this->password_hash)) {
            $errors[] = 'Password is required.';
        }
        return $errors;
    }

    public function checkCredentials(): bool
    {
        $sql = "SELECT * FROM users WHERE username = ?";
        $params = [$this->username];
        $response = Database::getRows($sql, $params);

        if ($response && count($response) === 1) {
            $user = $response[0];
            return $this->password_hash === $user['password_hash'];
        } else {
            return false;
        }
    }

    public static function fromCredentials(string $username, string $password_hash): self
    {
        return new self(0, $username, $password_hash, '', '');
    }


}
