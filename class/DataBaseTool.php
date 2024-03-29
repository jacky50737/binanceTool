<?php

class DataBaseTool
{
    protected string $server;    # MySQL/MariaDB 伺服器
    protected string $user;      # 使用者帳號
    protected string $password;  # 使用者密碼
    protected string $dbname;    # 資料庫名稱
    protected object $connection;

    /**
     * @var
     */
    private static $instance;

    /**
     * @return DataBaseTool
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        $configs = include(__DIR__ . '/../config/database.php');
        $this->server = $configs['server'];
        $this->user = $configs['user'];
        $this->password = $configs['password'];
        $this->dbname = $configs['dbname'];

        //連接 MySQL/MariaDB 資料庫
        $this->connection = new mysqli($this->server, $this->user, $this->password, $this->dbname);
        // 設定語言編碼為UTF8
        $this->connection->set_charset("utf8");
    }

    /**
     * 驗證遊戲期數是否存在(true存在 false不存在)
     * @param string $game
     * @return bool
     */
    public function checkGame(string $game): bool
    {
        $sqlQuery = "SELECT * FROM DATA WHERE game = " . $game . ";";

        if ($this->connection->query($sqlQuery)) {
            if (!is_null($this->connection->query($sqlQuery)->fetch_row())) {
                return true;
            }
        }
        return false;
    }

    /**
     * 驗證使用者功能設定檔是否存在(true存在 false不存在)
     * @param string $apiKey
     * @param string $featureName
     * @return bool
     */
    public function checkUserFeature(string $apiKey, string $featureName): bool
    {
        $sqlQuery = "SELECT * FROM ACCOUNT_FEATURE WHERE ACCOUNT_KEY = '" . $apiKey . "' AND FEATURE_NAME ='".$featureName."';";

        if ($this->connection->query($sqlQuery)) {
            if (!is_null($this->connection->query($sqlQuery)->fetch_row())) {
                return true;
            }
        }
        return false;
    }

    /**
     * 查詢特定功能的合法列表
     * @param string $featureName
     * @return bool|array
     */
    public function checkUserFeatureStatus(string $featureName = ""): bool|array
    {
        if(!empty($featureName)){
            $sqlQuery = "SELECT ACCOUNT_KEY FROM ACCOUNT_FEATURE WHERE FEATURE_NAME ='" . $featureName . "' AND STATUS ='ENABLE' AND EXPIRED_DAY > CURRENT_TIMESTAMP;";
        }else{
            $sqlQuery = "SELECT ACCOUNT_KEY FROM ACCOUNT_FEATURE WHERE STATUS ='ENABLE';";
        }


        if ($this->connection->query($sqlQuery)) {
            $rows = $this->connection->query($sqlQuery)->fetch_all();
            if (is_array($rows)) {
                $data = [];
                foreach ($rows as $row) {
                    if (is_string($row[0])) {
                        $data[] = $row[0];
                    }
                }
                return $data;
            }
        }
        return false;
    }
    
    /**
     * 查詢串接數量的過期列表
     */
    public function getApiLimitExpirList()
    {
        $now = date('Y-m-d H:i:s', strtotime('+8 hours'));
        $sqlQuery = "SELECT LINE_ID,API_LIMIT  FROM ACCOUNT_LIMIT WHERE API_LIMIT > (2) AND EXPIR_DAY <'" . $now . "';";
        if ($this->connection->query($sqlQuery)) {
            $rows = $this->connection->query($sqlQuery)->fetch_all();
            return $rows;
        }
        return false;
    }

    /**
     * 查詢特定功能的合法列表
     * @param string $line_Id
     * @return array|bool
     */
    public function getUserFeatureStatus(string $line_Id): bool|string
    {
        $sqlQuery = "SELECT BINANCE_API_KEY.NICK_NAME as NAME, FEATURE_NAME, STATUS, EXPIRED_DAY FROM ACCOUNT_FEATURE LEFT JOIN BINANCE_API_KEY on BINANCE_API_KEY.API_KEY = ACCOUNT_KEY WHERE BINANCE_API_KEY.LINE_ID = '".$line_Id."' ORDER by BINANCE_API_KEY.NICK_NAME";

        $nameArray = [ '帳戶','功能名稱','狀態','過期時間'];
        if ($this->connection->query($sqlQuery)) {
            $rows = $this->connection->query($sqlQuery)->fetch_all();
            if (is_array($rows)) {
                $data = "";
                foreach ($rows as $row) {
                    foreach($row as $key => $userData){
                        switch($userData){
                            case 'AUTO_WALLET_NOTIFY':
                                $userData = '資產通知';
                                break;
                            case 'AUTO_ORDER_NOTIFY':
                                $userData = '開平倉通知';
                                break;
                            case 'ENABLE':
                                $userData = '開';
                                break;
                            case 'DISABLE':
                                $userData = '關';
                                break;
                            case 'EXPIRED':
                                $userData = '已失效';
                                break;
                        }
                        $data .= "\n".$nameArray[$key]."：".$userData;
                    }
                    $data .= "\n---";
                }
                return $data;
            }
        }
        return false;
    }

