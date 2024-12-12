<?php
namespace Utils;

/**
 * Класс для логирования сообщений в файл.
 *
 * @package Utils
 */
class Logger
{
    /**
     * @var string Путь к файлу логов.
     */
    private string $logFile;

    /**
     * Logger constructor.
     * Конструктор инициализирует путь к файлу логов и создает необходимые каталоги, если они не существуют.
     *
     * @param string $logFile Путь к файлу логов (по умолчанию 'app.log').
     */
    public function __construct(string $logFile = __DIR__ . '/../../logs/app.log')
    {
        $this->logFile = $logFile;

        // Проверка и создание каталога для файла логов
        if (!file_exists(dirname($this->logFile))) {
            mkdir(dirname($this->logFile), 0777, true);
        }
    }

    /**
     * Запись сообщения в лог файл с текущим временем.
     *
     * @param string $message Сообщение для записи в лог.
     */
    public function log(string $message): void
    {
        // Текущее время в формате Y-m-d H:i:s
        $time = date('Y-m-d H:i:s');

        // Запись сообщения в файл логов, добавление новой строки
        file_put_contents($this->logFile, "[$time] $message" . PHP_EOL, FILE_APPEND);
    }
}
