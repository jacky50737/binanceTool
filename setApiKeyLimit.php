<?php
/**
 * 開發者 User
 * 創建於 2022/7/5
 * 使用   PhpStorm
 * 專案名稱binanceTool
 */

declare(strict_types=1);

require_once 'class/autoload.php';

header('Content-Type: application/json; charset=utf-8');

if (isset($_GET["PASSWORD"]) and $_GET["PASSWORD"] == "幣安小工具GCP") {
    $db = DataBaseTool::getInstance();
    $originLimit = $db->checkApiKeyCountLimit($_GET['LINE_ID']);
    if($originLimit >= 2){
        if($db->updateUserApiLimit($_GET['LINE_ID'], (int)$_GET['API_LIMIT'], $_GET['EXPIRED_DAY'])){
            $newLimit = $db->checkApiKeyCountLimit($_GET['LINE_ID']);
            $data = [
                'status' => '201',
                'msg' => "成功更新Api串接數量：{$originLimit}->{$newLimit}\n到期時間為：".$time,
            ];
        }else{
            $data = [
                'status' => '400',
                'msg' => '更新失敗，請注意時間是否合法!',
            ];
        }
    }else{
        $data = [
            'status' => '500',
            'msg' => '發生未知的錯誤!',
        ];
    }
} else {
    $data = [
        'status' => '400',
        'msg' => '密碼錯誤',
    ];
}
echo json_encode($data);
