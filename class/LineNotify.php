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

        $token = ['gEUyWFxGu74kORojtNpYS8Sscwobo0fTSqCe1l8xLKQ','wY8w8hdxbO8GvNsDPTgEsXY5K2z4FxGZXb7hu3XAjl6'];

        $curl = $this->curl;

        for ($i = 0; $i < 1; $i++) {
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
