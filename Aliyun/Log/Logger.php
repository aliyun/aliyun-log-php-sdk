<?php
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */

class Aliyun_Log_Logger{

    protected $client;

    protected $project;

    protected $logstore;

    public function __construct($client, $project, $logstore)
    {
        $this ->client = $client;
        $this->logstore=$logstore;
        $this->project=$project;
    }

    public function log($logLevel, $logMessage, $topic){
        if(!Aliyun_Log_Models_logLevel_LogLevel::isValidValue($logLevel)){
            throw new Exception('logLevel value is invalid!');
        }
        $ip = $this->getLocalIp();
        $contents = array( // key-value pair
            'time'=>date('m/d/Y h:i:s a', time()),
            'message'=> $logMessage,
            'loglevel'=> $logLevel
        );
        try {
            $logItem = new Aliyun_Log_Models_LogItem();
            $logItem->setTime(time());
            $logItem->setContents($contents);
            $logitems = array($logItem);
            $request = new Aliyun_Log_Models_PutLogsRequest($this->project, $this->logstore,
                $topic, $ip, $logitems);
            $response = $this->client->putLogs($request);
            print($response ->getRequestId());
        } catch (Aliyun_Log_Exception $ex) {
            logVarDump($ex);
        } catch (Exception $ex) {
            logVarDump($ex);
        }
    }

    private function getLocalIp(){
        $local_ip = getHostByName(php_uname('n'));
        if(strlen($local_ip) == 0){
            $local_ip = getHostByName(getHostName());
        }
        return $local_ip;
    }
}