<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Services\ExcelImporter;

// Импорт данных
$importer = new ExcelImporter();
$importer->import(__DIR__ . '/../data/estate.xlsx');

echo "Импорт завершен!";