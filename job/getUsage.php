<?php
require_once '../class/autoload.php';

$lineTool = LineNotify::getInstance();
$helpTool = Help::getInstance();
$log = LogFileTool::getInstance();
$db = DataBaseTool::getInstance();
$logUUID = $helpTool->getUid();
$log->setUid($logUUID);
echo "job開始!\n";
try {
    $cpu = $helpTool->get_server_cpu_usage();
    $ram = $helpTool->get_server_memory_usage();
    $autoOrderNotifyCount = $db->checkFeatureCount('AUTO_ORDER_NOTIFY');
    $autoWalletNotify = $db->checkFeatureCount('AUTO_WALLET_NOTIFY');
    $userCount = $db->checkUserCount();
    $msg = "\n----目前主機狀況----";
    $msg .="\nCPU：".$cpu;
    $msg .="\nRAM：".$ram;
    $msg .="\n總使用者數：".$userCount;
    $msg .="\n----目前通知串接狀況----";
    $msg .="\n開平倉帳戶數：".$autoOrderNotifyCount;
    $msg .="\n定時資產帳戶數：".$autoOrderNotifyCount;
    $lineTool->sendToAdmin($msg);
    echo "job結束\n";
} catch (Exception $exception) {
    echo '發生錯誤：' . $exception->getMessage();
    $lineTool->sendToAdmin($exception->getMessage());
}