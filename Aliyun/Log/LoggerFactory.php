<?php
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */

class Aliyun_Log_LoggerFactory extends Aliyun_Log_SimpleLogger{
    protected static $instanceLogger = null;
    protected static $instanceSimpleLogger = null;

    public static function getLogger($client, $project, $logstore){
        if (!isset(static::$instanceLogger)) {
            static::$instanceLogger = new Aliyun_Log_Logger($client, $project, $logstore);
        }
        return static::$instanceLogger;
    }

    public static function getSimpleLogger($client, $project, $logstore, $topic=null){
        if($topic === null){
            $topic = 'MainFlow';
        }
        if (!isset(static::$instanceSimpleLogger)) {
            $logger = new Aliyun_Log_Logger($client, $project, $logstore);
            static::$instanceSimpleLogger = new Aliyun_Log_SimpleLogger($logger,$topic);
        }
        return static::$instanceSimpleLogger;
    }

    protected function __construct()
    {

    }

    private function __clone()
    {}
}