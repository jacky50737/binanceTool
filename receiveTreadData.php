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

if (isset($_GET["API_KEY"]) and $_GET['orderStatus'] == "FILLED") {
    $rowData = [];
    $rowData['symbol'] = $_GET['symbol'];
    $rowData['orderId'] = $_GET['orderId'];
    $rowData['orderSide'] = $_GET['orderSide'];
    $rowData['positionSide'] = $_GET['positionSide'];
    $rowData['orderStatus'] = $_GET['orderStatus'];
    $rowData['averagePrice'] = $_GET['averagePrice'];
    $rowData['originalQuantity'] = $_GET['originalQuantity'];
    $rowData['commissionAmount'] = $_GET['commissionAmount'];
    $rowData['realisedProfit'] = $_GET['realisedProfit'];

    $db = DataBaseTool::getInstance();
    $lineTool = LineNotify::getInstance();
    $binanceTool = BinanceTool::getInstance();

    try {
        $accessToken = $db->getLineToken($_GET["API_KEY"]);
        $nickName = $db->getNickName($_GET["API_KEY"]);
        $lineTool->setToken($accessToken);
        $orderStatus = $binanceTool->transferStockStatus($rowData['orderSide'],$rowData['positionSide']);
        $notifyString  = "\n帳戶名稱：".$nickName;
        $notifyString .= "\n幣種：".$rowData['symbol'];
        $notifyString .= "\n狀態：".$orderStatus;
        $notifyString .= "\n成交均價：".$rowData['averagePrice'];
        $notifyString .= "\n成交數量：".$rowData['originalQuantity'];
        $notifyString .= "\n交易手續費：".$rowData['commissionAmount'];
        $notifyString .= "\n實現利潤：".$rowData['realisedProfit'];
        $logStatus = "NEW";
        if($lineTool->doLineNotify($notifyString)){
            $logStatus = "SEND";
        }
        $db->upLoadTreadLog($_GET["API_KEY"], $rowData,$logStatus);
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
    } catch (Exception $e) {
        $data = [
            'status' => '400',
            'msg' => '發生未知的錯誤',
            'error' => $e->getMessage()
        ];
    }
    $db->closeDB();
} else {
    $data = [
        'status' => '200',
        'msg' => '參數錯誤',
    ];
}
echo json_encode($data);
exit(0);
