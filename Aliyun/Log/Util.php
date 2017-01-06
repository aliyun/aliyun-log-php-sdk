<?php

/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */

class Aliyun_Log_Util {

    /**
     * Get the local machine ip address.
     *
     * @return string
     */
    public static function getLocalIp() {
        try { // if exec can be used
            $preg = "/\A((([0-9]?[0-9])|(1[0-9]{2})|(2[0-4][0-9])|(25[0-5]))\.){3}(([0-9]?[0-9])|(1[0-9]{2})|(2[0-4][0-9])|(25[0-5]))\Z/";
            
            if ( PATH_SEPARATOR==':' ) { // linux
                exec("ifconfig", $out, $stats);
                if (!empty($out)) {
                    if (isset($out[1]) && strstr($out[1], 'addr:')) {
                        $tmpArray = explode(":", $out[1]);
                        $tmpIp = explode(" ", $tmpArray[1]);
                        if (preg_match($preg, trim($tmpIp[0])))
                            return trim($tmpIp[0]);
                    }
                }
            } else { // windows PATH_SEPARATOR==';'
                exec("ipconfig", $out, $stats);
                if (!empty($out)) {
                    foreach ($out AS $row) {
                        if (strstr($row, "IP") && strstr($row, ":") && !strstr($row, "IPv6")) {
                            $tmpIp = explode(":", $row);
                            if (preg_match($preg, trim($tmpIp[1])))
                                return trim($tmpIp[1]);
                        }
                    }
                }
            }
        } catch ( Exception $ex ){
        }
        
        if (isset($_ENV["HOSTNAME"]))
            $MachineName = $_ENV["HOSTNAME"];
        else if (isset($_ENV["COMPUTERNAME"]))
            $MachineName = $_ENV["COMPUTERNAME"];
        else
            $MachineName = "";
        if ($MachineName!="")
            return $MachineName;
        
        return '127.0.0.1';
    }
    
    /**
     * If $gonten is raw IP address, return true.
     *
     * @return bool
     */
    public static function isIp($gonten){
        $ip = explode(".", $gonten);
        for($i=0;$i<count($ip);++$i)
            if($ip[$i]>255)
                return 0;
        return preg_match("/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/", $gonten);
    }
    
    /**
     * Calculate string $value MD5.
     *
     * @return string
     */
    public static function calMD5($value) {
        return strtoupper(md5($value));
    }
    
    /**
     * Calculate string $content hmacSHA1 with secret key $key.
     *
     * @return string
     */
    public static function hmacSHA1($content, $key) {
        $signature = hash_hmac("sha1", $content, $key, true);
        return base64_encode($signature);
    }
    
    /**
     * Change $logGroup to bytes.
     *
     * @return string
     */
    public static function toBytes($logGroup) {
        $mem = fopen("php://memory", "rwb");
        $logGroup->write($mem);
        rewind($mem);
        $bytes="";
      
        if(feof($mem)===false){
            $bytes = fread($mem, 10*1024*1024);
        }
        fclose($mem);

        return $bytes;
        
         //$mem = fopen("php://memory", "wb");
      /*   $fiveMBs = 5*1024*1024;
         $mem = fopen("php://temp/maxmemory:$fiveMBs", 'rwb');
         $logGroup->write($mem);
        // rewind($mem);
        
        // fclose($mem);
         //d://logGroup.pdoc
        // $mem = fopen("php://memory", "rb");
        // $mem = fopen("php://temp/maxmemory:$fiveMBs", 'r+');
         $bytes;
         while(!feof($mem))
             $bytes = fread($mem, 10*1024*1024);
         fclose($mem);
         //test
         if($bytes===false)echo "fread fail";
         return $bytes;*/
              
    }


    /**
     * Get url encode.
     *
     * @return string
     */
    public static function urlEncodeValue($value) {
        return urlencode ( $value );
    }
    
    /**
     * Get url encode.
     *
     * @return string
     */
    public static function urlEncode($params) {
        ksort ( $params );
        $url = "";
        $first = true;
        foreach ( $params as $key => $value ) {
            $val = Aliyun_Log_Util::urlEncodeValue ( $value );
            if ($first) {
                $first = false;
                $url = "$key=$val";
            } else
                $url .= "&$key=$val";
        }
        return $url;
    }
    
    /**
     * Get canonicalizedLOGHeaders string as defined.
     *
     * @return string
     */
    public static function canonicalizedLOGHeaders($header) {
        ksort ( $header );
        $content = '';
        $first = true;
        foreach ( $header as $key => $value )
            if (strpos ( $key, "x-log-" ) === 0 || strpos ( $key, "x-acs-" ) === 0) { // x-log- header
            if ($first) {
                $content .= $key . ':' . $value;
                $first = false;
            } else
                $content .= "\n" . $key . ':' . $value;
        }
        return $content;
    }
    
    /**
     * Get canonicalizedResource string as defined.
     *
     * @return string
     */
    public static function canonicalizedResource($resource, $params) {
        if ($params) {
            ksort ( $params );
            $urlString = "";
            $first = true;
            foreach ( $params as $key => $value ) {
                if ($first) {
                    $first = false;
                    $urlString = "$key=$value";
                } else
                    $urlString .= "&$key=$value";
            }
            return $resource . '?' . $urlString;
        }
        return $resource;
    }
    
    /**
     * Get request authorization string as defined.
     *
     * @return string
     */
    public static function getRequestAuthorization($method, $resource, $key,$stsToken, $params, $headers) {
        if (! $key)
            return '';
        $content = $method . "\n";
        if (isset ( $headers ['Content-MD5'] ))
            $content .= $headers ['Content-MD5'];
        $content .= "\n";
        if (isset ( $headers ['Content-Type'] ))
            $content .= $headers ['Content-Type'];
        $content .= "\n";
        $content .= $headers ['Date'] . "\n";
        $content .= Aliyun_Log_Util::canonicalizedLOGHeaders ( $headers ) . "\n";
        $content .= Aliyun_Log_Util::canonicalizedResource ( $resource, $params );
        return Aliyun_Log_Util::hmacSHA1 ( $content, $key );
    }

}

