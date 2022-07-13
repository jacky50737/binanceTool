<?php
/**
 * 開發者 User
 * 創建於 2022/7/5
 * 使用   PhpStorm
 * 專案名稱binanceTool
 */

declare(strict_types=1);

require_once __DIR__ . '/class/autoload.php';

header('Content-Type: application/json; charset=utf-8');

if (isset($_GET["PASSWORD"]) and $_GET["PASSWORD"] == "幣安小工具GCP") {
    $db = DataBaseTool::getInstance();
    if ($db->checkUserFeature($_GET['API_KEY'], $_GET['FEATURE_NAME'])) {
        if ($db->updateUserFeature($_GET['API_KEY'], $_GET['FEATURE_NAME'], $_GET['STATUS'], $_GET['EXPIRED_DAY'])) {
            $data = [
                'status' => '200',
                'msg' => '資料更新完成',
            ];
        } else {
            $data = [
                'status' => '400',
                'msg' => '尚未順利更新',
            ];
        }
    } else {
        if ($db->insertUserFeature($_GET['API_KEY'], $_GET['FEATURE_NAME'], $_GET['STATUS'], $_GET['EXPIRED_DAY'])) {
            $data = [
                'status' => '201',
                'msg' => '資料新增完成',
            ];
        } else {
            $data = [
                'status' => '400',
                'msg' => '尚未順利新增',
            ];
        }
    }
} else {
    $data = [
        'status' => '400',
        'msg' => '密碼錯誤',
    ];
}
echo json_encode($data);
