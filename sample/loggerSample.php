<?php
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */

require_once realpath(dirname(__FILE__) . '/../Log_Autoload.php');

/**
 * List all shards in current log configuration
 * @param Aliyun_Log_Client $client
 * @param $project
 * @param $logstore
 */
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

/**
 * sumit log by client directly
 * @param Aliyun_Log_Client $client
 * @param $project
 * @param $logstore
 */
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

/**
 * query log by client directly
 * @param Aliyun_Log_Client $client
 * @param $project
 * @param $logstore
 */
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
$logstore = 'test';
$token = "";


 // create a log client
$client = new Aliyun_Log_Client($endpoint, $accessKeyId, $accessKey,$token);
listShard($client,$project,$logstore);

// create a logger instance by calling factory method
$logger = Aliyun_Log_LoggerFactory::getLogger($client, $project, $logstore);
$logMap = array(
    'message' => 'tet',
    'haha' => 'hehe'
);

// submit single string message by logger
$logger->logSingleMessage(Aliyun_Log_Models_LogLevel_LogLevel::getLevelInfo(),'test INFO LOG');

//create same logger instance by calling factory method with same parameters
$logger2 = Aliyun_Log_LoggerFactory::getLogger($client, $project, $logstore);


$logger2->logSingleMessage(Aliyun_Log_Models_LogLevel_LogLevel::getLevelInfo(),'test INFO LOG2222222', 'MainFlow');

// submit single array message by logger
$logger->logArrayMessage(Aliyun_Log_Models_LogLevel_LogLevel::getLevelInfo(),$logMap, 'MainFlow');

//$logger->log('test', 'something wrong with the inner info', 'MainFlow');

//create different logger instance by calling factory method with topic parameter defined
$batchLogger = Aliyun_Log_LoggerFactory::getLogger($client, $project, $logstore,'helloworld');

// batch submit single string message, with default cache size 100
for($i = 1; $i <= 129; $i++){
    $batchLogger->logSingleMessage(Aliyun_Log_Models_LogLevel_LogLevel::getLevelInfo(),'something wrong with the inner info '.$i);
}

// manually flush log message
$batchLogger->logFlush();


getLogs($client,$project,$logstore);

$logger2->info('test log message 000 info');
$logger2->warn('test log message 000 warn');
$logger2->error('test log message 000 error');
$logger2->debug('test log message 000 debug');

$logMap['level'] = 'info';
$logger2->infoArray($logMap);
$logMap['level'] = 'debug';
$logger2->debugArray($logMap);
$logMap['level'] = 'warn';
$logger2->warnArray($logMap);
$logMap['level'] = 'error';
$logger2->errorArray($logMap);

$logger2->logFlush();

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
