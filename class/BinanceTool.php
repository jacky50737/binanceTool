<?php
/**
 * 開發者 User
 * 創建於 2022/7/4
 * 使用   PhpStorm
 * 專案名稱binanceTool
 */

class BinanceTool{
    public function transferStockStatus(string $orderSide, string $positionSide): string
    {
        $orderStatus = "異常";
        switch ($orderSide){
            case 'BUY':
                switch ($positionSide){
                    case 'LONG':
                        $orderStatus = '開多';
                        break;
                    case 'SHORT':
                        $orderStatus = '平空';
                        break;
                }
                break;
            case 'SELL':
                switch ($positionSide){
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
}
