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
class Aliyun_Log_Models_LogBatch{

    private $logItems = [];

    private $arraySize;

    private $logger;

    private $sem_id;

    private $shm_id;

    private $topic;

    private $waitTime;

    private $previousLogTime;

    public function __construct(Aliyun_Log_Logger $logger, $topic, $cacheLogCount = null, $cacheLogWaitTime = null)
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

        $time_stampe = time();
        $MEMSIZE    =   5120;
        $SEMKEY     =   $time_stampe;
        $SHMKEY     =   $time_stampe+2233;

        $this->sem_id = sem_get($SEMKEY, 10);
        $this->shm_id = shm_attach($SHMKEY, $MEMSIZE);
        if(shm_has_var($this->shm_id, 1)){
            shm_remove_var($this->shm_id, 1);
        }
        shm_put_var($this->shm_id, 1, $this->logItems);

    }

    public function log($logMessage, $logLevel){
        $prevoisCallTime = $this->previousLogTime;
        if(NULL ===  $prevoisCallTime){
            $prevoisCallTime = 0;
        }
        $this->previousLogTime = time();
        $contents = array( // key-value pair
            'time'=>date('m/d/Y h:i:s a', time()),
            'message'=> $logMessage,
            'loglevel'=> $logLevel
        );
        $logItem = new Aliyun_Log_Models_LogItem();
        $logItem->setTime(time());
        $logItem->setContents($contents);
        printf($logMessage.'<br>');
        if(shm_has_var($this->shm_id, 1)){
            $logItems = shm_get_var($this->shm_id, 1);
            array_push($logItems, $logItem);

            if((sizeof($logItems) == $this->arraySize || $this->previousLogTime - $prevoisCallTime > 5000)
                && $prevoisCallTime > 0){
                $this->logger->logBatch($logItems, $this->topic);
                $logItems = [];
            }

            shm_remove_var($this->shm_id, 1);
            shm_put_var($this->shm_id, 1, $logItems);
            $this->logItems = $logItems;
        }
    }

    public function logFlush(){
        if(sizeof($this->logItems) > 0){
            $this->logger->logBatch($this->logItems, $this->topic);
            $this->logItems = [];
        }
        shm_remove_var($this->shm_id, 1);
    }

    function __destruct() {
        if(sizeof($this->logItems) > 0){
            $this->logger->logBatch($this->logItems, $this->topic);
        }

        sem_remove($this->sem_id);
        shm_remove($this->shm_id);
    }
}