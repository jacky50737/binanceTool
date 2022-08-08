<?php

class LogFileTool
{
    private string $logFilePath = "/home/cryptoharvester/public_html/binanceToolApi/log/logFile.log";

    private static $instance;

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function writeLog($msg): void
    {
        $file = fopen($this->logFilePath, "a+");
        fwrite($file, $msg . "\n");
        fclose($file);
    }
}