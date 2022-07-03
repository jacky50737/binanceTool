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
     * @param string $apiKey
     * @param array $data
     * @return bool
     */
    public function upLoadTreadLog(string $apiKey, array $data): bool
    {
        $sqlQuery = "INSERT INTO TREAD_LOG" .
            "(SYMBOL, ORDER_ID,ORDER_SIDE, POSITION_SIDE,ORDER_STATUS, ORDER_PRICE, ORDER_QTY, API_KEY, LOG_STATUS)" .
            " VALUES ('" .strval($data['symbol']) . "', '" .
            strval($data['orderId']) . "', '" .
            strval($data['orderSide']) . "', '" .
            strval($data['positionSide']) . "', '" .
            strval($data['orderStatus']) . "', '" .
            strval($data['averagePrice']) . "',' " .
            strval($data['originalQuantity']) . "', '" .
            strval($apiKey) . "',' " . "NEW"."')";

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
