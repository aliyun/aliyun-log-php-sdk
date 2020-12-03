<?php
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */

require_once realpath(dirname(__FILE__) . '/../Log_Autoload.php');



// please update the configuration according your profile
$endpoint = '';
$accessKeyId = '';
$accessKey = '';
$project = '';
$logstore = '';
$token = "";

/**
 * client and logger usage
 */
 // create a log client
$client = new Aliyun_Log_Client($endpoint, $accessKeyId, $accessKey,$token);
listShard($client,$project,$logstore);

// create a logger instance by calling factory method
$logger = Aliyun_Log_LoggerFactory::getLogger($client, $project, $logstore);
$logMap = array(
    'message' => 'tet',
    'haha' => 'hehe'
);

//create same logger instance by calling factory method with same parameters
$anotherLogger = Aliyun_Log_LoggerFactory::getLogger($client, $project, $logstore);

//create different logger instance by calling factory method with topic parameter defined
$batchLogger = Aliyun_Log_LoggerFactory::getLogger($client, $project, $logstore,'helloworld');

// batch submit single string message, with default cache size 100
for($i = 1; $i <= 129; $i++){
    $batchLogger->info('something wrong with the inner info '.$i);
}

// manually flush log message
$batchLogger->logFlush();

// query logs manually;
getLogs($client,$project,$logstore);

$anotherLogger->info('test log message 000 info');
$anotherLogger->warn('test log message 000 warn');
$anotherLogger->error('test log message 000 error');
$anotherLogger->debug('test log message 000 debug');

$logMap['level'] = 'info';
$anotherLogger->infoArray($logMap);
$logMap['level'] = 'debug';
$anotherLogger->debugArray($logMap);
$logMap['level'] = 'warn';
$anotherLogger->warnArray($logMap);
$logMap['level'] = 'error';
$anotherLogger->errorArray($logMap);

$anotherLogger->logFlush();
/**
 * client and logger usage end
 */

// create a csv shipper and delete it
createCsvShipper($client, $project, $logstore);
getAndRetryShipperTasks($client, $project, $logstore, 'testcsvshipper');
var_dump(getShipperConfig($client, $project, $logstore, 'testcsvshipper'));
deleteShipper($client, $project, $logstore,'testcsvshipper');

// create a json shipper and delete it
createJsonShipper($client, $project, $logstore);
var_dump(getShipperConfig($client, $project, $logstore, 'testjsonshipper'));
deleteShipper($client, $project, $logstore,'testjsonshipper');

// create a parquet shipper and delete it
createParquetShipper($client, $project, $logstore);
var_dump(getShipperConfig($client, $project, $logstore, 'testparquetshipper'));
deleteShipper($client, $project, $logstore,'testparquetshipper');

// create a csv shipper and update it
createCsvShipper($client, $project, $logstore);
$listShppers = listShipper($client, $project, $logstore);
var_dump(updateShipper($client, $project, $logstore, $listShppers->getShippers()[0]));

function updateShipper($client, $project, $logstore, $shipperName){

    $updateShipper = new Aliyun_Log_Models_UpdateShipperRequest($project);
    $updateShipper->setShipperName($shipperName);
    $updateShipper->setTargetType('oss');
    $updateShipper->setLogStore($logstore);

    $ossConfigResp = getShipperConfig($client, $project, $logstore, $shipperName);
    $ossConfigResp->getTargetConfigration()['bufferInterval'] = 500;

    $updateShipper->setTargetConfigration($ossConfigResp->getTargetConfigration());

    $updateShipperResp = $client->updateShipper($updateShipper);

    return $updateShipperResp;
}

function getAndRetryShipperTasks(Aliyun_Log_Client $client, $project, $logstore,$shipperName){
    $getShipperTasks = new Aliyun_Log_Models_GetShipperTasksRequest($project);
    $getShipperTasks->setShipperName($shipperName);
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
    $retryShipperTask->setShipperName($shipperName);
    $retryShipperTask->setLogStore($logstore);
    $retryShipperTask->setTaskLists($taskIdList);
    $client->retryShipperTasks($retryShipperTask);
}

function deleteShipper(Aliyun_Log_Client $client, $project, $logstore, $shipperName){
    //try delete the existing shipper
    $deleteShipper = new Aliyun_Log_Models_DeleteShipperRequest($project);
    $deleteShipper->setShipperName($shipperName);
    $deleteShipper->setLogStore($logstore);
    try{
        $client->deleteShipper($deleteShipper);
    }catch (Exception $ex){}
}

