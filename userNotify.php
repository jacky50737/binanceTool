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
    $lineTool = LineNotify::getInstance();
//    var_dump($listData);
    if (!empty($_GET['SEND_UID'])){
        $listData = explode(',',$_GET['SEND_UID']);
        $checkList = $db->checkUserAccusesToken($listData);
    }else{
        $checkList = $db->checkUserAccusesToken();
    }

//    var_dump($checkList);
    $msg = $_GET['SEND_MSG']?:'測試訊息';
    foreach ($checkList as $row) {
//        var_dump($row);
        if($row == "wDfvd2b5qfdpX5izFBVRYC0DBD8KmaoN0OX2uQEV7xU"){
            continue;
        }
        $lineTool->setToken($row);
        $lineTool->doLineNotify($msg);
    }
    $countL = count($checkList);
    $data = [
        'status' => '200',
        'msg' => "群發{$countL}完成!",
    ];
} else {
    $data = [
        'status' => '400',
        'msg' => '密碼錯誤',
    ];
}
echo json_encode($data);