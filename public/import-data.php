<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Services\ExcelImporter;

if (php_sapi_name() !== 'cli') {
    echo "Этот скрипт предназначен для запуска в консоли.\n";
    exit(1);
}

// Проверяем аргументы
if ($argc < 2) {
    echo "Использование: php import-data.php <path_to_excel_file>\n";
    exit(1);
}

$filePath = __DIR__ . '/../data/' . $argv[1];

if (!file_exists($filePath)) {
    echo "Файл $filePath не найден.\n";
    exit(1);
}

try {
    $importer = new ExcelImporter();
    $importer->import($filePath);
    echo "Импорт файла $filePath завершен.\n";
} catch (Exception $e) {
    echo "Произошла ошибка: " . $e->getMessage() . "\n";
}
