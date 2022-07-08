<?php
/**
 * 開發者 User
 * 創建於 2022/7/7
 * 使用   PhpStorm
 * 專案名稱binanceTool
 */

require_once '/home/cryptoharvester/public_html/binanceToolApi/vendor/autoload.php';

$listRows = shell_exec('npx forever list');
$listRows = explode(" ", $listRows);
$arrForeverList = [];
foreach ($listRows as $key => $listRow) {
    if (str_contains($listRow, "ws-userdata")) {
        $arrForeverList[] = $listRow;
    }
}
$liveAccountList = [];
foreach ($arrForeverList as $row) {
    $step1 = explode(".", $row);
    $step2 = explode("-", $step1[0]);
    $result = ['API_KEY' => $step2[2], 'API_SECRET' => $step2[3]];
    $liveAccountList[] = $result;
}
var_dump($liveAccountList);