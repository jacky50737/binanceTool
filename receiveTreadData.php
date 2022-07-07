<?php
/**
 * 開發者 User
 * 創建於 2022/7/3
 * 使用   PhpStorm
 * 專案名稱binanceTool
 */

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

header('Content-Type: application/json');

if (isset($_GET["API_KEY"]) and $_GET['orderStatus'] == "FILLED") {
    $rowData = [];
    $rowData['symbol'] = $_GET['symbol'];
    $rowData['orderId'] = $_GET['orderId'];
    $rowData['orderSide'] = $_GET['orderSide'];
    $rowData['positionSide'] = $_GET['positionSide'];
    $rowData['orderStatus'] = $_GET['orderStatus'];
    $rowData['averagePrice'] = $_GET['averagePrice'];
    $rowData['originalQuantity'] = $_GET['originalQuantity'];
    $db = DataBaseTool::getInstance();
    $lineTool = new LineNotify();
    try {
        $accessToken = $db->getLineToken($_GET["API_KEY"]);
        $lineTool->setToken($accessToken);
        $orderStatus = "異常";
        switch ($rowData['orderSide']){
            case 'BUY':
                switch ($rowData['positionSide']){
                    case 'LONG':
                        $orderStatus = '開多';
                        break;
                    case 'SHORT':
                        $orderStatus = '平多';
                        break;
                }
                break;
            case 'SELL':
                switch ($rowData['positionSide']){
                    case 'LONG':
                        $orderStatus = '開空';
                        break;
                    case 'SHORT':
                        $orderStatus = '平空';
                        break;
                }
                break;
        }
        $notifyString  = "幣種：".$rowData['symbol'];
        $notifyString .= "\n狀態：".$orderStatus;
        $notifyString .= "\n成交均價：".$rowData['averagePrice'];
        $notifyString .= "\n成交數量：".$rowData['originalQuantity'];
        $logStatus = "NEW";
        if($lineTool->doLineNotify()){
            $logStatus = "SEND";
        }
        $db->upLoadTreadLog($_GET["API_KEY"], $rowData,$logStatus);

        $data = [
            'status' => '200',
            'msg' => '新增完成',
        ];
    } catch (Exception $e) {
        $data = [
            'status' => '400',
            'msg' => '發生未知的錯誤',
            'error' => $e->getMessage()
        ];
    }
} else {
    $data = [
        'status' => '200',
        'msg' => '參數錯誤',
    ];
}
echo json_encode($data);
exit(0);
