<?php
/**
 * 開發者 User
 * 創建於 2022/7/4
 * 使用   PhpStorm
 * 專案名稱binanceTool
 */

declare(strict_types=1);

require_once 'class/autoload.php';

header('Content-Type: application/json');

if (isset($_GET["PASSWORD"]) and $_GET["PASSWORD"] =="幣安小工具GCP") {
    $help = Help::getInstance();
    $db = DataBaseTool::getInstance();
    $arrLog = $db->getTreadLogByOrderId($_GET['ORDER_ID'],['NEW','PARTIALLY_FILLED','FILLED']);
    var_dump(77777);
    var_dump($arrLog);
    foreach ($arrLog as &$log){
        $log = $help->reArrayFromKey($log);
    }
    var_dump($arrLog);
    if($arrLog){
        $data = [
            'status' => '200',
            'msg' => '有資料',
        ];
    }else{
        $data = [
            'status' => '200',
            'msg' => '沒資料',
        ];
    }
//    $arrLog = $db->getTreadLog($_GET['API_KEY']);

} else {
    $data = [
        'status' => '400',
        'msg' => '密碼錯誤',
    ];
}
echo json_encode($data);