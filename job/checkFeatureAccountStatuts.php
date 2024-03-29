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
        // var_dump($checkList);
        $countList = count($checkList);
        echo "對帳號數：{$countList}進行檢查!\n";
        foreach ($checkList as $row) {
            $key = $row;
            $secret = $db->getApiSecret($key)[0];
            $binanceTool->setApiKey($key);
            $binanceTool->setApiSecret($secret);
    //         echo "將對帳號：$key 執行檢查....\n";
            if(!$binanceTool->checkBinanceApi()){
                echo "發現APIKEY：$key 失效! 將進行關閉功能.....\n";
                $lineTool->sendToAdmin("\n發現APIKEY：$key 失效! 將進行關閉功能.....\n");
                $is_Successes = $db->updateUserFeature($key,"關閉全功能",'EXPIRED')?"成功":"失敗";
                echo "關閉{$is_Successes}!\n";
                $lineTool->sendToAdmin("\n關閉{$is_Successes}!\n");
            }

        }
        echo "帳號有效期限檢查完畢!\n";

    //取得過期列表
    $expirList = $db->getApiLimitExpirList();
    // var_dump($expirList);
    $countList = count($expirList);
    echo "對過期列表數：{$countList}進行檢查!\n";
    foreach($expirList as $row){
        //取得串接帳號列表(倒序)
        $userAccountList = $db->checkUserAccusesTokenLlist($row[0],'DESC');
        $countNeedDelete = count($userAccountList) - 2;
        if($countNeedDelete > 0){
            $msg = "發現帳號 UID：$row[0] 超出最大可用串接數! 將進行關閉功能.....\n";
            echo $msg;
            $lineTool->sendToAdmin("\n{$msg}");
            for($i=0;$i<$countNeedDelete;$i++){
                $db->deleteUser($userAccountList[$i], $row[0]);
            }
            $msg ="關閉成功!\n";
            echo $msg;
            $lineTool->sendToAdmin("\n$msg");
        }
        $db->updateUserApiLimit($row[0], 2, date('Y-m-d H:i:s', strtotime('now')));
        $msg ="已重設UID：{$row[0]}為最大數量2!\n";
        echo $msg;
        $lineTool->sendToAdmin("\n$msg");
    }
    echo "帳號串接數過期列表檢查完畢!\n";
    echo "job結束\n";
} catch (Exception $exception) {
    echo '發生錯誤：' . $exception->getMessage();
    $lineTool->sendToAdmin($exception->getMessage());
}
