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

    private function getTimestamp(){
        return round(microtime(true) * 1000);
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
        $timeStamp = strval("timestamp=".$this->getTimestamp());
        $url = $this->futuresUrl.$uri."?".$timeStamp;
        $signature = "signature=".$this->getSignature($this->futuresUrl.$uri."&".$timeStamp);
        $url .="&".$signature;
        $header = ['X-MBX-APIKEY:'.$this->apiKey];
        var_dump($url);
        $data = $this->curlTool->doGet($url,$header);

        return $data;
    }

    private function getSignature($queryString){
        return hash_hmac('sha256', $queryString, $this->apiSecret);
    }
}
