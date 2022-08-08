<?php

/**
 * Created by Jacky.
 * Project Name: LotteryGameDataSyncer.
 * Date Time: 2021/4/21 下午 06:01.
 */

class LineNotify
{
    private static $instance;
    private string $token = "";
    private CurlTool $curl;

    /**
     * @return LineNotify
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct(){
        $this->curl = CurlTool::getInstance();
    }

    public function setToken(string $token): bool
    {
        $this->token = $token;

        return true;
    }

    public function doLineNotify(string $msg): string
    {

        $url = "https://notify-api.line.me/api/notify";

        $payload['message'] = $msg;

        $curl = $this->curl;

        $header = array('Authorization:Bearer ' . $this->token);

        try {
            for ($try = 0; $try < 10; $try++) {
                $results = $curl->doPost($url, $header, $payload);
                if (!is_null($results->status) && !is_null($results->message)) {
                    if ($results->message == "ok" || $results->status == 200) {
                        return true;
                    }
                } else {
                    $this->sendToAdmin(strval($results));
                }
            }
        } catch (Exception $exception) {
            $this->sendToAdmin($exception->getMessage());
        }

        return false;
    }

    /**
     * @param $msg
     * @return void
     */
    public function sendToAdmin($msg)
    {
        $url = "https://notify-api.line.me/api/notify";

        $payload['message'] = strval($msg);

        $token = [
            'gEUyWFxGu74kORojtNpYS8Sscwobo0fTSqCe1l8xLKQ',
            '5RfzuUrQcZRdWwkOyU8bzbZlTflsZD1iqZxBdNzysUe',
            'TinmNlCXo2iZoscvcxsMAT3HcAy7UBHAEWek8ebXjO5',
            'Q6RgZUc5pVaJiwfgMc5Rcy9SwpQuXibxAjNJcaAKAAY',
            'UhGAvOvgeIoLwb5QmkOAzXtXB1Umw7kO0hZtfUTHIMy',
            'oRRJQ04XYFnVnmLbiElEw4VJdb8g1w1k0zzzGSXtGEX',
            'KLIYwoiqwPywQVODSz3gAFqIwzp7kOSxWZsdb9YoqRF',
            'Cyc4DMlbbFtxv219ADCyl2vNvR0LiZGxK0JoBkYleHq',
            'YKmxkDoyuhkI4eQgdTaLdSMN9SCre4M9KYoYCQpmP7I',
            'OYnuNZNhvJdRr0f1LL3oWxGsbpuKp8we7Q65abcnlhu',
            'zhxuQQ1sLK98Awr0S5ELvYZTXFmR3wdxEQ3G2hnWjHj',
            '4p7axS7ykzqFnRNjmXQkigamc8ctuMY8zppRed4JqWH',
            '5Pz5tN28w2lQFIGq0iSjdjkuglBlDUWuRwO2x9EXp4Z',
            'pzF2NInEkAEf4cfey8sBjbTPuNXIXRB2aO21sfRBVzi',
            'wY8w8hdxbO8GvNsDPTgEsXY5K2z4FxGZXb7hu3XAjl6'];

        $curl = $this->curl;

        for ($i = 0; $i < count($token)-1; $i++) {
            $header = array('Authorization:Bearer ' . $token[$i]);
            $results = $curl->doPost($url, $header, $payload);
            if (!is_null($results->status) && !is_null($results->message)) {
                if ($results->message == "ok" || $results->status == 200) {
                    $i = count($token);
                }
            }
        }
    }
}
