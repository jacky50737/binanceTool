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
    if(in_array($_GET['STATUS'],['ENABLE','DISABLE', '開', '關'])){
        $status_chinese = "無狀態";
        switch ($_GET['STATUS']){
            case 'ENABLE':
                $status_chinese = "開";
                break;
            case 'DISABLE':
                $status_chinese = "關";
                break;
            case '開':
                $_GET['STATUS'] = 'ENABLE';
                $status_chinese = "開";
                break;
            case '關':
                $_GET['STATUS'] = 'DISABLE';
                $status_chinese = "關";
                break;
        }

        $db = DataBaseTool::getInstance();
        if ($db->checkUserFeature($_GET['API_KEY'], $_GET['FEATURE_NAME'])) {
            if ($db->updateUserFeature($_GET['API_KEY'], $_GET['FEATURE_NAME'], $_GET['STATUS'], $_GET['EXPIRED_DAY'])) {
                $data = [
                    'status' => '200',
                    'msg' => '資料更新完成，目前狀態為：'.$status_chinese,
                ];
            } else {
                $data = [
                    'status' => '400',
                    'msg' => '尚未順利更新，請確認是否有權限，或洽詢管理員。',
                ];
            }
        } else {
            if ($db->insertUserFeature($_GET['API_KEY'], $_GET['FEATURE_NAME'], $_GET['STATUS'], $_GET['EXPIRED_DAY'])) {
                $data = [
                    'status' => '201',
                    'msg' => '資料新增完成，目前狀態為：'.$status_chinese,
                ];
            } else {
                $data = [
                    'status' => '400',
                    'msg' => '尚未順利建立，請確認是否有權限，或洽詢管理員。',
                ];
            }
        }
    }else{
        $data = [
            'status' => '400',
            'msg' => '參數錯誤',
        ];
    }
} else {
    $data = [
        'status' => '400',
        'msg' => '密碼錯誤',
    ];
}
echo json_encode($data);
