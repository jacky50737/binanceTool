<?php
/**
 * 開發者 User
 * 創建於 2022/7/4
 * 使用   PhpStorm
 * 專案名稱binanceTool
 */

class BinanceTool
{
    private string $apiSecret;
    private string $apiKey;
    private string $futuresUrl = "https://fapi.binance.com";
    private CurlTool $curlTool;

    public function __construct()
    {
        $this->curlTool = new CurlTool();
    }

    public function setApiKey(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function setApiSecret(string $apiSecret){
        $this->apiSecret = $apiSecret;
    }
    public function transferStockStatus(string $orderSide, string $positionSide): string
    {
        $orderStatus = "異常";
        switch ($orderSide) {
            case 'BUY':
                switch ($positionSide) {
                    case 'LONG':
                        $orderStatus = '開多';
                        break;
                    case 'SHORT':
                        $orderStatus = '平空';
                        break;
                }
                break;
            case 'SELL':
                switch ($positionSide) {
                    case 'LONG':
                        $orderStatus = '平多';
                        break;
                    case 'SHORT':
                        $orderStatus = '開空';
                        break;
                }
                break;
        }
        return $orderStatus;
    }

    public function checkBinanceApi()
    {
        $rows = $this->getAccountInfo();
        var_dump($rows);

        return false;
    }

    public function getAccountInfo()
    {
        $uri = "/fapi/v2/account";
        $timeStamp = strval("signature=".time());
        $url = $this->futuresUrl.$uri."?".$timeStamp;
        $signature = $this->getSignature($this->futuresUrl.$uri."&".$timeStamp);
        $header = ['X-MBX-APIKEY:'.$this->apiKey];
        var_dump($header);
        var_dump($timeStamp);
        var_dump($url."&".$signature);
        $data = $this->curlTool->doGet($url."&".$signature."&".$timeStamp,$header);

        return $data;
    }

    private function getSignature($queryString){
        return hash_hmac('sha256', $queryString, $this->apiSecret);
    }
}
