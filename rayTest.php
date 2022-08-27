<?php
/**
 * 開發者 User
 * 創建於 2022/7/12
 * 使用   PhpStorm
 * 專案名稱binanceTool
 */
declare(strict_types=1);
//ForTest
require_once 'class/autoload.php';

header('Content-Type: application/json; charset=utf-8');

if (isset($_GET["PASSWORD"]) and $_GET["PASSWORD"] == "GCP") {
    $data = match ($_GET["TYPE"]) {
        '1' => [
            'status' => '200',
            'msg' => [
                'AccountAssets' => 7777,
                'walletBalance' => 9999,
                'MarginRate' => 8,
                'CurrentFloatingLoss' => 2333,
                'AmountAvailable' => 6666,
            ],
        ],
        '2' => [
            'status' => '200',
            'msg' => [
                'NickName' => 'abc777gg',
                'Username' => 'MingRay888777',
                'Password' => 'djqwjiojcioawewjoid',
                'Birthday' => '1980-12-32',
                'PointCardBalance' => 8888,
            ],
        ],
        '3' => [
            'status' => '200',
            'msg' => [
                'MemberFunction' => [
                    'AutomaticallyPositions' => true,
                    'PositionAutomaticNotification' => false,
                ],
                'AddedFunction' => [
                    'MarginRateNotification' => true,
                ],
                'Expiration' => '2022-12-31',
            ],
        ],
        '4' => [
            'status' => '200',
            'NickName' => 'abc777gg',
        ],
        default => [
            'status' => '400',
            'msg' => 'The parameter [TYPE] is not carried or the value is invalid',
        ],
    };

} else {
    $data = [
        'status' => '400',
        'msg' => 'Password Error!!',
    ];
}
echo json_encode($data);
