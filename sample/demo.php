<?php
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */

require_once realpath(dirname(__FILE__) . '/../Log_Autoload.php');

function listShard(Aliyun_Log_Client $client,$project,$logstore){
    $request = new Aliyun_Log_Models_ListShardsRequest($project,$logstore);
    try
    {
        $response = $client -> listShards($request);
        print("<br>");
        foreach ($response ->getShardIds() as $shardId){
            print($shardId."<br>");
        }

    } catch (Exception $ex) {
        print("exception code: ".$ex -> getErrorCode());
    }
}

function putLogs(Aliyun_Log_Client $client, $project, $logstore) {
    $topic = 'TestTopic';

    $contents = array( // key-value pair
        'TestKey'=>'TestContent',
        'message'=>'test log from '.' at '.date('m/d/Y h:i:s a', time())
    );
    $logItem = new Aliyun_Log_Models_LogItem();
    $logItem->setTime(time());
    $logItem->setContents($contents);
    $logitems = array($logItem);
    $request = new Aliyun_Log_Models_PutLogsRequest($project, $logstore,
        $topic, "", $logitems);

    try {
        $response = $client->putLogs($request);
        print($response ->getRequestId());
    } catch (Aliyun_Log_Exception $ex) {
        logVarDump($ex);
    } catch (Exception $ex) {
        logVarDump($ex);
    }
}

function getLogs(Aliyun_Log_Client $client, $project, $logstore) {
    $topic = 'MainFlow';
    $from = time()-3600;
    $to = time();
    $request = new Aliyun_Log_Models_GetLogsRequest($project, $logstore, $from, $to, $topic, '', 100, 0, False);

    try {
        $response = $client->getLogs($request);
        foreach($response -> getLogs() as $log)
        {
            print $log -> getTime()."\t";
            foreach($log -> getContents() as $key => $value){
                print $key.":".$value."<br>";
            }
            print "\n";
        }

    } catch (Aliyun_Log_Exception $ex) {
        logVarDump($ex);
    } catch (Exception $ex) {
        logVarDump($ex);
    }
}

$endpoint = 'http://cn-shanghai-corp.sls.aliyuncs.com';
$accessKeyId = '';
$accessKey = '';
$project = 'ali-sls-sdk-test';
$logstore = 'sls-test';
$token = "";

$client = new Aliyun_Log_Client($endpoint, $accessKeyId, $accessKey,$token);
listShard($client,$project,$logstore);

$logger = new Aliyun_Log_Logger($client, $project, $logstore);

//$logger->log('test', 'something wrong with the inner info', 'MainFlow');
$batchLogger = new Aliyun_Log_Models_LogBatch( $logger,'MainFlow');

for($i = 1; $i <= 9; $i++){
    $batchLogger->log('something wrong with the inner info '.$i,'info');
}

getLogs($client,$project,$logstore);

