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
     * @return array|bool
     */
    public function checkUserFeatureStatus(string $featureName): bool|array
    {
        $sqlQuery = "SELECT ACCOUNT_KEY FROM ACCOUNT_FEATURE WHERE FEATURE_NAME ='" . $featureName . "' AND STATUS ='ENABLE' AND EXPIRED_DAY > CURRENT_TIMESTAMP;";

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
    public function updateUserFeature(string $apiKey, string $featureName, string $status, string $expiredDay): bool
    {
        $sqlQuery = "UPDATE ACCOUNT_FEATURE SET STATUS='".$status."' , EXPIRED_DAY ='".$expiredDay.
            "' WHERE ACCOUNT_KEY='" . $apiKey .
            "' AND FEATURE_NAME='".$featureName."';";

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
        $sqlQuery = "UPDATE BINANCE_API_KEY SET CAPITAL=".$capital." WHERE ACCOUNT_KEY='" . $apiKey ."';";
var_dump($this->connection->query($sqlQuery));
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
        $sqlQuery = "UPDATE TREAD_LOG SET LOG_STATUS='SEND' WHERE ORDER_ID='" . $orderId . "'";
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
     * 檢查使用者暱稱是否已存在
     * @param string $lineId
     * @param string $nickName
     * @return bool
     */
    public function checkUserNickName(string $lineId, string $nickName): bool
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
     * 更新使用者名稱
     * @param string $nickName
     * @param string $apiKey
     * @return bool
     */
    public function updateUserName(string $nickName, string $apiKey): bool
    {
        $sqlQuery = "UPDATE BINANCE_API_KEY SET NICK_NAME='".$nickName."'WHERE API_KEY='" . $apiKey . "'";

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
        $sqlQuery = "UPDATE BINANCE_API_KEY SET ACCESS_TOKEN='".$acessToken."'WHERE LINE_ID='" . $lineId . "'";

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
    public function deleteUser(string $apiKey): bool
    {
        $sqlQuery = "DELETE FROM BINANCE_API_KEY WHERE API_KEY='" . strval($apiKey) . "'";

        for ($i = 0; $i < 5; $i++) {
            if ($this->connection->query($sqlQuery)) {
                return true;
            }
        }
        return false;
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
     * @param string $apiKey
     * @return bool|array
     */
    public function getUserCapital(string $apiKey): bool|array
    {
        $sqlQuery = "SELECT CAPITAL FROM BINANCE_API_KEY WHERE API_KEY = '" . strval($apiKey) . "';";

        if ($this->connection->query($sqlQuery)) {
            $rows = $this->connection->query($sqlQuery)->fetch_all();
            if (is_array($rows)) {
                $data = [];
                foreach ($rows as $row){
                    $data[] = $row[0];
                }
                return $data[0];
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

    /**
     * 寫入交易並回傳成功與否
     * @param string $apiKey
     * @param array $data
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
