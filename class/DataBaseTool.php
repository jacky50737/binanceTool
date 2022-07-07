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

        # 連接 MySQL/MariaDB 資料庫
        $this->connection = new mysqli($this->server, $this->user, $this->password, $this->dbname);
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
            if ($this->connection->query($sqlQuery) == TRUE) {
                return true;
            }
        }
        return false;
    }

    /**
     * 檢查使用者是否已存在
     * @param string $lineId
     * @return bool
     */
    public function checkUser(string $lineId): bool
    {
        $sqlQuery = "SELECT count(*) FROM BINANCE_API_KEY WHERE LINE_ID = '" . strval($lineId) . "';";

        if ($this->connection->query($sqlQuery)) {
            if ($this->connection->query($sqlQuery)->fetch_all()[0]) {
                return true;
            }
        }
        return false;
    }

    /**
     * 寫入使用者
     * @param string $apiKey
     * @param string $apiSecret
     * @param string $lineId
     * @return bool
     */
    public function inputUser(string $apiKey, string $apiSecret, string $lineId, string $accessToken): bool
    {
        $sqlQuery = "INSERT INTO BINANCE_API_KEY" .
            "(API_KEY, API_SECRET, LINE_ID, ACCESS_TOKEN)" .
            " VALUES ('" . strval($apiKey) . "', '" .
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
     * 取的API的KEY跟SECRET
     * @param string $lineId
     * @return bool|array
     */
    public function getApiKey(string $lineId): bool|array
    {
        $sqlQuery = "SELECT API_KEY, API_SECRET FROM BINANCE_API_KEY WHERE LINE_ID = '" . strval($lineId) . "';";

        if ($this->connection->query($sqlQuery)) {
            $rows = $this->connection->query($sqlQuery)->fetch_all()[0];
            if (is_array($rows)) {
                return ['API_KEY' => $rows[0], 'API_SECRET' => $rows[1]];
            }
        }
        return false;
    }

    /**
     * 取的API的KEY跟SECRET
     * @param string $lineId
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
     * 取的API的KEY跟SECRET
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
    public function upLoadTreadLog(string $apiKey, array $data,string $status = "NEW"): bool
    {
        $sqlQuery = "INSERT INTO TREAD_LOG" .
            "(SYMBOL, ORDER_ID,ORDER_SIDE, POSITION_SIDE,ORDER_STATUS, ORDER_PRICE, ORDER_QTY, API_KEY, LOG_STATUS)" .
            " VALUES ('" . strval($data['symbol']) . "', '" .
            strval($data['orderId']) . "', '" .
            strval($data['orderSide']) . "', '" .
            strval($data['positionSide']) . "', '" .
            strval($data['orderStatus']) . "', '" .
            strval($data['averagePrice']) . "',' " .
            strval($data['originalQuantity']) . "', '" .
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
