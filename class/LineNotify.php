<?php

/**
 * Created by Jacky.
 * Project Name: LotteryGameDataSyncer.
 * Date Time: 2021/4/21 下午 06:01.
 */

class LineNotify
{
    private string $token = "";

    public function setToken(string $token): bool
    {
        $this->token = $token;

        return true;
    }

    public function doLineNotify(string $msg): string
    {

        $url = "https://notify-api.line.me/api/notify";

        $payload['message'] = $msg;

        $curl = new CurlTool();

        $header = array('Authorization:Bearer ' . $this->token);
        for ($try = 0; $try < 10; $try++) {
            $results = $curl->doPost($url, $header, $payload);
            if (!is_null($results->status) && !is_null($results->message)) {
                if ($results->message == "ok" || $results->status == 200) {
                    $try = 10;
                    return true;
                }
            } else {
                var_dump($results);
            }
        }


        return false;
    }
}
