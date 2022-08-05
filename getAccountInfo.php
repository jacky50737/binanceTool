<?php
/**
 * 開發者 User
 * 創建於 2022/7/3
 * 使用   PhpStorm
 * 專案名稱binanceTool
 */

declare(strict_types=1);

require_once 'class/autoload.php';

header('Content-Type: application/json; charset=utf-8');
$db = DataBaseTool::getInstance();
$lineTool = LineNotify::getInstance();
$binanceTool = BinanceTool::getInstance();

$lineTool->sendToAdmin(__FILE__."\n輸入：\n".print_r($_GET));
$data = [
    'status' => '400',
    'msg' => '初始化',
];

if (isset($_GET["API_KEY"])) {
    $key = $_GET["API_KEY"];
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
    }else{
        $msg = "\n帳戶名稱：" . $nickName . "\n" .
            "您的錢包暫無任何資產!";
    }
    $lineTool->setToken($lineToken);
    $lineTool->doLineNotify($msg);

}else {
    $data = [
        'status' => '200',
        'msg' => '參數錯誤',
    ];
}
$lineTool->sendToAdmin(__FILE__."\n輸出：\n".print_r($data));
echo json_encode($data);
exit(0);