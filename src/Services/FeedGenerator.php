<?php

namespace Services;

use Database\Connection;
use DOMDocument;
use PDO;

/**
 * Класс для генерации XML лент с данными агентств, контактов, менеджеров и объектов недвижимости.
 *
 * @package Services
 */
class FeedGenerator
{
    /**
     * @var PDO
     * Экземпляр соединения с базой данных.
     */
    private PDO $pdo;

    /**
     * FeedGenerator constructor.
     * Инициализирует соединение с базой данных.
     */
    public function __construct()
    {
        $this->pdo = Connection::getInstance();
    }

    /**
     * Генерация XML ленты агентств недвижимости.
     *
     * @return string XML строка с данными агентств.
     */
    public function generateAgenciesFeed(): string
    {
        // Создание нового XML документа
        $dom = new DOMDocument('1.0', 'UTF-8');
        $root = $dom->createElement('agencies');
        $dom->appendChild($root);

        // Запрос всех агентств
        $query = $this->pdo->query("SELECT * FROM agency");
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            // Создание элемента для каждого агентства
            $agency = $dom->createElement('agency');

            $id = $dom->createElement('id', htmlspecialchars((string)$row['id'], ENT_XML1, 'UTF-8'));
            $name = $dom->createElement('name', htmlspecialchars($row['name'], ENT_XML1, 'UTF-8'));

            $agency->appendChild($id);
            $agency->appendChild($name);
            $root->appendChild($agency);
        }

        // Возвращение XML строки
        return $dom->saveXML();
    }

    /**
     * Генерация XML ленты контактов.
     *
     * @param int|null $agencyId Идентификатор агентства для фильтрации (по умолчанию null, чтобы вернуть все контакты).
     *
     * @return string XML строка с данными контактов.
     */
    public function generateContactsFeed(?int $agencyId = null): string
    {
        // Запрос контактов с возможностью фильтрации по агентству
        $query = "
            SELECT c.*, a.name AS agency_name
            FROM contacts c
            LEFT JOIN manager m ON c.id = m.id
            LEFT JOIN agency a ON m.agency_id = a.id
            WHERE (:agencyId IS NULL OR a.id = :agencyId)
        ";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':agencyId' => $agencyId]);
        $contacts = $stmt->fetchAll();

        // Создание XML документа
        $xml = new \SimpleXMLElement('<contacts/>');
        foreach ($contacts as $contact) {
            $contactNode = $xml->addChild('contact');
            $contactNode->addChild('id', (string)$contact['id']);
            $contactNode->addChild('name', $contact['name']);
            $contactNode->addChild('phones', $contact['phones']);
            if ($agencyId) {
                $contactNode->addChild('agency', $contact['agency_name']);
            }
        }

        // Возвращение XML строки
        return $xml->asXML();
    }

    /**
     * Генерация XML ленты менеджеров.
     *
     * @param int|null $agencyId Идентификатор агентства для фильтрации (по умолчанию null, чтобы вернуть всех менеджеров).
     *
     * @return string XML строка с данными менеджеров.
     */
    public function generateManagersFeed(?int $agencyId = null): string
    {
        // Запрос менеджеров с возможностью фильтрации по агентству
        $query = "
        SELECT m.*, a.name AS agency_name
        FROM manager m
        JOIN agency a ON m.agency_id = a.id
        WHERE (:agencyId IS NULL OR m.agency_id = :agencyId)
    ";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':agencyId' => $agencyId]);
        $managers = $stmt->fetchAll();

        // Создание XML документа
        $xml = new \SimpleXMLElement('<managers/>');
        foreach ($managers as $manager) {
            $managerNode = $xml->addChild('manager');
            $managerNode->addChild('id', (string)$manager['id']);
            $managerNode->addChild('name', $manager['name']);
            $managerNode->addChild('agency', $manager['agency_name']);
        }

        // Возвращение XML строки
        return $xml->asXML();
    }

    /**
     * Генерация XML ленты объектов недвижимости.
     *
     * @param array $filters Массив фильтров для выборки объектов недвижимости.
     *
     * @return string XML строка с данными объектов недвижимости.
     */
    public function generateEstatesFeed(array $filters): string
    {
        // Запрос объектов недвижимости с возможностью фильтрации по агентству, контакту и менеджеру
        $query = "
        SELECT e.*, c.name AS contact_name, m.name AS manager_name, a.name AS agency_name
        FROM estate e
        JOIN contacts c ON e.contact_id = c.id
        JOIN manager m ON e.manager_id = m.id
        JOIN agency a ON m.agency_id = a.id
        WHERE (:agency_id IS NULL OR a.id = :agency_id)
          AND (:contact_id IS NULL OR c.id = :contact_id)
          AND (:manager_id IS NULL OR m.id = :manager_id)
    ";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            ':agency_id' => $filters['agency_id'],
            ':contact_id' => $filters['contact_id'],
            ':manager_id' => $filters['manager_id'],
        ]);
        $estates = $stmt->fetchAll();

        // Создание XML документа
        $xml = new \SimpleXMLElement('<estates/>');
        foreach ($estates as $estate) {
            $estateNode = $xml->addChild('estate');
            $estateNode->addChild('id', (string)$estate['id']);
            $estateNode->addChild('address', $estate['address']);
            $estateNode->addChild('price', (string)$estate['price']);
            $estateNode->addChild('rooms', (string)$estate['rooms']);
            $estateNode->addChild('floor', (string)$estate['floor']);
            $estateNode->addChild('house_floors', (string)$estate['house_floors']);
            $estateNode->addChild('description', $estate['description']);
            $estateNode->addChild('contact', $estate['contact_name']);
            $estateNode->addChild('manager', $estate['manager_name']);
            $estateNode->addChild('agency', $estate['agency_name']);
        }

        // Возвращение XML строки
        return $xml->asXML();
    }
}
