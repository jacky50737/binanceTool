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
    $checkList = $db->checkUserFeatureStatus();
    var_dump($checkList);
    foreach ($checkList as $row) {
        $key = $row;
        $secret = $db->getApiSecret($key)[0];
        $binanceTool->setApiKey($key);
        $binanceTool->setApiSecret($secret);
        echo "將對帳號：$key 執行檢查....\n";
        if(!$binanceTool->checkBinanceApi()){
            echo "發現APIKEY：$key 失效! 將進行關閉功能.....\n";
            $lineTool->sendToAdmin("\n發現APIKEY：$key 失效! 將進行關閉功能.....\n");
            $is_Successes = $db->updateUserFeature($key,"關閉全功能",'DISABLE')?"成功":"失敗";
            echo "關閉{$is_Successes}!\n";
            $lineTool->sendToAdmin("\n關閉{$is_Successes}!\n");
        }
        echo "檢查完畢!";
    }
    echo "job結束\n";
} catch (Exception $exception) {
    echo '發生錯誤：' . $exception->getMessage();
    $lineTool->sendToAdmin($exception->getMessage());
}