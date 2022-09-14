<?php
/**
 * 開發者 User
 * 創建於 2022/7/5
 * 使用   PhpStorm
 * 專案名稱binanceTool
 */

require_once 'class/autoload.php';

header('Content-Type: application/json');

if (isset($_GET["PASSWORD"]) and $_GET["PASSWORD"] =="幣安小工具GCP") {
    if (isset($_GET["LINE_ID"]) and !empty($_GET["LINE_ID"])) {
        $db = DataBaseTool::getInstance();
        $result = $db->getUserWalletStatus($_GET["LINE_ID"]);
        var_dump($result);
        $data = [
            'status' => '200',
            // 'msg' => $result,
        ];
    }
} else {
    $data = [
        'status' => '400',
        'msg' => '密碼錯誤',
    ];
}
echo json_encode($data);