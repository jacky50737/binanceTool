<?php
/**
 * 開發者 User
 * 創建於 2022/7/5
 * 使用   PhpStorm
 * 專案名稱binanceTool
 */

require_once __DIR__ . '/vendor/autoload.php';

header('Content-Type: application/json');

if (isset($_GET["PASSWORD"]) and $_GET["PASSWORD"] =="幣安小工具GCP") {
    $db = DataBaseTool::getInstance();
    if($db->checkUser($_GET['LINE_ID'])){
        if ($db->inputUser($_GET['API_KEY'], $_GET['API_SECRET'], $_GET['LINE_ID'])){
            $data = [
                'status' => '200',
                'msg' => '使用者新增成功!',
            ];
        }else{
            $data = [
                'status' => '400',
                'msg' => '使用者新增失敗!',
            ];
        }
    }else{
        $data = [
            'status' => '400',
            'msg' => '使用者已存在',
        ];
    }
} else {
    $data = [
        'status' => '400',
        'msg' => '密碼錯誤',
    ];
}
echo json_encode($data);