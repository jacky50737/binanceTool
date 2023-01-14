<?php

/**
 * 開發者 User
 * 創建於 2022/7/4
 * 使用   PhpStorm
 * 專案名稱binanceTool
 */

class BinanceTool
{
    private static $instance;
    private string $apiSecret;
    private string $apiKey;
    private string $baseUrl = "https://fapi.binance.com/";
    private CurlTool $curlTool;

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __construct()
    {
        $this->curlTool = CurlTool::getInstance();
    }

    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    public function setApiSecret(string $apiSecret): void
    {
        $this->apiSecret = $apiSecret;
    }

    public function checkKeySecretLen(): bool
    {
        if(strlen($this->apiKey) != 64){
            return false;
        }
        if(strlen($this->apiSecret) != 64){
            return false;
        }
        return true;
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
                    case 'BOTH':
                        $orderStatus = '買入';
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
                    case 'BOTH':
                        $orderStatus = '賣出';
                        break;
                }
                break;
        }
        return $orderStatus;
    }

    public function checkBinanceApi(): bool
    {
        $rows = $this->getAccountInfo();
        if(isset($rows['totalMaintMargin'])){
            return true;
        }
        return false;
    }

    public function getAccountInfo()
    {
        return $this->signedRequest('GET', 'fapi/v2/account');
    }

    private function signature($queryString): bool|string
    {
        return hash_hmac('sha256', $queryString, $this->apiSecret);
    }

    private function signedRequest(string $method, string $path, array $parameters = []) {

        $parameters['timestamp'] = round(microtime(true) * 1000);
        $query = $this->buildQuery($parameters);
        $signature = $this->signature($query);
        return $this->curlTool->binanceSendRequest($method, "${path}?${query}&signature=${signature}",$this->apiKey,$this->baseUrl);
    }


    private function buildQuery(array $params): string
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

    /**
     * @param object $tradeMsg
     * @param string $nickName
     * @param array $extend
     * @return array
     */
    public function transactionMessageProcessing(object $tradeMsg, string $nickName, array $extend = []): array
    {
        $msg = [];
        $logData = [];
        if (isset($tradeMsg->eventType)) {
            switch ($tradeMsg->eventType) {
                case "ORDER_TRADE_UPDATE":
                    switch ($tradeMsg->order->orderStatus) {
                        case 'FILLED':
                            $order = $tradeMsg->order;
                            $orderStatus = $this->transferStockStatus($order->orderSide, $order->positionSide);
                            $notifyString = "\n帳戶名稱：" . $nickName;
                            $notifyString .= "\n幣種：" . $order->symbol;
                            $notifyString .= "\n狀態：" . $orderStatus;
                            $notifyString .= "\n成交均價：" . $order->averagePrice;
                            $notifyString .= "\n成交數量：" . $order->originalQuantity;
                            $notifyString .= "\n手續費(" . $order->commissionAsset . ")：" . $extend['totalCommission'];
                            $notifyString .= "\n實現利潤：" . $extend['totalProfit'];
                            if($order->commissionAsset == "USDT"){
                                $notifyString .= "\n收益率：" . round($extend['totalProfit'] / ($order->averagePrice * $order->originalQuantity) * 100,2) . "%";
                            }


                            //開/盈/虧表情功能
                            if(floatval($extend['totalProfit'])>0){
                                $notifyString = "\n😆😆😆" . $notifyString;
                            }elseif (floatval($extend['totalProfit'])<0){
                                $notifyString = "\n😢😢😢" . $notifyString;
                            }elseif (floatval($extend['totalProfit']) == 0){
                                $notifyString = "\n😎😎😎" . $notifyString;
                            }

                            $msg = $notifyString;
                            $logData = $order;
                            $code = '200';
                            break;
                        case 'NEW':
                        case 'PARTIALLY_FILLED':
                        case 'CANCELED':
                        case 'EXPIRED':
                        case 'NEW_INSURANCE':
                        case 'NEW_ADL':
                            $order = $tradeMsg->order;
                            $logData = $order;
                            $code = '201';
                            $msg = "資料新增完成";
                            break;
                        default:
                            $code = '400';
                    }
                break;

                default:
                    $code = '400';
                    $msg = '無法辨識的狀態';
            }
        }else{
            $code = '400';
            $msg = '無法辨識的輸入';
        }

        return ['code'=>$code,'msg'=>$msg,'data'=>$logData];
    }

    public function calculateCommissionAndProfit($orderData)
    {
        $totalCommission = 0;
        $totalProfit = 0;

        foreach ($orderData as $data) {
            if (isset($data['orderCommission']) and !empty($data['orderCommission'])) {
                $totalCommission = $totalCommission + floatval($data['orderCommission']);
            }

            if (isset($data['orderProfit']) and !empty($data['orderProfit'])) {
                $totalProfit = $totalProfit + floatval($data['orderProfit']);
            }
        }

        return ['totalCommission' => number_format($totalCommission,4), 'totalProfit' => number_format($totalProfit,4)];
    }
}