    /**
     * 查詢帳戶資產功能的合法列表
     * @param string $line_Id
     * @return array|bool
     */
    public function getUserWalletStatus(string $line_Id): bool|array
    {
        $sqlQuery = "SELECT BINANCE_API_KEY.NICK_NAME as NAME, ACCOUNT_KEY FROM ACCOUNT_FEATURE LEFT JOIN BINANCE_API_KEY on BINANCE_API_KEY.API_KEY = ACCOUNT_KEY WHERE STATUS = 'ENABLE' and FEATURE_NAME = 'AUTO_WALLET_NOTIFY' and BINANCE_API_KEY.LINE_ID = '".$line_Id."'";

        $nameArray = [ 'name','key'];
        if ($this->connection->query($sqlQuery)) {
            $rows = $this->connection->query($sqlQuery)->fetch_all();
            if (is_array($rows)) {
                $data = [];
                foreach($rows as $nowRow => $row){
                    foreach($row as $key => $value){
                        $data[$nowRow][$nameArray[$key]] = $value;
                    }
                }
                return $data;
            }
        }
        return false;
    }

    /**
     * 查詢所有使用者的LineAccusesToken列表
     * @param $userID
     * @return array|bool
     */
    public function checkUserAccusesToken($userID = []): bool|array
    {
        if(empty($userID)){
            $sqlQuery = "SELECT DISTINCT ACCESS_TOKEN FROM BINANCE_API_KEY ;";
        }else{
            $sqlQuery = "SELECT DISTINCT ACCESS_TOKEN FROM BINANCE_API_KEY WHERE LINE_ID IN ('" . implode("','", $userID) . "');";
        }

        if ($this->connection->query($sqlQuery)) {
            $rows = $this->connection->query($sqlQuery)->fetch_all();
            if (is_array($rows)) {
                $data = [];
                foreach ($rows as $row) {
                    if (is_string($row[0])) {
                        $data[] = $row[0];
                    }
                }
                return $data;
            }
        }
        return false;
    }

    /**
     * 查詢使用者的AccusesToken列表
     * @param $userID
     * @return array|bool
     */
    public function checkUserAccusesTokenLlist($userID, $sort = 'ASC'): bool|array
    {
        $sqlQuery = "SELECT API_KEY FROM BINANCE_API_KEY WHERE LINE_ID = '".$userID."' ORDER BY id {$sort} ;";

        if ($this->connection->query($sqlQuery)) {
            $rows = $this->connection->query($sqlQuery)->fetch_all();
            if (is_array($rows)) {
                $data = [];
                foreach ($rows as $row) {
                    if (is_string($row[0])) {
                        $data[] = $row[0];
                    }
                }
                return $data;
            }
        }
        return false;
    }


