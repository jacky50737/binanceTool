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
    private string $baseUrl = "https://fapi.binance.com/";
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
        $response = $this->signedRequest('GET', 'fapi/v2/account');
        return json_decode($response);
    }

    private function signature($queryString){
        return hash_hmac('sha256', $queryString, $this->apiSecret);
    }

    private function signedRequest(string $method, string $path, array $parameters = []) {

        $parameters['timestamp'] = round(microtime(true) * 1000);
        $query = $this->buildQuery($parameters);
        $signature = $this->signature($query);
        return $this->curlTool->binanceSendRequest($method, "${path}?${query}&signature=${signature}",$this->apiKey,$this->baseUrl);
    }


    private function buildQuery(array $params)
    {
        $query_array = array();
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $query_array = array_merge($query_array, array_map(function ($v) use ($key) {
                    return urlencode($key) . '=' . urlencode($v);
                }, $value));
            } else {
                $query_array[] = urlencode($key) . '=' . urlencode($value);
            }
        }
        return implode('&', $query_array);
    }
}
