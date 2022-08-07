<?php
/**
 * 開發者 User
 * 創建於 2022/7/3
 * 使用   PhpStorm
 * 專案名稱binanceTool
 */

declare(strict_types=1);

require_once 'class/autoload.php';

header('Content-Type: application/json; charset=utf-8');
$lineTool = LineNotify::getInstance();
$helpTool = Help::getInstance();
$lineTool->sendToAdmin(__FILE__."\nGET輸入：\n".$helpTool->mixArray($_GET));

$postData = [];
try {
    parse_str(file_get_contents('php://input'), $postData);
    if(isset($postData['data']) and is_string($postData['data'])){
        $postData = json_decode($postData['data']);
        $postMsgData = $helpTool->mixArray((array)$postData);
    }else{
        $postMsgData = "無輸入";
    }

    $lineTool->sendToAdmin(__FILE__."\nPOST輸入：\n".$postMsgData);
}catch (Exception $exception){
    $lineTool->sendToAdmin(__FILE__."\nPOST異常：\n".$exception->getMessage());
}

$data = [
    'status' => '400',
    'msg' => '初始化',
];
if (isset($_GET["API_KEY"]) and $_GET["API_KEY"] =="WK0AaBNAfdukp7RHhFH6M2qJkzH2hyulkypc22O5qY8rpPUEv5yQNeKeFGPgMFrM") {
    $db = DataBaseTool::getInstance();
    $binanceTool = BinanceTool::getInstance();

    try {
        $accessToken = $db->getLineToken($_GET["API_KEY"]);
        $nickName = $db->getNickName($_GET["API_KEY"]);
        $lineTool->setToken($accessToken);
        $notifyArray = $binanceTool->transactionMessageProcessing($postData, $nickName);
        $logStatus = "NEW";
        if($lineTool->doLineNotify($notifyArray['msg']) and $notifyArray['code'] =='200'){
            $logStatus = "SEND";
            $lineTool->sendToAdmin(__FILE__."\n輸出：\n".$notifyArray['msg']);
        }

        $db->upLoadTreadLog($_GET["API_KEY"], $notifyArray['data'],$logStatus);
        if($logStatus == "SEND"){
            $data = [
                'status' => '201',
                'msg' => '完全新增完成',
            ];
        }else{
            $data = [
                'status' => '200',
                'msg' => '新增完成，但未發送成功',
            ];
        }
    } catch (Exception $exception) {
        $data = [
            'status' => '400',
            'msg' => '發生未知的錯誤',
            'error' => $exception->getMessage()
        ];
    }
    $db->closeDB();
}
else {
    $data = [
        'status' => '400',
        'msg' => '參數錯誤',
    ];
}
echo json_encode($data);
exit(0);
