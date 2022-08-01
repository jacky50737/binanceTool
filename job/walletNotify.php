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
        $nickName = $db->getNickName($key);
        $data = $binanceTool->getAccountInfo();
        $msg = "\n帳戶名稱：".$nickName."\n" .
            "帳戶資產(USDT)：".number_format($data['totalInitialMargin'],2)."\n" .
            "錢包餘額(USDT)：".number_format($data['totalWalletBalance'],2)."\n" .
            "可用金額(USDT)：".number_format($data['availableBalance'],2)."\n" .
            "當前浮虧(USDT)：".number_format($data['totalUnrealizedProfit'],2)."\n" .
//            "累計盈虧(USDT)：".number_format()."\n" .
            "當前保證金率：".number_format($data['totalMaintMargin']/$data['totalMarginBalance']*100,2)."%";
        $lineTool->sendToAdmin($msg);
    }

} catch (Exception $exception) {
    $lineTool->sendToAdmin($exception->getMessage());
}