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
    $checkTag = true;
    foreach ($_GET as $key => &$item) {
        $item = htmlentities($item);
        if (empty($item)) {
            $checkTag = false;
        }

        if ($_GET[$key] == 0) {
            $checkTag = true;
        }
    }
    if($checkTag){
        $db = DataBaseTool::getInstance();
        if(is_numeric($_GET['CAPITAL'])){
            if($db->updateUserCapital($_GET['API_KEY'],$_GET['CAPITAL'])){
                $data = [
                    'status' => '200',
                    'msg' => '本金設定成功為：'.$_GET['CAPITAL'],
                ];
            }else{
                $data = [
                    'status' => '400',
                    'msg' => '設定失敗，請洽管理員!',
                ];
            }
        } else {
            $data = [
                'status' => '400',
                'msg' => '輸入的本金不是數字',
            ];
        }
    }else{
        $data = [
            'status' => '400',
            'msg' => '參數不正確',
        ];
    }


} else {
    $data = [
        'status' => '400',
        'msg' => '密碼錯誤',
    ];
}
echo json_encode($data);