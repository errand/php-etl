<?php

namespace Models;

use Database\Connection;
use PDO;

abstract class BaseModel
{
    protected PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connection::getInstance();
    }

    protected function insertIfNotExists(string $table, array $data, string $uniqueField): int
    {
        // Проверяем существование
        $checkQuery = $this->pdo->prepare("SELECT id FROM $table WHERE $uniqueField = :value");
        $checkQuery->execute(['value' => $data[$uniqueField]]);
        $result = $checkQuery->fetch();

        if ($result) {
            return $result['id'];
        }

        // Вставляем новую запись
        $fields = implode(',', array_keys($data));
        $placeholders = implode(',', array_map(fn($field) => ":$field", array_keys($data)));
        $insertQuery = $this->pdo->prepare("INSERT INTO $table ($fields) VALUES ($placeholders)");
        $insertQuery->execute($data);

        return $this->pdo->lastInsertId();
    }
}