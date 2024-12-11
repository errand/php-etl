<?php
namespace Models;

use PDO;
use Utils\Logger;

class Estate extends BaseModel
{
    private Logger $logger;

    public function __construct()
    {
        parent::__construct();
        $this->logger = new Logger();
    }

    private function getById(string $id): ?array
    {
        $query = $this->pdo->prepare("SELECT * FROM estate WHERE id = :id");
        $query->execute(['id' => $id]);
        return $query->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function create(array $data): int
    {
        $fields = implode(',', array_keys($data));
        $placeholders = implode(',', array_map(fn($field) => ":$field", array_keys($data)));
        $query = $this->pdo->prepare("INSERT INTO estate ($fields) VALUES ($placeholders)");
        $query->execute($data);

        return $this->pdo->lastInsertId();
    }

    public function update(array $data, int $id): bool
    {
        $fields = implode(',', array_map(fn($field) => "$field = :$field", array_keys($data)));
        $query = $this->pdo->prepare("UPDATE estate SET $fields WHERE id = :id");
        $data['id'] = $id;

        return $query->execute($data);
    }

    public function updateOrCreate(array $data, string $id = null): int
    {
        $this->logger->log("Updating or creating Estate");
        $this->logger->log("Data: " . json_encode($data));
        if ($id) {
            $existing = $this->getById($id);

            if ($existing) {
                $changes = array_diff_assoc($data, $existing);

                if (!empty($changes)) {
                    $this->update($data, $id);
                    $this->logger->log("Обновлена запись ID $id: " . json_encode($changes));
                } else {
                    $this->logger->log("Данные для записи ID $id не изменились.");
                }

                return $id;
            }
        }

        $newId = $this->create($data);
        $this->logger->log("Создана новая запись ID $newId: " . json_encode($data));
        return $newId;
    }
}
