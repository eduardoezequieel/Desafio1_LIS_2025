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
    }

    public function validateFields(): array
    {
        $errors = [];
        if (empty($this->username)) {
            $errors[] = 'El usuario es requerido';
        }
        if (empty($this->password_hash)) {
            $errors[] = 'La contraseña es requerida';
        }
        return $errors;
    }

    public function getUsers(int $idToExclude): array|false
    {
        $sql = "SELECT id, username, created_at, updated_at FROM users WHERE id != ? AND username != 'admin'";
        $params = [$idToExclude];
        return Database::getRows($sql, $params);
    }

    public function checkCredentials(): User|false
    {
        $sql = "SELECT * FROM users WHERE username = ?";
        $params = [$this->username];
        $response = Database::getRows($sql, $params);

        if ($response && count($response) === 1) {
            $user = $response[0];
            if (password_verify($this->password_hash, $user['password_hash'])) {
                return new self(
                    $user['id'],
                    $user['username'],
                    '',
                    $user['created_at'],
                    $user['updated_at']
                );
            }
        }

        return false;
    }

    public function createUser(): bool
    {
        $sql = "INSERT INTO users (username, password_hash) VALUES (?, ?)";
        $params = [$this->username, password_hash($this->password_hash, PASSWORD_BCRYPT)];
        return Database::executeRow($sql, $params);
    }

    public function deleteUser(int $id): bool
    {
        $sql = "DELETE FROM users WHERE id = ?";
        $params = [$id];
        return Database::executeRow($sql, $params);
    }

    public function updateUser(): bool
    {
        $sql = "UPDATE users SET username = ?, password_hash = ? WHERE id = ?";
        $params = [$this->username, password_hash($this->password_hash, PASSWORD_BCRYPT), $this->id];
        return Database::executeRow($sql, $params);
    }

    public function generateAdmin()
    {
        $sql = "INSERT INTO users (username, password_hash) VALUES (?, ?)";
        $params = [$this->username, password_hash($this->password_hash, PASSWORD_BCRYPT)];
        return Database::executeRow($sql, $params);
    }

    public static function fromCredentials(string $username, string $password_hash): self
    {
        return new self(0, $username, $password_hash, '', '');
    }

    public static function empty(): self
    {
        return new self(0, '', '', '', '');
    }


}
