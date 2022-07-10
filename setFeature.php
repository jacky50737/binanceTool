<?php
/**
 * 開發者 User
 * 創建於 2022/7/5
 * 使用   PhpStorm
 * 專案名稱binanceTool
 */

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

header('Content-Type: application/json; charset=utf-8');

if (isset($_GET["PASSWORD"]) and $_GET["PASSWORD"] =="幣安小工具GCP") {
    $db = DataBaseTool::getInstance();
    $arrLog = $db->getTreadLog($_GET['API_KEY']);
    $data = [
        'status' => '200',
        'msg' => '資料',
    ];
} else {
    $data = [
        'status' => '400',
        'msg' => '密碼錯誤',
    ];
}
echo json_encode($data);