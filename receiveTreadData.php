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

if (isset($_GET["API_KEY"])) {
    $rowData = [];
    $rowData['symbol'] = $_GET['symbol'];
    $rowData['orderId'] = $_GET['orderId'];
    $rowData['orderSide'] = $_GET['orderSide'];
    $rowData['positionSide'] = $_GET['positionSide'];
    $rowData['orderStatus'] = $_GET['orderStatus'];
    $rowData['averagePrice'] = $_GET['averagePrice'];
    $rowData['originalQuantity'] = $_GET['originalQuantity'];
    $db = DataBaseTool::getInstance();
    try {
        $db->upLoadTreadLog($_GET["API_KEY"], $rowData);
//        $response = json_decode(file_get_contents($url_all));
//        $data = ['price' => (double)$response->price];
//        $data = $rowData["order"]["symbol"];
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
        'msg' => '密碼錯誤',
    ];
}
echo json_encode($data);
