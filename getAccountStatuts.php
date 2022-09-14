<?php
/**
 * 開發者 User
 * 創建於 2022/7/5
 * 使用   PhpStorm
 * 專案名稱binanceTool
 */

require_once 'class/autoload.php';

header('Content-Type: application/json');

$db = DataBaseTool::getInstance();
$lineTool = LineNotify::getInstance();
$binanceTool = BinanceTool::getInstance();

if (isset($_GET["PASSWORD"]) and $_GET["PASSWORD"] =="幣安小工具GCP") {
    if (isset($_GET["LINE_ID"]) and !empty($_GET["LINE_ID"])) {
        $result = $db->getUserWalletStatus($_GET["LINE_ID"]);
        foreach ($result as $row) {
            $key = $row['key'];
            $secret = $db->getApiSecret($key)[0];
            $binanceTool->setApiKey($key);
            $binanceTool->setApiSecret($secret);
            $nickName = $db->getNickName($key);
            $lineToken = $db->getLineToken($key);
            $data = $binanceTool->getAccountInfo();
            $capital = $db->getUserCapital($key)[0];
            if ($data['totalMarginBalance'] != 0) {
                $msg = "\n帳戶名稱：" . $nickName . "\n" .
                    "帳戶資產(USDT)：" . number_format($data['totalMarginBalance'], 2) . "\n" .
                    "錢包餘額(USDT)：" . number_format($data['totalWalletBalance'], 2) . "\n" .
                    "可用金額(USDT)：" . number_format($data['availableBalance'], 2) . "\n" .
                    "當前浮虧(USDT)：" . number_format($data['totalUnrealizedProfit'], 2) . "\n" .
                    "當前保證金率：" . number_format($data['totalMaintMargin'] / $data['totalMarginBalance'] * 100, 2) . "%";
                if ($capital > 0) {
                    $msg .= "\n" . "當前獲利率：" . number_format((($data['totalMarginBalance']-$capital) / $capital)*100, 2) . "%";
                }
    
            } else {
                $msg = "\n帳戶名稱：" . $nickName . "\n" .
                    "您的錢包暫無任何資產!";
            }
            $lineTool->setToken($lineToken);
            $lineTool->doLineNotify($msg);
        }
        $data = [
            'status' => '200',
            'msg' => '已發送通知到Line Notify!!',
        ];
    }
} else {
    $data = [
        'status' => '400',
        'msg' => '密碼錯誤',
    ];
}
echo json_encode($data);