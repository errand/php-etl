<?php

namespace Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
use Models\Agency;
use Models\Manager;
use Models\Contact;
use Models\Estate;
use Utils\Logger;

/**
 * Class ExcelImporter
 * Импорт данных из Excel файла и обновление/создание записей в базе данных.
 *
 * @package Services
 */
class ExcelImporter
{
    /**
     * @var Agency
     * Модель для работы с агентствами недвижимости.
     */
    private Agency $agencyModel;

    /**
     * @var Manager
     * Модель для работы с менеджерами.
     */
    private Manager $managerModel;

    /**
     * @var Contact
     * Модель для работы с контактами.
     */
    private Contact $contactModel;

    /**
     * @var Estate
     * Модель для работы с объектами недвижимости.
     */
    private Estate $estateModel;

    /**
     * @var Logger
     * Логгер для записи логов.
     */
    private Logger $logger;

    /**
     * ExcelImporter constructor.
     * Инициализация моделей и логгера.
     */
    public function __construct()
    {
        $this->agencyModel = new Agency();
        $this->managerModel = new Manager();
        $this->contactModel = new Contact();
        $this->estateModel = new Estate();
        $this->logger = new Logger();
    }

    /**
     * Импорт данных из Excel файла в базу данных.
     *
     * @param string $filePath Путь к файлу Excel.
     *
     * @return void
     */
    public function import(string $filePath): void
    {
        // Загрузка и чтение Excel файла
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray(null, true, true, true);

        // Логирование начала импорта
        $this->logger->log("Начало импорта файла: $filePath");

        // Убираем первую строку (заголовки)
        $headers = array_shift($data);

        // Процесс импорта данных по строкам
        foreach ($data as $row) {
            $this->logger->log("Строка: " . json_encode($row));

            // Создание или поиск существующих записей для агентства, менеджера и контакта
            $agencyId = $this->agencyModel->findOrCreate($row['B']); // Агенство Недвижимости
            $managerId = $this->managerModel->findOrCreate($row['C'], $agencyId); // Менеджер
            $contactId = $this->contactModel->findOrCreate($row['D'], $row['E']); // Продавец + Телефоны

            // Подготовка данных для обновления или создания записи недвижимости
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

            // Получаем ID объекта недвижимости из Excel (если он есть)
            $estateId = $row['A'] ?? null;

            // Обновляем или создаем запись в базе данных
            $this->estateModel->updateOrCreate($estateData, $estateId);
        }

        // Логирование завершения импорта
        $this->logger->log("Импорт файла завершен: $filePath");
    }
}
