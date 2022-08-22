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

//        $load = sys_getloadavg();
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
    public function reArrayFromKey(array $ogArray){
        $newArray = [];
        foreach ($ogArray as $key => $row){
            if(!is_numeric($key)){
                $newArray[$key] = $row;
            }
        }

        if(empty($newArray)){
            $newArray = $ogArray;
        }

        return $newArray;
     }
}