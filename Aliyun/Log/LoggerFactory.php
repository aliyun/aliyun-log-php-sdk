<?php
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */

/**
 * Class Aliyun_Log_LoggerFactory
 */
class Aliyun_Log_LoggerFactory{

    private static $loggerMap = array();

    public static function getLogger($client, $project, $logstore, $topic = null){
        if($project === null || $project == ''){
            throw new Exception('project name is blank!');
        }
        if($logstore === null || $logstore == ''){
            throw new Exception('logstore name is blank!');
        }
        if($topic === null){
            $topic = '';
        }
        $loggerKey = $project.'#'.$logstore.'#'.$topic;
        if (!array_key_exists($loggerKey, static::$loggerMap))
        {
            $instanceSimpleLogger = new Aliyun_Log_SimpleLogger($client,$project,$logstore,$topic);
            static::$loggerMap[$loggerKey] = $instanceSimpleLogger;
        }
        return static::$loggerMap[$loggerKey];
    }

    protected function __construct()
    {

    }

    private function __clone()
    {}

    function __destruct() {
        if(static::$loggerMap != null){
            foreach (static::$loggerMap as $innerLogger){
                $innerLogger->logFlush();
            }
        }
    }
}
