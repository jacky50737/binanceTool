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

    function getUid(): string
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
    function get_server_memory_usage()
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
    function get_server_cpu_usage()
    {

        $load = sys_getloadavg();
        return $load[0]."%";

    }
}