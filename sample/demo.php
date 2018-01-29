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

// please update the configuration according your profile
$endpoint = '';
$accessKeyId = '';
$accessKey = '';
$project = '';
$logstore = '';
$token = "";

$client = new Aliyun_Log_Client($endpoint, $accessKeyId, $accessKey,$token);
listShard($client,$project,$logstore);

$logger = Aliyun_Log_LoggerFactory::getLogger($client, $project, $logstore);
$logMap = array(
    'message' => 'tet',
    'haha' => 'hehe'
);

$logger->log(Aliyun_Log_Models_LogLevel_LogLevel::getLevelInfo(),'test INFO LOG', 'MainFlow');

$logger->logArray(Aliyun_Log_Models_LogLevel_LogLevel::getLevelInfo(),$logMap, 'MainFlow');

//$logger->log('test', 'something wrong with the inner info', 'MainFlow');
$batchLogger = Aliyun_Log_LoggerFactory::getSimpleLogger($client, $project, $logstore);

for($i = 1; $i <= 29; $i++){
    $batchLogger->log('something wrong with the inner info '.$i,'info');
}
$batchLogger->logFlush();
getLogs($client,$project,$logstore);
$logger = null;

//try delete the created shipper
/*
$deleteShipper = new Aliyun_Log_Models_DeleteShipperRequest($project);
$deleteShipper->setShipperName('testjsonshipper');
$deleteShipper->setLogStore($logstore);
try{
    $client->deleteShipper($deleteShipper);
}catch (Exception $ex){}

//create shipper with csv storage
$shipper = new Aliyun_Log_Models_CreateShipperRequest($project);
$shipper->setShipperName('testshipper');
$shipper->setTargetType('oss');
$shipper->setLogStore($logstore);

$ossCsvStorage = new Aliyun_Log_Models_OssShipperCsvStorage();
$ossCsvStorage->setColumns(array('__topic__',
    'alarm_count',
    'alarm_message',
    'alarm_type',
    'category',
    'project_name'));
$ossCsvStorage->setDelimiter(',');
$ossCsvStorage->setQuote('"');
$ossCsvStorage->setHeader(false);
$ossCsvStorage->setNullIdentifier('');
$ossCsvStorage->setFormat('csv');

$ossJsonStorage = new Aliyun_Log_Models_OssShipperJsonStorage();
$ossJsonStorage->setFormat('json');

$ossConfig = new Aliyun_Log_Models_OssShipperConfig();
$ossConfig->setOssBucket('sls-test-oss-shipper');
$ossConfig->setOssPrefix('logtailalarm');
$ossConfig->setBufferInterval(300);
$ossConfig->setBufferSize(5);
$ossConfig->setCompressType('none');
$ossConfig->setRoleArn('acs:ram::1654218965343050:role/aliyunlogdefaultrole');
$ossConfig->setStorage($ossCsvStorage);
$ossConfig->setPathFormat('%Y/%m/%d/%H');

$shipper->setTargetConfigration($ossConfig->to_json_object());
try{
    $client->createShipper($shipper);
}catch (Exception $exception){
    var_dump($exception);
}

$getShipperConfig = new Aliyun_Log_Models_GetShipperConfigRequest($project);
$getShipperConfig->setShipperName($shipper->getShipperName());
$getShipperConfig->setLogStore($shipper->getLogStore());
$getconfigResp = $client->getShipperConfig($getShipperConfig);
var_dump($getconfigResp);

$listShipper = new Aliyun_Log_Models_ListShipperRequest($project);
$listShipper->setLogStore($shipper->getLogStore());
$listShpperResp = $client->listShipper($listShipper);
var_dump($listShpperResp);

$updateShipper = new Aliyun_Log_Models_UpdateShipperRequest($project);
$updateShipper->setShipperName('testshipper');
$updateShipper->setTargetType('oss');
$updateShipper->setLogStore($logstore);
$ossConfig->setBufferInterval(599);
$updateShipper->setTargetConfigration($ossConfig->to_json_object());

$updateShipperResp = $client->updateShipper($updateShipper);

$deleteShipper = new Aliyun_Log_Models_DeleteShipperRequest($project);
$deleteShipper->setShipperName($shipper->getShipperName());
$deleteShipper->setLogStore($shipper->getLogStore());

$client->deleteShipper($deleteShipper);

//create shipper with json storage
$shipper->setShipperName('testjsonshipper');
$ossConfig->setStorage($ossJsonStorage);
$shipper->setTargetConfigration($ossConfig->to_json_object());
try{
    //$client->createShipper($shipper);
}catch (Exception $exception){
    var_dump($exception);
}

//create shipper with parquet storage
$shipper->setShipperName('testparquetshipper');
$ossParquetStorage = new Aliyun_Log_Models_OssShipperParquetStorage();
$ossParquetStorage->setFormat('parquet');
$ossParquetStorage->setColumns(array(
    array(
        'name' => '__topic__',
        'type' => 'string'
    ),
    array(
        'name' => 'alarm_count',
        'type' => 'int32'
    ),
    array(
        'name' => 'alarm_message',
        'type' => 'string'
    ),
    array(
        'name' => 'alarm_type',
        'type' => 'string'
    ),
    array(
        'name' => 'is_active',
        'type' => 'boolean'
    ),
    array(
        'name' => 'project_name',
        'type' => 'string'
    ),
));
$ossConfig->setStorage($ossParquetStorage);
$shipper->setTargetConfigration($ossConfig->to_json_object());

try{

    $client->createShipper($shipper);
}catch (Exception $exception){
    var_dump($exception);
}

$getShipperTasks = new Aliyun_Log_Models_GetShipperTasksRequest($project);
$getShipperTasks->setShipperName('testjsonshipper');
$getShipperTasks->setLogStore($logstore);
$getShipperTasks->setStartTime(time()-10000);
$getShipperTasks->setEndTime(time());
$getShipperTasks->setStatusType('');//means all status
$getShipperTasks->setOffset(0);
$getShipperTasks->setSize(5);

$tasks = $client->getShipperTasks($getShipperTasks);
var_dump(json_encode($tasks->getStatistics()));
var_dump(json_encode($tasks->getTasks()));

$taskIdList = array();
for($i=0, $size=count($tasks->getTasks());$i<$size;++$i){
    $taskId = $tasks->getTasks()[$i]['id'];
    array_push($taskIdList, $taskId);
}

$retryShipperTask = new Aliyun_Log_Models_RetryShipperTasksRequest($project);
$retryShipperTask->setShipperName('testjsonshipper');
$retryShipperTask->setLogStore($logstore);
$retryShipperTask->setTaskLists($taskIdList);
$client->retryShipperTasks($retryShipperTask);
*/
