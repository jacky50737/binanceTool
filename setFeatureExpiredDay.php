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
    $apiKey = $db->checkUserAccusesTokenLlist($_GET['LINE_ID']);
    if(!empty($_GET['EXPIRED_DAY']) and !empty($apiKey)){
        if($db->updateUserFeatureExpiredDay($apiKey,$_GET['EXPIRED_DAY'])){
            $data = [
                'status' => '201',
                'msg' => '成功更新全功能過期時間為：'.$_GET['EXPIRED_DAY'],
            ];
        }else{
            $data = [
                'status' => '400',
                'msg' => '更新失敗，請注意時間是否合法!',
            ];
        }
    }else{
        $data = [
            'status' => '200',
            'msg' => '設定成功，尚未有已串接功能需更新!',
        ];
    }
} else {
    $data = [
        'status' => '400',
        'msg' => '密碼錯誤',
    ];
}
echo json_encode($data);
