<?php
/**
 * 開發者 User
 * 創建於 2022/7/7
 * 使用   PhpStorm
 * 專案名稱binanceTool
 */

require_once '/home/cryptoharvester/public_html/binanceToolApi/class/autoload.php';

//撈DB中的執行名單
$db = DataBaseTool::getInstance();
$onListData = $db->checkUserFeatureStatus("AUTO_ORDER_NOTIFY");
//var_dump($onListData);

//取得本機forever列表並轉換成陣列格式
$listRows = shell_exec('npx forever list');
$listRows = explode(" ", $listRows);
$arrForeverList = [];
foreach ($listRows as $key => $listRow) {
    if (str_contains($listRow, "ws-userdata")) {
        $arrForeverList[] = $listRow;
    }
}
//需執行中帳號的列表
$liveAccountList = [];
foreach ($arrForeverList as $row) {
    $step1 = explode(".", $row);
    $step2 = explode("-", $step1[0]);
    $result = ['API_KEY' => $step2[2], 'API_SECRET' => $step2[3]];
    $liveAccountList[] = $result;
}

//取出本機Key為後續判斷使用
$liveAccountListOnlyKey = [];
foreach ($liveAccountList as $row) {
    $liveAccountListOnlyKey[] = $row['API_KEY'];
}

//var_dump($liveAccountList);
//關閉已被停用的執行並刪除檔案
foreach ($liveAccountList as $row){
    if (!in_array($row['API_KEY'],$onListData)){
        var_dump('npx forever stop '."examples/ws-userdata-{$row['API_KEY']}-{$row['API_SECRET']}.ts");
        var_dump('rm -f '."examples/ws-userdata-{$row['API_KEY']}-{$row['API_SECRET']}.ts");
//        shell_exec('npx forever stop '."examples/ws-userdata-$row[0]-$row[1].ts");
//        shell_exec('rm -f '."examples/ws-userdata-$row[0]-$row[1].ts");
    }
}
//取得DB列表上需要執行的API_SECRET
//並把未執行的執行起來
foreach ($onListData as $row){
    $userData['API_KEY'] = $row;
    $userData['API_SECRET'] = "";
    $userData['LINE_ID'] = $db->getLineToken($row);
    $arrData = $db->getApiKey($userData['LINE_ID']);
    foreach ($arrData as $data){
        if($row == $data['API_KEY']){
            $userData['API_SECRET'] = $data['API_SECRET'];
        }
    }
    if (!in_array($userData['API_KEY'],$liveAccountListOnlyKey)){
        var_dump("touch examples/ws-userdata-{$userData['API_KEY']}-{$userData['API_SECRET']}.ts");
        var_dump("\cp examples/ws-userdata.ts examples/ws-userdata-{$userData['API_KEY']}-{$userData['API_SECRET']}.ts");
        var_dump("npx forever --minUptime=1000 --spinSleepTime=1000 start -c ts-node examples/ws-userdata-{$userData['API_KEY']}-{$userData['API_SECRET']}.ts");
//        shell_exec("touch examples/ws-userdata-{$userData['API_KEY']}-{$userData['API_SECRET']}.ts");
//        shell_exec("\cp examples/ws-userdata.ts examples/ws-userdata-{$userData['API_KEY']}-{$userData['API_SECRET']}.ts");
//        shell_exec("npx forever --minUptime=1000 --spinSleepTime=1000 start -c ts-node examples/ws-userdata-{$userData['API_KEY']}-{$userData['API_SECRET']}.ts");
    }
}





