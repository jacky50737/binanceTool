<?php

class LogFileTool
{
    private string $logFilePath = "/home/cryptoharvester/public_html/binanceToolApi/log/logFile.log";

    private static $instance;
    private string $uid;

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function setUid(string $uid){
        $this->uid = $uid;
    }

    public function writeLog($msg): void
    {
//        $timeNow = date("Y-m-d h:i:sa");
        $timeNow = date("Y-m-d h:i:s");
        $file = fopen($this->logFilePath, "a+");
        fwrite($file, "\n----------------{$timeNow}[{$this->uid}]-------------------\n".$msg."\n----------------End-------------------\n" );
        fclose($file);
    }
}