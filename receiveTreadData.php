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
$log = LogFileTool::getInstance();
//$lineTool->sendToAdmin(__FILE__."\nGET輸入：\n".$helpTool->mixArray($_GET));

$postData = [];
try {
    parse_str(file_get_contents('php://input'), $postData);
    if(isset($postData['data']) and is_string($postData['data'])){
        $postData = json_decode($postData['data']);
        $postMsgData = $helpTool->mixArray((array)$postData);
    }else{
        $postMsgData = "無輸入";
    }
    if ($postData->eventType =="ORDER_TRADE_UPDATE"){
        $lineTool->sendToAdmin("\nAPIKEY：{$_GET["API_KEY"]}\nPOST輸入：\n".$postMsgData);
    }
}catch (Exception $exception){
    $postMsgData = "異常->".$exception->getMessage();
    $lineTool->sendToAdmin("\nAPIKEY：{$_GET["API_KEY"]}\nPOST異常：\n".$exception->getMessage());
}

$data = [
    'status' => '400',
    'msg' => '初始化',
];
if (isset($_GET["API_KEY"]) and !empty($_GET["API_KEY"])) {
    $db = DataBaseTool::getInstance();
    $binanceTool = BinanceTool::getInstance();

    try {
        $accessToken = $db->getLineToken($_GET["API_KEY"]);
        $nickName = $db->getNickName($_GET["API_KEY"]);
        $lineTool->setToken($accessToken);
        $notifyArray = $binanceTool->transactionMessageProcessing($postData, $nickName);
        $logStatus = "NEW";

        if ($notifyArray['code'] == '200') {
            if ($lineTool->doLineNotify($notifyArray['msg'])) {
                $logStatus = "SEND";
                $msg = "APIKEY：{$_GET["API_KEY"]}\n輸出：\n" . $notifyArray['msg'];
                $log->writeLog($msg);
                $lineTool->sendToAdmin("\n".$msg);
                $db->upLoadTreadLog($_GET["API_KEY"], $notifyArray['data'],$logStatus);
            }
        }
//        else{
//            $lineTool->sendToAdmin(__FILE__ . "\n輸出({$notifyArray['code']})：\n" . $notifyArray['msg']);
//        }

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
        $lineTool->sendToAdmin( "\n發生未知的錯誤：".$exception->getMessage());
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
