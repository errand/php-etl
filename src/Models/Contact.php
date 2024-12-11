<?php
namespace Models;

use PDO;
use Utils\Logger;

class Contact extends BaseModel
{
    private Logger $logger;

    public function __construct()
    {
        parent::__construct();
        $this->logger = new Logger();
    }

    public function findOrCreate(string $name, string $phones): int
    {
        $contact = $this->findByName($name);

        if ($contact) {
            return $contact['id'];
        }

        return $this->create(['name' => $name, 'phones' => $phones]);
    }

    private function findByName(string $name): ?array
    {
        $query = $this->pdo->prepare("SELECT * FROM contacts WHERE name = :name");
        $query->execute(['name' => $name]);
        return $query->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function create(array $data): int
    {
        $query = $this->pdo->prepare("INSERT INTO contacts (name, phones) VALUES (:name, :phones)");
        $query->execute([
            'name' => $data['name'],
            'phones' => $data['phones']
        ]);

        $this->logger->log("Создан контакт: " . json_encode($data));
        return (int) $this->pdo->lastInsertId();
    }
}
