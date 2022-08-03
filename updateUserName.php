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
    $checkTag=true;
    foreach ($_GET as &$item){
        $item = htmlentities($item);
        if(empty($item)){
            $checkTag = false;
        }
    }
    if($checkTag){
        $db = DataBaseTool::getInstance();
        if($db->updateUserName($_GET['NICK_NAME'],$_GET['API_KEY'])){
            $data = [
                'status' => '200',
                'msg' => '名稱修改成功為：'.$_GET['NICK_NAME'],
            ];
        }else{
            $data = [
                'status' => '400',
                'msg' => '修改失敗，請洽管理員!',
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