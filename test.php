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
    //取得過期列表
    $expirList = $db->getApiLimitExpirList();
    var_dump($expirList);
    foreach($expirList as $row){
        //取得串接帳號列表(倒序)
        $userAccountList = $db->checkUserAccusesTokenLlist($row[0],'DESC');
        $countNeedDelete = count($userAccountList) - 2;
        for($i=0;$i<$countNeedDelete;$i++){
            $db->deleteUser($userAccountList[$i], $row[0]);
        }
        $db->updateUserApiLimit($row[0], 2, date('Y-m-d H:i:s', strtotime('now')));
    }
    $data = [
        'status' => '200',
    ];
} else {
    $data = [
        'status' => '400',
        'msg' => 'Password Error!!',
    ];
}
echo json_encode($data);
