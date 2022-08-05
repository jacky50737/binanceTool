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

    function mixArray($array): string
    {
        $data = "";
        foreach( $array as $key => $value ){
            $data .= $key."\t=>\t".$value."\n";
        }

        return $data;
    }
}