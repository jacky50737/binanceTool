<?php
/**
 * 開發者 User
 * 創建於 2022/7/7
 * 使用   PhpStorm
 * 專案名稱binanceTool
 */

require_once '../class/autoload.php';

$db = DataBaseTool::getInstance();
$lineTool = LineNotify::getInstance();
$binanceTool = BinanceTool::getInstance();

try {
    $checkList = $db->checkUserFeatureStatus('AUTO_WALLET_NOTIFY');
    foreach ($checkList as $row){
        $key = $row;
        $secret = $db->getApiSecret($key)[0];
        $binanceTool->setApiKey($key);
        $binanceTool->setApiSecret($secret);
        $data = $binanceTool->getAccountInfo();
        var_dump($data);
    }

} catch (Exception $exception) {
    $lineTool->sendToAdmin($exception->getMessage());
}