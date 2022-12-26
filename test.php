<?php
/**
 * 開發者 User
 * 創建於 2022/7/12
 * 使用   PhpStorm
 * 專案名稱binanceTool
 */
declare(strict_types=1);

require_once 'class/autoload.php';

header('Content-Type: application/json; charset=utf-8');

if (isset($_GET["PASSWORD"]) and $_GET["PASSWORD"] == "GCP") {
    $db = DataBaseTool::getInstance();
    $keyLimit = $db->checkApiKeyCountLimit($_GET['LINE_ID']);
    $keyCount = $db->checkApiKeyCount($_GET['LINE_ID']);
    $data = [
        'status' => '200',
        'msg' => "keyLimit={$keyLimit}, keyCount={$keyCount}",
    ];
} else {
    $data = [
        'status' => '400',
        'msg' => 'Password Error!!',
    ];
}
echo json_encode($data);
