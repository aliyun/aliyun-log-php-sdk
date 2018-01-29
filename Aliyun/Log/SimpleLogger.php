<?php
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */

/**
 * Class Aliyun_Log_Models_LogBatch
 * in some cases the http port is quite limited, so user could config a batch logger,
 * which will cache some log and send to server in bulk
 */
class Aliyun_Log_SimpleLogger extends Aliyun_Log_Logger {

    private $logItems = [];

    private $arraySize;

    private $logger;

    private $topic;

    private $waitTime;

    private $previousLogTime;

    /**
     * Aliyun_Log_Models_LogBatch constructor.
     * @param Aliyun_Log_Logger $logger
     * @param $topic
     * @param null $cacheLogCount max log items limitation, by default it's 100
     * @param null $cacheLogWaitTime max thread waiting time, bydefault it's 5 seconds
     */
    protected function __construct(Aliyun_Log_Logger $logger, $topic, $cacheLogCount = null, $cacheLogWaitTime = null)
    {
        if(NULL === $cacheLogCount || !is_integer($cacheLogCount)){
            $this->arraySize = 10;
        }else{
            $this->arraySize = $cacheLogCount;
        }

        if(NULL === $cacheLogWaitTime || !is_integer($cacheLogWaitTime)){
            $this->waitTime = 5;
        }else{
            $this->waitTime = $cacheLogWaitTime;
        }

        $this->logger = $logger;
        $this->topic = $topic;
    }

    /**
     * log expected message with proper level
     * @param $logMessage
     * @param $logLevel
     * @param $topic should be null
     */
    public function log(Aliyun_Log_Models_LogLevel_LogLevel $logLevel,$logMessage, $topic = null){
        $previousCallTime = $this->previousLogTime;
        if(null ===  $previousCallTime){
            $previousCallTime = 0;
        }
        $this->previousLogTime = time();
        if(is_array($logMessage)){
            $logItemTemps = array();
            foreach ($logMessage as &$logElement){
                $contents = array( // key-value pair
                    'time'=>date('m/d/Y h:i:s a', time()),
                    'message'=> $logElement,
                    'loglevel'=> Aliyun_Log_Models_LogLevel_LogLevel::getLevelStr($logLevel)
                );
                $logItem = new Aliyun_Log_Models_LogItem();
                $logItem->setTime(time());
                $logItem->setContents($contents);
                array_push($logItemTemps, $logItem);
            }
            $this->logger->logBatch($logItemTemps, $this->topic);
        }else{
            $logItems = $this->logItems;
            $contents = array( // key-value pair
                'time'=>date('m/d/Y h:i:s a', time()),
                'message'=> $logMessage,
                'loglevel'=> Aliyun_Log_Models_LogLevel_LogLevel::getLevelStr($logLevel)
            );
            $logItem = new Aliyun_Log_Models_LogItem();
            $logItem->setTime(time());
            $logItem->setContents($contents);

            array_push($logItems, $logItem);

            if((sizeof($logItems) == $this->arraySize
                    || $this->previousLogTime - $previousCallTime > 5000)
                    && $previousCallTime > 0){
                $this->logger->logBatch($logItems, $this->topic);
                $logItems = [];
            }
            $this->logItems = $logItems;
        }
    }

    /**
     * manually flush all cached log to log server
     */
    public function logFlush(){
        if(sizeof($this->logItems) > 0){
            $this->logger->logBatch($this->logItems, $this->topic);
            $this->logItems = [];
        }
    }

    function __destruct() {
        if(sizeof($this->logItems) > 0){
            $this->logger->logBatch($this->logItems, $this->topic);
        }
    }
}