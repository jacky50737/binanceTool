<?php
require_once '../class/autoload.php';

$lineTool = LineNotify::getInstance();
$helpTool = Help::getInstance();
$log = LogFileTool::getInstance();
$logUUID = $helpTool->getUid();
$log->setUid($logUUID);
echo "job開始!\n";
try {
    $cpu = $helpTool->get_server_cpu_usage();
    $ram = $helpTool->get_server_memory_usage();
    $msg = "\n----目前主機狀況----\nCPU：".$cpu."\nRAM：".$ram;
    $lineTool->sendToAdmin($msg);
    echo "job結束\n";
} catch (Exception $exception) {
    echo '發生錯誤：' . $exception->getMessage();
    $lineTool->sendToAdmin($exception->getMessage());
}