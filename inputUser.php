<?php
/**
 * 開發者 User
 * 創建於 2022/7/5
 * 使用   PhpStorm
 * 專案名稱binanceTool
 */

require_once 'class/autoload.php';

header('Content-Type: application/json');

if (isset($_GET["PASSWORD"]) and $_GET["PASSWORD"] == "幣安小工具GCP") {
    $db = DataBaseTool::getInstance();
    $binance = BinanceTool::getInstance();
    if (!$db->checkUserNickName($_GET['LINE_ID'], $_GET['NICK_NAME'])) {
        if(!$db->checkApiKey($_GET['API_KEY'])){
            $binance->setApiKey($_GET['API_KEY']);
            $binance->setApiSecret($_GET['API_SECRET']);
            if ($binance->checkBinanceApi() and $binance->checkKeySecretLen()) {
                if ($db->inputUser($_GET['NICK_NAME'], $_GET['API_KEY'], $_GET['API_SECRET'], $_GET['LINE_ID'], $_GET['ACCESS_TOKEN'])) {
                    $data = [
                        'status' => '200',
                        'msg' => '使用者新增成功!',
                    ];
                } else {
                    $data = [
                        'status' => '400',
                        'msg' => '使用者新增失敗!',
                    ];
                }
            } else {
                $data = [
                    'status' => '400',
                    'msg' => '無效的API!',
                ];
            }
        }else{
            $data = [
                'status' => '400',
                'msg' => 'API KEY已存在',
            ];
        }
    } else {
        $data = [
            'status' => '400',
            'msg' => '暱稱已存在',
        ];
    }
} else {
    $data = [
        'status' => '400',
        'msg' => '密碼錯誤',
    ];
}
echo json_encode($data);
