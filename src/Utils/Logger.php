<?php
namespace Utils;

class Logger
{
    private string $logFile;

    public function __construct(string $logFile = __DIR__ . '/../../logs/app.log')
    {
        $this->logFile = $logFile;
        if (!file_exists(dirname($this->logFile))) {
            mkdir(dirname($this->logFile), 0777, true);
        }
    }

    public function log(string $message): void
    {
        $time = date('Y-m-d H:i:s');
        file_put_contents($this->logFile, "[$time] $message" . PHP_EOL, FILE_APPEND);
    }
}
