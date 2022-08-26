<?php

/**
 * Created by Jacky.
 * Project Name: LotteryGameDataSyncer.
 * Date Time: 2021/4/21 下午 06:01.
 */

class Help
{
    private static $instance;

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function mixArray($array): string
    {
        $data = "";
        foreach ($array as $key => $value) {
            if (is_object($value) || is_array($value)) {
                foreach ($value as $key2 => $value2) {
                    if (is_object($value2) || is_array($value2)) {
                        foreach ($value2 as $key3 => $value3) {
                            if (is_object($value3) || is_array($value3)) {
                                foreach ($value3 as $key4 => $value4) {
                                    $data .= $key . "|" . $key2 . "|" . $key3 . "|" . $key4 . "\t=>\t" . $value4 . "\n";
                                }
                            } else {
                                $data .= $key . "|" . $key2 . "|" . $key3 . "\t=>\t" . $value3 . "\n";
                            }
                        }
                    } else {
                        $data .= $key . "|" . $key2 . "\t=>\t" . $value2 . "\n";
                    }

                }
            } else {
                $data .= $key . "\t=>\t" . $value . "\n";
            }
        }
        return $data;
    }

    public function getUid(): string
    {
        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $uuid = substr($charid, 0, 8)
            .substr($charid, 8, 4)
            .substr($charid,12, 4)
            .substr($charid,16, 4)
            .substr($charid,20,12);
        return $uuid;
    }

    /**
     * 取得主機RAM使用狀況
     * @return string
     */
    public function get_server_memory_usage()
    {

        $free = shell_exec('free');
        $free = (string)trim($free);
        $free_arr = explode("\n", $free);
        $mem = explode(" ", $free_arr[1]);
        $mem = array_filter($mem);
        $mem = array_merge($mem);
        $memory_usage = $mem[2]/$mem[1]*100;

        return round($memory_usage,2)."%";
    }

    /**
     * 取得主機CPU使用狀況
     * @return string
     */
    public function get_server_cpu_usage()
    {
        $stat1 = file('/proc/stat');
        sleep(1);
        $stat2 = file('/proc/stat');
        $info1 = explode(" ", preg_replace("!cpu +!", "", $stat1[0]));
        $info2 = explode(" ", preg_replace("!cpu +!", "", $stat2[0]));
        $dif = array();
        $dif['user'] = $info2[0] - $info1[0];
        $dif['nice'] = $info2[1] - $info1[1];
        $dif['sys'] = $info2[2] - $info1[2];
        $dif['idle'] = $info2[3] - $info1[3];
        $total = array_sum($dif);
        $cpu = array();
        foreach($dif as $x=>$y) $cpu[$x] = round($y / $total * 100, 1);
        $load = (100 - $cpu['idle']);

        return $load."%";
    }

    /**
     * 將Array格式整理
     * @param array $ogArray
     * @return array
     */
    public function reArrayFromKey(array $ogArray)
    {
        $newArray = [];
        $keyName = ['orderId', 'orderCommission', 'orderCommissionAsset', 'orderProfit'];
        foreach ($ogArray as $key => $row){
            $newArray[$keyName[$key]] = $row;
        }

        if(empty($newArray)){
            $newArray = $ogArray;
        }

        return $newArray;
    }

    /**
     * @param $orderMsg
     * @return array|string|string[]
     */
    public function orderLogNotifyFormat($orderMsg)
    {
        $orderMsg = str_replace('eventType','事件類型',$orderMsg);
        $orderMsg = str_replace('transactionTime','搓合時間',$orderMsg);
        $orderMsg = str_replace('eventTime','事件時間',$orderMsg);
        $orderMsg = str_replace('order|symbol','交易對',$orderMsg);
        $orderMsg = str_replace('order|clientOrderId','客戶端ID',$orderMsg);
        $orderMsg = str_replace('order|orderSide','訂單方向',$orderMsg);
        $orderMsg = str_replace('order|orderType','訂單類型',$orderMsg);
        $orderMsg = str_replace('order|timeInForce','有效方式',$orderMsg);
        $orderMsg = str_replace('order|originalQuantity','訂單原始數量',$orderMsg);
        $orderMsg = str_replace('order|originalPrice','訂單原始價格',$orderMsg);
        $orderMsg = str_replace('order|averagePrice','訂單平均價格',$orderMsg);
        $orderMsg = str_replace('order|executionType','事件執行類型',$orderMsg);
        $orderMsg = str_replace('order|orderStatus','訂單當前狀態',$orderMsg);
        $orderMsg = str_replace('order|orderId','訂單ID',$orderMsg);
        $orderMsg = str_replace('order|lastFilledQuantity','訂單最後成交量',$orderMsg);
        $orderMsg = str_replace('order|lastFilledPrice','訂單最後成交價格',$orderMsg);
        $orderMsg = str_replace('order|commissionAmount','訂單手續費量',$orderMsg);
        $orderMsg = str_replace('order|commissionAsset','訂單手續費單位',$orderMsg);
        $orderMsg = str_replace('order|orderTradeTime','成交時間',$orderMsg);
        $orderMsg = str_replace('order|tradeId','成交ID',$orderMsg);
        $orderMsg = str_replace('order|bidsNotional','買單淨值',$orderMsg);
        $orderMsg = str_replace('order|asksNotional','賣單淨值',$orderMsg);
        $orderMsg = str_replace('order|isMakerTrade','是否為市價單',$orderMsg);
        $orderMsg = str_replace('order|isReduceOnly','是否只減倉',$orderMsg);
        $orderMsg = str_replace('order|stopPriceWorkingType','止損動作類型',$orderMsg);
        $orderMsg = str_replace('order|stopPrice','止損價',$orderMsg);
        $orderMsg = str_replace('order|originalOrderType','原始訂單類型',$orderMsg);
        $orderMsg = str_replace('order|positionSide','持倉方向',$orderMsg);
        $orderMsg = str_replace('order|isCloseAll','是否全部平倉',$orderMsg);
        $orderMsg = str_replace('order|realisedProfit','已實現獲利',$orderMsg);

        return $orderMsg;
    }
}