<?php

/**
 * Created by Jacky.
 * Project Name: LotteryGameDataSyncer.
 * Date Time: 2021/4/21 下午 06:01.
 */

class CurlTool
{
    /**
     * URL 路徑 HEADER 內容
     * @param string $url
     * @param array $header
     * @param array $payload
     * @return false|mixed
     */
    public function doPost(string $url,array $header,array $payload)
    {
        try {
            set_time_limit(0);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 1);

            $results = curl_exec($ch);

            // Header 分割
            $headerSize = curl_getinfo( $ch , CURLINFO_HEADER_SIZE );
            $headerStr = substr( $results , 0 , $headerSize );
            $body = json_decode(substr( $results , $headerSize ));

            // 轉換 Header 成陣列
            $headers = $this->headersToArray( $headerStr );

//        var_dump(intval($headers['X-RateLimit-Remaining']));
//        var_dump($body);
            curl_close($ch);

            return $body;
        }catch (Exception $exception){
            var_dump($exception->getMessage());
            curl_close($ch);
            return false;
        }
    }

    /**
     * @param string $url
     * @param array $header
     * @return object
     */
    public function doGet(string $url,array $header=[]): object
    {
        set_time_limit(0);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);


        for($i=0;$i<10;$i++){
            $results = json_decode(curl_exec($ch));
            if(is_object($results)){
                break;
            }
        }
        curl_close($ch);

        return $results;

    }

    /**
     * @param $str
     * @return array
     */
    function headersToArray( $str ): array
    {
        $headers = array();
        $headersTmpArray = explode( "\r\n" , $str );
        for ( $i = 0 ; $i < count( $headersTmpArray ) ; ++$i )
        {
            // we dont care about the two \r\n lines at the end of the headers
            if ( strlen( $headersTmpArray[$i] ) > 0 )
            {
                // the headers start with HTTP status codes, which do not contain a colon so we can filter them out too
                if ( strpos( $headersTmpArray[$i] , ":" ) )
                {
                    $headerName = substr( $headersTmpArray[$i] , 0 , strpos( $headersTmpArray[$i] , ":" ) );
                    $headerValue = substr( $headersTmpArray[$i] , strpos( $headersTmpArray[$i] , ":" )+1 );
                    $headers[$headerName] = $headerValue;
                }
            }
        }
        return $headers;
    }

    function binanceSendRequest($method, $path, $KEY,$BASE_URL)
    {
        set_time_limit(0);

        $url = "${BASE_URL}${path}";

//        echo "requested URL: ". PHP_EOL;
//        echo $url. PHP_EOL;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-MBX-APIKEY:'.$KEY));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, $method == "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $execResult = curl_exec($ch);
        $response = curl_getinfo($ch);

        // if you wish to print the response headers
        // echo print_r($response);
var_dump($response);
        curl_close ($ch);
        return json_decode($execResult, true);
    }
}
