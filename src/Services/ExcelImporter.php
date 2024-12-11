<?php

namespace Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
use Models\Agency;
use Models\Manager;
use Models\Contact;
use Models\Estate;
use Utils\Logger;

class ExcelImporter
{
    private Agency $agencyModel;
    private Manager $managerModel;
    private Contact $contactModel;
    private Estate $estateModel;
    private Logger $logger;

    public function __construct()
    {
        $this->agencyModel = new Agency();
        $this->managerModel = new Manager();
        $this->contactModel = new Contact();
        $this->estateModel = new Estate();
        $this->logger = new Logger();
    }

    public function import(string $filePath): void
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray(null, true, true, true);

        $this->logger->log("Начало импорта файла: $filePath");

        // Убираем первую строку
        $headers = array_shift($data);

        foreach ($data as $row) {
            $this->logger->log("Строка: " . json_encode($row));
            $agencyId = $this->agencyModel->findOrCreate($row['B']); // Агенство Недвижимости
            $managerId = $this->managerModel->findOrCreate($row['C'], $agencyId); // Менеджер
            $contactId = $this->contactModel->findOrCreate($row['D'], $row['E']); // Продавец + Телефоны

            // Подготовка данных для обновления/вставки
            $estateData = [
                'address' => $row['H'],
                'price' => (float) str_replace(' ', '', $row['F']),
                'rooms' => $row['K'],
                'floor' => $row['I'],
                'house_floors' => $row['J'],
                'description' => $row['G'],
                'contact_id' => $contactId,
                'manager_id' => $managerId,
            ];

            $estateId = $row['A'] ?? null; // ID объекта из Excel (если есть)
            $this->estateModel->updateOrCreate($estateData, $estateId);
        }

        $this->logger->log("Импорт файла завершен: $filePath");
    }
}
