<?php
echo "Hello, World!";
require_once realpath(dirname(__FILE__) . '/./Log_Autoload.php');

$endpoint = 'cn-hangzhou.log.aliyuncs.com';
$accessKeyId = '';
$accessKey = '';
$token = "";
$project = 'test';
$logstore = 'test';


$credentialsProvider = new Aliyun_Log_Models_StaticCredentialsProvider($accessKeyId, $accessKey, $token);
$client = new Aliyun_Log_Client($endpoint, "", "", "", $credentialsProvider);

$req = new Aliyun_Log_Models_GetLogsRequest($project, $logstore, 1698740109, 1698744321, '', '*', null, null, null, null);

function putLogs(Aliyun_Log_Client $client, $project, $logstore)
{
    $topic = 'TestTopic';

    $contents = array( // key-value pair
        'TestKey' => 'TestContent',
        'kv_json' => '{"a": "b", "c": 19021}'
    );
    $logItem = new Aliyun_Log_Models_LogItem();
    $logItem->setTime(time());
    $logItem->setContents($contents);
    $logitems = array($logItem);
    $request = new Aliyun_Log_Models_PutLogsRequest(
        $project,
        $logstore,
        $topic,
        null,
        $logitems
    );

    try {
        $response = $client->putLogs($request);
    } catch (Aliyun_Log_Exception $ex) {
        var_dump($ex);
    } catch (Exception $ex) {
        var_dump($ex);
    }
}

putLogs($client, $project, $logstore);
$res = $client->getLogs($req);
var_dump($res->getLogs());
