<?php
/**
 * 開發者 User
 * 創建於 2022/7/12
 * 使用   PhpStorm
 * 專案名稱binanceTool
 */
declare(strict_types=1);

require_once __DIR__ . '/class/autoload.php';

header('Content-Type: application/json; charset=utf-8');

if (isset($_GET["PASSWORD"]) and $_GET["PASSWORD"] == "GCP") {
    $data = match ($_GET["TYPE"]) {
        '1' => [
            'status' => '200',
            'msg' => [
                '帳戶資產' => 7777,
                '錢包餘額' => 9999,
                '保證金率' => 8,
                '當前浮虧' => 2333,
                '可用金額' => 6666,
            ],
        ],
        '2' => [
            'status' => '200',
            'msg' => [
                '帳號' => 'abc777gg',
                '密碼' => 'djqwjiojcioawewjoid',
                '使用者名稱' => '阿銘',
                '生日' => '1980-12-32',
                '點卡餘額' => 8888,
            ],
        ],
        '3' => [
            'status' => '200',
            'msg' => [
                '會員功能' => [
                    '自動開平倉' => true,
                    '倉位自動通知' => false,
                ],
                '加值功能' => [
                    '保證金率通知' => true,
                ],
                '過期時間' => '2022-12-31',
            ],
        ],
        default => [
            'status' => '400',
            'msg' => '未攜帶參數[TYPE]或值不合法',
        ],
    };

} else {
    $data = [
        'status' => '400',
        'msg' => '密碼錯誤',
    ];
}
echo json_encode($data);
