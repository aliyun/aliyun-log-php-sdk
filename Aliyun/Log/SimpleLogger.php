<?php
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */

/**
 * Class Aliyun_Log_SimpleLogger
 * simple logger for submit log message
 */
class Aliyun_Log_SimpleLogger{

    private $logItems = [];

    private $maxCacheLog;

    private $topic;

    private $maxWaitTime;

    private $previousLogTime;

    private $maxCacheBytes;

    private $cacheBytes;

    private $client;

    private $project;

    private $logstore;

    /**
     * Aliyun_Log_Models_LogBatch constructor.
     * @param $client log client
     * @param $project the corresponding project
     * @param $logstore the logstore
     * @param $topic
     * @param null $maxCacheLog max log items limitation, by default it's 100
     * @param null $maxWaitTime max thread waiting time, bydefault it's 5 seconds
     */
    public function __construct($client, $project, $logstore, $topic, $maxCacheLog = null, $maxWaitTime = null, $maxCacheBytes = null)
    {
        if(NULL === $maxCacheLog || !is_integer($maxCacheLog)){
            $this->maxCacheLog = 100;
        }else{
            $this->maxCacheLog = $maxCacheLog;
        }

        if(NULL === $maxCacheBytes || !is_integer($maxCacheBytes)){
            $this->maxCacheBytes = 256 * 1024;
        }else{
            $this->maxCacheBytes = $maxCacheBytes;
        }

        if(NULL === $maxWaitTime || !is_integer($maxWaitTime)){
            $this->maxWaitTime = 5;
        }else{
            $this->maxWaitTime = $maxWaitTime;
        }
        if($client == null || $project == null || $logstore == null){
            throw new Exception('the input parameter is invalid! create SimpleLogger failed!');
        }
        $this->client = $client;
        $this->project = $project;
        $this->logstore = $logstore;
        $this->topic = $topic;
        $this->previousLogTime = time();
        $this->cacheBytes = 0;
    }

    private function logItem($cur_time, $logItem){
        array_push($this->logItems, $logItem);
        if ($cur_time - $this->previousLogTime >= $this->maxWaitTime || sizeof($this->logItems) >= $this->maxCacheLog
            || $this->cacheBytes >= $this->maxCacheBytes)
        {
            $this->logBatch($this->logItems, $this->topic);
            $this->logItems = [];
            $this->previousLogTime = time();
            $this->cacheBytes = 0;
        }
    }

    private function logSingleMessage(Aliyun_Log_Models_LogLevel_LogLevel $logLevel, $logMessage){
        if(is_array($logMessage)){
            throw new Exception('array is not supported in this function, please use logArrayMessage!');
        }
        $cur_time = time();
        $contents = array( // key-value pair
            'time'=>date('m/d/Y h:i:s a', $cur_time),
            'loglevel'=> Aliyun_Log_Models_LogLevel_LogLevel::getLevelStr($logLevel),
            'msg'=>$logMessage
        );
        $this->cacheBytes += strlen($logMessage) + 32;
        $logItem = new Aliyun_Log_Models_LogItem();
        $logItem->setTime($cur_time);
        $logItem->setContents($contents);
        $this->logItem($cur_time, $logItem);
    }

    private function logArrayMessage(Aliyun_Log_Models_LogLevel_LogLevel $logLevel, $logMessage){
        if(!is_array($logMessage)){
            throw new Exception('input message is not array, please use logSingleMessage!');
        }
        $cur_time = time();
        $contents = array( // key-value pair
            'time'=>date('m/d/Y h:i:s a', $cur_time)
        );
        $contents['logLevel'] = Aliyun_Log_Models_LogLevel_LogLevel::getLevelStr($logLevel);
        foreach ($logMessage as $key => $value)
        {
            $contents[$key] = $value;
            $this->cacheBytes += strlen($key) + strlen($value);
        }
        $this->cacheBytes += 32;
        $logItem = new Aliyun_Log_Models_LogItem();
        $logItem->setTime($cur_time);
        $logItem->setContents($contents);
        $this->logItem($cur_time, $logItem);
    }

    public function info( $logMessage){
        $logLevel = Aliyun_Log_Models_LogLevel_LogLevel::getLevelInfo();
        $this->logSingleMessage($logLevel, $logMessage);
    }
    public function debug($logMessage){
        $logLevel = Aliyun_Log_Models_LogLevel_LogLevel::getLevelDebug();
        $this->logSingleMessage($logLevel, $logMessage);
    }
    public function warn($logMessage){
        $logLevel = Aliyun_Log_Models_LogLevel_LogLevel::getLevelWarn();
        $this->logSingleMessage($logLevel, $logMessage);
    }
    public function error($logMessage){
        $logLevel = Aliyun_Log_Models_LogLevel_LogLevel::getLevelError();
        $this->logSingleMessage($logLevel, $logMessage);
    }

    public function infoArray($logMessage){
        $logLevel = Aliyun_Log_Models_LogLevel_LogLevel::getLevelInfo();
        $this->logArrayMessage($logLevel, $logMessage);
    }
    public function debugArray($logMessage){
        $logLevel = Aliyun_Log_Models_LogLevel_LogLevel::getLevelDebug();
        $this->logArrayMessage($logLevel, $logMessage);
    }
    public function warnArray($logMessage){
        $logLevel = Aliyun_Log_Models_LogLevel_LogLevel::getLevelWarn();
        $this->logArrayMessage($logLevel, $logMessage);
    }
    public function errorArray( $logMessage){
        $logLevel = Aliyun_Log_Models_LogLevel_LogLevel::getLevelError();
        $this->logArrayMessage($logLevel, $logMessage);
    }

    private function getLocalIp(){
        $local_ip = getHostByName(php_uname('n'));
        if(strlen($local_ip) == 0){
            $local_ip = getHostByName(getHostName());
        }
        return $local_ip;
    }

    private function logBatch($logItems, $topic){
        $ip = $this->getLocalIp();
        $request = new Aliyun_Log_Models_PutLogsRequest($this->project, $this->logstore,
            $topic, $ip, $logItems);
        $error_exception = NULL;
        for($i = 0 ;  $i < 3 ; $i++)
        {
            try{
                $response = $this->client->putLogs($request);
                return;
            } catch (Aliyun_Log_Exception $ex) {
                $error_exception = $ex;
            } catch (Exception $ex) {
                var_dump($ex);
                $error_exception = $ex;
            }
        }
        if ($error_exception != NULL)
        {
            var_dump($error_exception);
        }
    }

    /**
     * manually flush all cached log to log server
     */
    public function logFlush(){
        if(sizeof($this->logItems) > 0){
            $this->logBatch($this->logItems, $this->topic);
            $this->logItems = [];
            $this->previousLogTime= time();
            $this->cacheBytes = 0;
        }
    }

    function __destruct() {
        if(sizeof($this->logItems) > 0){
            $this->logBatch($this->logItems, $this->topic);
        }
    }
}
