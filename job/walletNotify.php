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
echo "job開始!\n";
try {
    $checkList = $db->checkUserFeatureStatus('AUTO_WALLET_NOTIFY');
    var_dump($checkList);
    foreach ($checkList as $row) {
        $key = $row;
        $secret = $db->getApiSecret($key)[0];
        $binanceTool->setApiKey($key);
        $binanceTool->setApiSecret($secret);
        $nickName = $db->getNickName($key);
        $lineToken = $db->getLineToken($key);
        $data = $binanceTool->getAccountInfo();
        if($data['totalMarginBalance'] != 0){
            $msg = "\n帳戶名稱：" . $nickName . "\n" .
                "帳戶資產(USDT)：" . number_format($data['totalMarginBalance'], 2) . "\n" .
                "錢包餘額(USDT)：" . number_format($data['totalWalletBalance'], 2) . "\n" .
                "可用金額(USDT)：" . number_format($data['availableBalance'], 2) . "\n" .
                "當前浮虧(USDT)：" . number_format($data['totalUnrealizedProfit'], 2) . "\n" .
                "當前保證金率：" . number_format($data['totalMaintMargin'] / $data['totalMarginBalance'] * 100, 2) . "%";
            $lineTool->setToken($lineToken);
            $lineTool->doLineNotify($msg);
            echo '已發送通知到KEY：'.$key."\n";
        }else{
            echo '錢包尚無資產KEY：'.$key."\n";
        }
    }
    echo "job結束\n";
} catch (Exception $exception) {
    echo '發生錯誤：'.$exception->getMessage();
    $lineTool->sendToAdmin($exception->getMessage());
}