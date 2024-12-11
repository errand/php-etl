<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Services\FeedGenerator;

//header('Content-Type: application/xml; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];

// Создаём экземпляры необходимых классов
$feedGenerator = new FeedGenerator();

// Разбираем путь запроса
$path = parse_url($path);
$pathParts = explode('/', trim($path, '/'));


echo "<note>
<to>$pathParts</to>
<from>Jani</from>
<heading>Reminder</heading>
<body>Don't forget me this weekend!</body>
</note>";

if ($method === 'GET' && $pathParts[0] === 'api') {
    echo '<response><status>success</status></response>';
    // Обработка эндпоинтов
    switch ($pathParts[1]) {
        case 'agencies':
            //echo $feedGenerator->generateAgenciesFeed();
            echo '<response><status>success</status></response>';
            break;

        case 'contacts':
            $agencyId = $_GET['agency_id'] ?? null;
            $agencyId = $agencyId ? (int) $agencyId : null;
            echo $feedGenerator->generateContactsFeed($agencyId);
            break;

        case 'managers':
            $agencyId = $_GET['agency_id'] ?? null;
            $agencyId = $agencyId ? (int) $agencyId : null;
            echo $feedGenerator->generateManagersFeed($agencyId);
            break;

        case 'estates':
            $agencyId = $_GET['agency_id'] ?? null;
            $contactId = $_GET['contact_id'] ?? null;
            $managerId = $_GET['manager_id'] ?? null;

            $filters = [
                'agency_id' => $agencyId ? (int) $agencyId : null,
                'contact_id' => $contactId ? (int) $contactId : null,
                'manager_id' => $managerId ? (int) $managerId : null,
            ];

            echo $feedGenerator->generateEstatesFeed($filters);
            break;

        default:
            http_response_code(404);
            echo "<error>Endpoint not found</error>";
    }
} else {
    http_response_code(405);
    echo "<error>Method not allowed</error>";
}