function getShipperCommonConfig(Aliyun_Log_Models_OssShipperStorage $ossShipperStorage){
    $ossConfig = new Aliyun_Log_Models_OssShipperConfig();
    $ossConfig->setOssBucket('sls-test-oss-shipper');
    $ossConfig->setOssPrefix('logtailalarm');
    $ossConfig->setBufferInterval(300);
    $ossConfig->setBufferSize(5);
    $ossConfig->setCompressType('none');
    $ossConfig->setRoleArn('acs:ram::1654218965343050:role/aliyunlogdefaultrole');
    $ossConfig->setTimeZone("+0800");
    $ossConfig->setStorage($ossShipperStorage);
    $ossConfig->setPathFormat('%Y/%m/%d/%H');
    return $ossConfig;
}

function createCommonShipper($project, $logstore, $shipperName){
    //create shipper with csv storage
    $shipper = new Aliyun_Log_Models_CreateShipperRequest($project);
    $shipper->setShipperName($shipperName);
    $shipper->setTargetType('oss');
    $shipper->setLogStore($logstore);
    return $shipper;
}

function createCsvShipper(Aliyun_Log_Client $client, $project, $logstore){
    $shipper = createCommonShipper($project, $logstore, 'testcsvshipper');

    $ossCsvStorage = new Aliyun_Log_Models_OssShipperCsvStorage();
    $ossCsvStorage->setColumns(array('__topic__',
        'alarm_count',
        'alarm_message',
        'alarm_type',
        'category',
        'project_name'));
    $ossCsvStorage->setDelimiter(',');
    $ossCsvStorage->setQuote('"');
    $ossCsvStorage->setLineFeed('\n');
    $ossCsvStorage->setHeader(false);
    $ossCsvStorage->setNullIdentifier('');

    $ossConfig = getShipperCommonConfig($ossCsvStorage);
    $shipper->setTargetConfigration($ossConfig->to_json_object());
    try{
        $client->createShipper($shipper);
    }catch (Exception $exception){
        var_dump($exception);
    }
}

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

/**
 * Query existing shipper configuration from log server
 * @param Aliyun_Log_Client $client
 * @param $project
 * @param $logstore
 * @param $shipperName
 * @return Aliyun_Log_Models_GetShipperConfigRequest
 */
function getShipperConfig(Aliyun_Log_Client $client, $project, $logstore, $shipperName){

    $getShipperConfig = new Aliyun_Log_Models_GetShipperConfigRequest($project);
    $getShipperConfig->setShipperName($shipperName);
    $getShipperConfig->setLogStore($logstore);
    $getconfigResp = $client->getShipperConfig($getShipperConfig);
    return $getconfigResp;
}

/**
 * get the list of existing shippers
 * @param Aliyun_Log_Client $client
 * @param $project
 * @param $logstore
 * @return Aliyun_Log_Models_ListShipperResponse
 */
function listShipper(Aliyun_Log_Client $client, $project, $logstore){
    $listShipper = new Aliyun_Log_Models_ListShipperRequest($project);
    $listShipper->setLogStore($logstore);
    $listShpperResp = $client->listShipper($listShipper);
    return $listShpperResp;
}

/**
 * create a parquet shipper
 * @param Aliyun_Log_Client $client
 * @param $project
 * @param $logstore
 */
function createParquetShipper(Aliyun_Log_Client $client, $project, $logstore){
    //create shipper with json storage
    $shipper = createCommonShipper($project, $logstore, 'testparquetshipper');

    $ossParquetStorage = new Aliyun_Log_Models_OssShipperParquetStorage();
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
    $ossConfig = getShipperCommonConfig($ossParquetStorage);
    $shipper->setTargetConfigration($ossConfig->to_json_object());

    try{

        $client->createShipper($shipper);
    }catch (Exception $exception){
        var_dump($exception);
    }

}

/**
 * create a json shipper
 * @param Aliyun_Log_Client $client
 * @param $project
 * @param $logstore
 */
function createJsonShipper(Aliyun_Log_Client $client, $project, $logstore){
    // create a json shipper
    $ossJsonStorage = new Aliyun_Log_Models_OssShipperJsonStorage();
    $ossJsonStorage->setEnableTag(true);

    //create shipper with json storage
    $shipper = createCommonShipper($project, $logstore, 'testjsonshipper');

    $ossConfig = getShipperCommonConfig($ossJsonStorage);
    $shipper->setTargetConfigration($ossConfig->to_json_object());
    try{
        $client->createShipper($shipper);
    }catch (Exception $exception){
        var_dump($exception);
    }
}