    /**
     * 更新使用者功能設定檔
     * @param string $apiKey
     * @param string $featureName
     * @param string $status
     * @param string $expiredDay
     * @return bool
     */
    public function updateUserFeature(string $apiKey, string $featureName, string $status, string $expiredDay = ""): bool
    {
        if(!empty($expiredDay)){
            $sqlQuery = "UPDATE ACCOUNT_FEATURE SET STATUS='".$status."' , EXPIRED_DAY ='".$expiredDay.
                "' WHERE ACCOUNT_KEY='" . $apiKey .
                "' AND FEATURE_NAME='".$featureName."';";
        }else{
            $sqlQuery = "UPDATE ACCOUNT_FEATURE SET STATUS='".$status."' WHERE ACCOUNT_KEY='" . $apiKey . "';";
        }


        for ($i = 0; $i < 5; $i++) {

            if ($this->connection->query($sqlQuery)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 更新使用者功能過期時間
     * @param array $apiKey
     * @param string $expiredDay
     * @return bool
     */
    public function updateUserFeatureExpiredDay(array $apiKey, string $expiredDay = ""): bool
    {

        
        $sqlQuery = "UPDATE ACCOUNT_FEATURE SET EXPIRED_DAY ='".$expiredDay."' WHERE ACCOUNT_KEY IN ('" . implode("','", $apiKey) . "');";

        for ($i = 0; $i < 5; $i++) {

            if ($this->connection->query($sqlQuery)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 更新使用者本金
     * @param string $apiKey
     * @param int $capital
     * @return bool
     */
    public function updateUserCapital(string $apiKey, int $capital): bool
    {
        $sqlQuery = "UPDATE BINANCE_API_KEY SET CAPITAL=".$capital." WHERE API_KEY='" . $apiKey ."';";

        for ($i = 0; $i < 5; $i++) {

            if ($this->connection->query($sqlQuery)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 新增使用者功能設定檔
     * @param string $apiKey
     * @param string $featureName
     * @param string $status
     * @param string $expiredDay
     * @return bool
     */
    public function insertUserFeature(string $apiKey, string $featureName, string $status, string $expiredDay): bool
    {
        $sqlQuery = "INSERT INTO ACCOUNT_FEATURE" .
            "(ACCOUNT_KEY, FEATURE_NAME, STATUS, EXPIRED_DAY)" .
            " VALUES ('" .
            strval($apiKey) . "', '" .
            strval($featureName) . "', '" .
            strval($status) . "', '" .
            strval($expiredDay) .  "')";

        for ($i = 0; $i < 5; $i++) {
            if ($this->connection->query($sqlQuery)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 拿交易紀錄(最多一次一百筆)
     * @param string $apiKey
     * @return bool
     */
    public function getTreadLog(string $apiKey){
        $sqlQuery = "SELECT * FROM TREAD_LOG WHERE API_KEY = '" . strval($apiKey) . "' and LOG_STATUS = 'NEW' Order By ID ASC LIMIT 100;";

        if ($this->connection->query($sqlQuery)) {
            if ($this->connection->query($sqlQuery)->fetch_all()[0]) {
                var_dump($this->connection->query($sqlQuery)->fetch_array());
                return true;
            }
        }
        return false;
    }

    /**
     * 標記已發送的交易紀錄
     * @param string $orderId
     * @return bool
     */
    public function tagTreadLog(string $orderId){
        $sqlQuery = "UPDATE TREAD_LOG SET LOG_STATUS='SEND' WHERE ORDER_ID='" . $orderId . "';";
        for ($i = 0; $i < 5; $i++) {
            if ($this->connection->query($sqlQuery)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 檢查使用者Line ID是否已存在
     * @param string $lineId
     * @return bool
     */
    public function checkUser(string $lineId): bool
    {
        $sqlQuery = "SELECT count(*) FROM BINANCE_API_KEY WHERE LINE_ID = '" . strval($lineId) . "';";

        if ($this->connection->query($sqlQuery)) {
            if ($this->connection->query($sqlQuery)->fetch_row()[0] >= 1) {
                return true;
            }
        }
        return false;
    }

    /**
     * 總使用者數量
     */
    public function checkUserCount()
    {
        $sqlQuery = "SELECT count(*) FROM BINANCE_API_KEY ;";

        if ($this->connection->query($sqlQuery)) {
            if ($this->connection->query($sqlQuery)->fetch_row()[0] >= 1) {
                return $this->connection->query($sqlQuery)->fetch_row()[0];
            }
        }
        return false;
    }

    /**
     * 檢查串接功能中的使用者數量
     * @param string $featureName
     * @return false|mixed
     */
    public function checkFeatureCount(string $featureName): mixed
    {
        $sqlQuery = "SELECT count(*) FROM ACCOUNT_FEATURE WHERE FEATURE_NAME = '" . strval($featureName) . "' AND STATUS = 'ENABLE';";

        if ($this->connection->query($sqlQuery)) {
            if ($this->connection->query($sqlQuery)->fetch_row()[0] >= 1) {
                return $this->connection->query($sqlQuery)->fetch_row()[0];
            }
        }
        return false;
    }

    /**
     * 檢查使用者暱稱是否已存在
     * @param string $lineId
     * @param string $nickName
     * @return bool
     */
    public function checkUserNickName(string $lineId, string $nickName)
    {
        $sqlQuery = "SELECT count(*) FROM BINANCE_API_KEY WHERE LINE_ID = '" . strval($lineId) . "' AND NICK_NAME = '" . strval($nickName) . "';";

        if ($this->connection->query($sqlQuery)) {
            if ($this->connection->query($sqlQuery)->fetch_row()[0] >= 1) {
                return true;
            }
        }
        return false;
    }

    /**
     * 檢查使用者API KEY是否已存在
     * @param string $apiKey
     * @return bool
     */
    public function checkApiKey(string $apiKey): bool
    {
        $sqlQuery = "SELECT count(*) FROM BINANCE_API_KEY WHERE API_KEY = '" . strval($apiKey) . "';";

        if ($this->connection->query($sqlQuery)) {
            if ($this->connection->query($sqlQuery)->fetch_row()[0] >= 1) {
                return true;
            }
        }
        return false;
    }

    /**
     * 檢查使用者API KEY串接數量
     * @param string $lineId
     */
    public function checkApiKeyCount(string $lineId)
    {
        $sqlQuery = "SELECT count(*) FROM BINANCE_API_KEY WHERE LINE_ID = '" . strval($lineId) . "';";

        if ($this->connection->query($sqlQuery)) {
            if ($this->connection->query($sqlQuery)->fetch_row()[0]) {
                return $this->connection->query($sqlQuery)->fetch_row()[0];
            }
        }
        return 0;
    }

    /**
     * 檢查使用者API KEY可串接數量
     *
     * @param string $lineId
     * @return int|mixed|void
     */
    public function checkApiKeyCountLimit(string $lineId)
    {
        $sqlQuery = "SELECT API_LIMIT FROM ACCOUNT_LIMIT WHERE LINE_ID = '" . strval($lineId) . "';";

        if ($this->connection->query($sqlQuery)) {
            if(is_null($this->connection->query($sqlQuery)->fetch_row())){
                if($this->inputUserApiKeyLimit($lineId, 2, date('Y-m-d 23:59:59', strtotime('now')))){
                    return 2;
                }else{
                    return 0;
                };
            }
            if($this->connection->query($sqlQuery)->fetch_row()[0]){
                return $this->connection->query($sqlQuery)->fetch_row()[0];
            }
        }
    }

    /**
     * 寫入使用者
     * @param string $nickName
     * @param string $apiKey
     * @param string $apiSecret
     * @param string $lineId
     * @param string $accessToken
     * @return bool
     */
    public function inputUser(string $nickName, string $apiKey, string $apiSecret, string $lineId, string $accessToken): bool
    {
        $sqlQuery = "INSERT INTO BINANCE_API_KEY" .
            "(NICK_NAME, API_KEY, API_SECRET, LINE_ID, ACCESS_TOKEN)" .
            " VALUES ('" .
            strval($nickName) . "', '" .
            strval($apiKey) . "', '" .
            strval($apiSecret) . "', '" .
            strval($lineId) .  "', '" .
            strval($accessToken) . "')";

        for ($i = 0; $i < 5; $i++) {
            if ($this->connection->query($sqlQuery)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 寫入使用者綁定上限
     * @param string $nickName
     * @param string $apiKey
     * @param string $apiSecret
     * @param string $lineId
     * @param string $accessToken
     * @return bool
     */
    public function inputUserApiKeyLimit(string $lineId, string $apiLimit, string $expiredDay): bool
    {
        $sqlQuery = "INSERT INTO ACCOUNT_LIMIT" .
            "(LINE_ID, API_LIMIT, EXPIR_DAY, UPDATE_AT)" .
            " VALUES ('" .
            strval($lineId) . "', '" .
            intval($apiLimit) . "', '" .
            strval($expiredDay) . "', '" .
            strval(date('Y-m-d H:i:s',strtotime('now'))) . "')";
        for ($i = 0; $i < 5; $i++) {
            if ($this->connection->query($sqlQuery)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 更新使用者名稱
     * @param string $nickName
     * @param string $apiKey
     * @return bool
     */
    public function updateUserName(string $nickName, string $apiKey): bool
    {
        $sqlQuery = "UPDATE BINANCE_API_KEY SET NICK_NAME='".$nickName."'WHERE API_KEY='" . $apiKey . "';";

        for ($i = 0; $i < 5; $i++) {
            if ($this->connection->query($sqlQuery)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 更新使用者API串接上限與過期時間
     *
     * @param string $lineId
     * @param integer $apiLimit
     * @param string $expiredDay
     * @return boolean
     */
    public function updateUserApiLimit(string $lineId, int $apiLimit, string $expiredDay): bool
    {
        $sqlQuery = "UPDATE ACCOUNT_LIMIT SET API_LIMIT=(".$apiLimit."), EXPIR_DAY='" . $expiredDay ."'WHERE LINE_ID='" . $lineId . "';";

        for ($i = 0; $i < 5; $i++) {
            if ($this->connection->query($sqlQuery)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 更新使用者名稱
     * @param string $acessToken
     * @param string $lineId
     * @return bool
     */
    public function updateUserLineToken(string $acessToken, string $lineId): bool
    {
        $sqlQuery = "UPDATE BINANCE_API_KEY SET ACCESS_TOKEN='".$acessToken."'WHERE LINE_ID='" . $lineId . "';";

        for ($i = 0; $i < 5; $i++) {
            if ($this->connection->query($sqlQuery)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 刪除使用者帳戶
     * @param string $apiKey
     * @return bool
     */
    public function deleteUser(string $apiKey, string $line_Id): bool
    {
        $sqlQuery = "DELETE FROM BINANCE_API_KEY WHERE API_KEY='" . strval($apiKey) . "' AND LINE_ID='" . strval($line_Id) . "';";

        $runStepOne = false;
        $runStepTwo = false;

        for ($i = 0; $i < 5; $i++) {
            if ($this->connection->query($sqlQuery)) {
                $runStepOne =  true;
            }
        }

        if ($runStepOne) {
            $sqlQuery = "DELETE FROM ACCOUNT_FEATURE WHERE ACCOUNT_KEY='" . strval($apiKey) . "';";
            for ($i = 0; $i < 5; $i++) {
                if ($this->connection->query($sqlQuery)) {
                    $runStepTwo =  true;
                }
            }
        }

        $runTag = $runStepOne && $runStepTwo;
        return $runTag;
    }

    /**
     * 取的API的KEY跟SECRET
     * @param string $lineId
     * @return bool|array
     */
    public function getApiKey(string $lineId): bool|array
    {
        $sqlQuery = "SELECT API_KEY, API_SECRET FROM BINANCE_API_KEY WHERE LINE_ID = '" . strval($lineId) . "';";

        if ($this->connection->query($sqlQuery)) {
            $rows = $this->connection->query($sqlQuery)->fetch_all();
            if (is_array($rows)) {
                $data = [];
                foreach ($rows as $row){
                    $data[] = ['API_KEY' => $row[0], 'API_SECRET' => $row[1]];
                }
                return $data;
            }
        }
        return false;
    }

    /**
     * 取的APIKEY的SECRET
     * @param string $apiKey
     * @return bool|array
     */
    public function getApiSecret(string $apiKey): bool|array
    {
        $sqlQuery = "SELECT API_SECRET FROM BINANCE_API_KEY WHERE API_KEY = '" . strval($apiKey) . "';";

        if ($this->connection->query($sqlQuery)) {
            $rows = $this->connection->query($sqlQuery)->fetch_all();
            if (is_array($rows)) {
                $data = [];
                foreach ($rows as $row){
                    $data[] = $row[0];
                }
                return $data;
            }
        }
        return false;
    }

    /**
     * 取的APIKEY的本金
     * 記的從[0]拿資料
     * @param string $apiKey
     * @return bool|array
     */
    public function getUserCapital(string $apiKey): bool|array
    {
        $sqlQuery = "SELECT CAPITAL FROM BINANCE_API_KEY WHERE API_KEY = '" . strval($apiKey) . "';";

        if ($this->connection->query($sqlQuery)) {
            $rows = $this->connection->query($sqlQuery)->fetch_all();
            if (is_array($rows)) {
                return $rows[0];
            }
        }
        return false;
    }

    /**
     * 取得Line TOKEN
     * @param string $apiKey
     * @return bool|array
     */
    public function getLineToken(string $apiKey): bool|string
    {
        $sqlQuery = "SELECT ACCESS_TOKEN FROM BINANCE_API_KEY WHERE API_KEY = '" . strval($apiKey) . "';";

        if ($this->connection->query($sqlQuery)) {
            $rows = $this->connection->query($sqlQuery)->fetch_all()[0];
            if (is_array($rows)) {
                return $rows[0];
            }
        }
        return false;
    }

    /**
     * 取得Line ID
     * @param string $apiKey
     * @return bool|array
     */
    public function getLineId(string $apiKey): bool|string
    {
        $sqlQuery = "SELECT LINE_ID FROM BINANCE_API_KEY WHERE API_KEY = '" . strval($apiKey) . "';";

        if ($this->connection->query($sqlQuery)) {
            $rows = $this->connection->query($sqlQuery)->fetch_all()[0];
            if (is_array($rows)) {
                return $rows[0];
            }
        }
        return false;
    }

    /**
     * 取的APIKEY的NickName
     * @param string $apiKey
     * @return bool|array
     */
    public function getNickName(string $apiKey): bool|string
    {
        $sqlQuery = "SELECT NICK_NAME FROM BINANCE_API_KEY WHERE API_KEY = '" . strval($apiKey) . "';";

        if ($this->connection->query($sqlQuery)) {
            $rows = $this->connection->query($sqlQuery)->fetch_all()[0];
            if (is_array($rows)) {
                return $rows[0];
            }
        }
        return false;
    }

    public function getTreadLogByOrderId($orderId,$status,$apiKey)
    {
        $sqlQuery = "SELECT ORDER_ID, ORDER_COMMISSION, ORDER_COMMISSION_ASSET, ORDER_PROFIT FROM TREAD_LOG WHERE ORDER_ID = '" . strval($orderId) . "' AND API_KEY = '" . strval($apiKey) . "' AND ORDER_STATUS IN ('".implode("','",$status)."');";

        if ($this->connection->query($sqlQuery)) {
            if (!empty($this->connection->query($sqlQuery)->fetch_all())) {
                return $this->connection->query($sqlQuery)->fetch_all();
            }
        }
        return false;
    }

    /**
     * 建立訂單
     * @param string $apiKey
     * @param object $data
     * @param string $status
     * @return bool
     */
    public function insertTreadLog(string $apiKey, object $data,string $status = "NEW"): bool
    {
        $sqlQuery = "INSERT INTO TREAD_LOG" .
            "(SYMBOL, ORDER_ID,ORDER_SIDE, POSITION_SIDE,ORDER_STATUS, ORDER_PRICE, ORDER_QTY, ORDER_COMMISSION, ORDER_COMMISSION_ASSET, ORDER_PROFIT, API_KEY, LOG_STATUS)" .
            " VALUES ('" . strval($data->symbol) . "', '" .
            strval($data->orderId) . "', '" .
            strval($data->orderSide) . "', '" .
            strval($data->positionSide) . "', '" .
            strval($data->orderStatus) . "', '" .
            strval($data->averagePrice) . "',' " .
            strval($data->originalQuantity) . "', '" .
            strval($data->commissionAmount) . "', '" .
            strval($data->commissionAsset) . "', '" .
            strval($data->realisedProfit) . "', '" .
            strval($apiKey) . "',' " .
            strval($status) . "')";

        for ($i = 0; $i < 5; $i++) {
            if ($this->connection->query($sqlQuery) == TRUE) {
                return true;
            }
        }
        return false;
    }

    /**
     * 寫入交易並回傳成功與否
     * @param string $apiKey
     * @param object $data
     * @param string $status
     * @return bool
     */
    public function upLoadTreadLog(string $apiKey, object $data,string $status = "NEW"): bool
    {
        $sqlQuery = "INSERT INTO TREAD_LOG" .
            "(SYMBOL, ORDER_ID,ORDER_SIDE, POSITION_SIDE,ORDER_STATUS, ORDER_PRICE, ORDER_QTY, ORDER_COMMISSION, ORDER_COMMISSION_ASSET, ORDER_PROFIT, API_KEY, LOG_STATUS)" .
            " VALUES ('" . strval($data->symbol) . "', '" .
            strval($data->orderId) . "', '" .
            strval($data->orderSide) . "', '" .
            strval($data->positionSide) . "', '" .
            strval($data->orderStatus) . "', '" .
            strval($data->averagePrice) . "',' " .
            strval($data->originalQuantity) . "', '" .
            strval($data->commissionAmount) . "', '" .
            strval($data->commissionAsset) . "', '" .
            strval($data->realisedProfit) . "', '" .
            strval($apiKey) . "',' " .
            strval($status) . "')";

        for ($i = 0; $i < 5; $i++) {
            if ($this->connection->query($sqlQuery) == TRUE) {
                return true;
            }
        }
        return false;
    }

    public function closeDB()
    {
        # 釋放資源
        $this->connection->close();
    }

}
