<?php
/**
 * 開發者 User
 * 創建於 2022/7/7
 * 使用   PhpStorm
 * 專案名稱binanceTool
 */

require_once '../class/autoload.php';
echo 0000;
$db = DataBaseTool::getInstance();
echo 1111;
$lineTool = LineNotify::getInstance();
echo 2222;
$binanceTool = BinanceTool::getInstance();
echo 3333;
try {
    $checkList = $db->checkUserFeatureStatus('AUTO_WALLET_NOTIFY');
    var_dump($checkList);
    foreach ($checkList as $row){
        var_dump($row);
    }

} catch (Exception $exception) {
    $lineTool->sendToAdmin($exception->getMessage());
}