<?php

namespace Services;

use Database\Connection;
use DOMDocument;
use PDO;

class FeedGenerator
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connection::getInstance();
    }

    public function generateAgenciesFeed(): string
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $root = $dom->createElement('agencies');
        $dom->appendChild($root);

        $query = $this->pdo->query("SELECT * FROM agency");
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $agency = $dom->createElement('agency');

            $id = $dom->createElement('id', htmlspecialchars((string)$row['id'], ENT_XML1, 'UTF-8'));
            $name = $dom->createElement('name', htmlspecialchars($row['name'], ENT_XML1, 'UTF-8'));

            $agency->appendChild($id);
            $agency->appendChild($name);
            $root->appendChild($agency);
        }

        return $dom->saveXML();
    }


    public function generateContactsFeed(?int $agencyId = null): string
    {
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

        return $xml->asXML();
    }

    public function generateManagersFeed(?int $agencyId = null): string
    {
        $query = "
        SELECT m.*, a.name AS agency_name
        FROM manager m
        JOIN agency a ON m.agency_id = a.id
        WHERE (:agencyId IS NULL OR m.agency_id = :agencyId)
    ";

        $stmt = $this->pdo->getConnection()->prepare($query);
        $stmt->execute([':agencyId' => $agencyId]);
        $managers = $stmt->fetchAll();

        $xml = new \SimpleXMLElement('<managers/>');

        foreach ($managers as $manager) {
            $managerNode = $xml->addChild('manager');
            $managerNode->addChild('id', (string)$manager['id']);
            $managerNode->addChild('name', $manager['name']);
            $managerNode->addChild('agency', $manager['agency_name']);
        }

        return $xml->asXML();
    }

    public function generateEstatesFeed(array $filters): string
    {
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

        $stmt = $this->pdo->getConnection()->prepare($query);
        $stmt->execute([
            ':agency_id' => $filters['agency_id'],
            ':contact_id' => $filters['contact_id'],
            ':manager_id' => $filters['manager_id'],
        ]);
        $estates = $stmt->fetchAll();

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

        return $xml->asXML();
    }


}
