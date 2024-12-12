<?php

namespace Models;

use Database\Connection;
use Utils\Logger;
use PDO;

/**
 * Abstract Class BaseModel
 *
 * Абстрактная модель, содержащая базовые методы для реализации в дочерних классах
 * Предоставляет методы логгирования, поиска или создания записей.
 */
abstract class BaseModel
{
    /**
     * @var PDO
     */
    protected PDO $pdo;

    /**
     * @var Logger
     */
    protected Logger $logger;

    /**
     * @var string
     */
    protected string $table;

    /**
     * BaseModel constructor.
     */
    public function __construct()
    {
        $this->pdo = Connection::getInstance();
        $this->logger = new Logger();
    }

    /**
     * Получить название таблицы.
     *
     * @return string
     *
     * @throws \Exception
     */
    protected function getTable(): string
    {
        if (!isset($this->table)) {
            throw new \Exception("Table is not defined in " . static::class);
        }
        return $this->table;
    }

    /**
     * Вставить данные в таблицу, если записи с таким уникальным полем не существует.
     *
     * @param array $data Данные для вставки.
     * @param string $uniqueField Уникальное поле для проверки.
     *
     * @return int ID вставленной или существующей записи.
     */
    protected function insertIfNotExists(array $data, string $uniqueField): int
    {
        $table = $this->getTable();

        $checkQuery = $this->pdo->prepare("SELECT id FROM $table WHERE $uniqueField = :value");
        $checkQuery->execute(['value' => $data[$uniqueField]]);
        $result = $checkQuery->fetch();

        if ($result) {
            return $result['id'];
        }

        return $this->create($data);
    }

    /**
     * Создать новую запись в таблице.
     *
     * @param array $data Данные для вставки.
     *
     * @return int ID вставленной записи.
     */
    protected function create(array $data): int
    {
        $table = $this->getTable();

        $fields = implode(',', array_keys($data));
        $placeholders = implode(',', array_map(fn($field) => ":$field", array_keys($data)));
        $query = $this->pdo->prepare("INSERT INTO $table ($fields) VALUES ($placeholders)");
        $query->execute($data);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Обновить существующую запись в таблице.
     *
     * @param array $data Данные для обновления.
     * @param int $id ID записи для обновления.
     *
     * @return bool Возвращает true, если обновление успешно.
     */
    protected function update(array $data, int $id): bool
    {
        $table = $this->getTable();

        $fields = implode(',', array_map(fn($field) => "$field = :$field", array_keys($data)));
        $query = $this->pdo->prepare("UPDATE $table SET $fields WHERE id = :id");
        $data['id'] = $id;

        return $query->execute($data);
    }

    /**
     * Найти запись по заданному полю.
     *
     * @param string $field Поле для поиска.
     * @param mixed $value Значение для поиска.
     *
     * @return array|null Возвращает найденную запись или null, если не найдена.
     */
    protected function findByField(string $field, $value): ?array
    {
        $table = $this->getTable();

        $query = $this->pdo->prepare("SELECT * FROM $table WHERE $field = :value");
        $query->execute(['value' => $value]);
        return $query->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}
