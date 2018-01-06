<?php
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */

class Aliyun_Log_Models_LogBatch{

    private $logItems = [];

    private $arraySize;

    private $logger;

    private $sem_id;

    private $shm_id;

    public function __construct($maxLogSize, Aliyun_Log_Logger $logger, $sem_id = null, $shm_id = null)
    {
        $this->arraySize = $maxLogSize;
        if(!is_integer($maxLogSize)){
            $this->arraySize = 5;
        }
        $this->logger = $logger;
        if($sem_id == null || $shm_id == null){
            $MEMSIZE    =   10240;
            $SEMKEY     =   22;
            $SHMKEY     =   33;

            $this->sem_id = sem_get($SEMKEY, 1);
            sem_acquire($this->sem_id);
            $this->shm_id =   shm_attach($SHMKEY, $MEMSIZE);
            if(shm_has_var($this->shm_id, 1)){
                shm_remove_var($this->shm_id, 1);
            }
            shm_put_var($this->shm_id, 1, $this->logItems);
        }
    }

    public function log($logMessage, $logLevel){
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

            if(sizeof($logItems) == $this->arraySize){
                $this->logger->logBatch($logItems, 'MainFlow');
                $logItems = [];
            }

            shm_remove_var($this->shm_id, 1);
            shm_put_var($this->shm_id, 1, $logItems);
            $this->logItems = $logItems;
        }
    }

    public function shareSem($shm_id){

        if(shm_has_var($shm_id, 1)){
            $tmp = shm_get_var($shm_id, 1);
            shm_put_var($shm_id, 1, "*,".$tmp);
        }else{
            shm_put_var($shm_id, 1, "Variable 1");
        }

        $result = shm_get_var($shm_id, 1);
        echo $result."<br>";
    }

    function __destruct() {
        if(sizeof($this->logItems) > 0){
            $this->logger->logBatch($this->logItems, 'MainFlow');
        }

        sem_remove($this->sem_id);
        shm_remove ($this->shm_id);
    }
}