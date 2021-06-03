<?php
function putLogs(\Aliyun\Log\Client $client, $project, $logstore) {
    $topic = 'TestTopic';
    
    $contents = array( // key-value pair
        'TestKey'=>'TestContent'
    );
    $logItem = new \Aliyun\Log\Models\LogItem();
    $logItem->setTime(time());
    $logItem->setContents($contents);
    $logitems = array($logItem);
    $request = new \Aliyun\Log\Models\Request\PutLogsRequest($project, $logstore, 
            $topic, null, $logitems);
    
    try {
        $response = $client->putLogs($request);
        logVarDump($response);
    } catch (\Aliyun\Log\Exception $ex) {
        logVarDump($ex);
    } catch (\Exception $ex) {
        logVarDump($ex);
    }
}

function listLogstores(\Aliyun\Log\Client $client, $project) {
    try{
        $request = new \Aliyun\Log\Models\Request\ListLogstoresRequest($project);
        $response = $client->listLogstores($request);
        logVarDump($response);
    } catch (\Aliyun\Log\Exception $ex) {
        logVarDump($ex);
    } catch (\Exception $ex) {
        logVarDump($ex);
    }
}


function listTopics(\Aliyun\Log\Client $client, $project, $logstore) {
    $request = new \Aliyun\Log\Models\Request\ListTopicsRequest($project, $logstore);
    
    try {
        $response = $client->listTopics($request);
        logVarDump($response);
    } catch (\Aliyun\Log\Exception $ex) {
        logVarDump($ex);
    } catch (\Exception $ex) {
        logVarDump($ex);
    }
}

function getLogs(\Aliyun\Log\Client $client, $project, $logstore) {
    $topic = 'TestTopic';
    $from = time()-3600;
    $to = time();
    $request = new \Aliyun\Log\Models\Request\GetLogsRequest($project, $logstore, $from, $to, $topic, '', 100, 0, False);
    
    try {
        $response = $client->getLogs($request);
        foreach($response -> getLogs() as $log)
        {
            print $log -> getTime()."\t";
            foreach($log -> getContents() as $key => $value){
                print $key.":".$value."\t";
            }
            print "\n";
        }

    } catch (\Aliyun\Log\Exception $ex) {
        logVarDump($ex);
    } catch (\Exception $ex) {
        logVarDump($ex);
    }
}

function getHistograms(\Aliyun\Log\Client $client, $project, $logstore) {
    $topic = 'TestTopic';
    $from = time()-3600;
    $to = time();
    $request = new \Aliyun\Log\Models\Request\GetHistogramsRequest($project, $logstore, $from, $to, $topic, '');
    
    try {
        $response = $client->getHistograms($request);
        logVarDump($response);
    } catch (\Aliyun\Log\Exception $ex) {
        logVarDump($ex);
    } catch (\Exception $ex) {
        logVarDump($ex);
    }
}
function listShard(\Aliyun\Log\Client $client,$project,$logstore){
    $request = new \Aliyun\Log\Models\Request\ListShardsRequest($project,$logstore);
    try
    {
        $response = $client -> listShards($request);
        logVarDump($response);
    } catch (\Aliyun\Log\Exception $ex) {
        logVarDump($ex);
    } catch (\Exception $ex) {
        logVarDump($ex);
    }
}

function batchGetLogs(\Aliyun\Log\Client $client,$project,$logstore)
{
    $listShardRequest = new \Aliyun\Log\Models\Request\ListShardsRequest($project,$logstore);
    $listShardResponse = $client -> listShards($listShardRequest);
    foreach($listShardResponse-> getShardIds()  as $shardId)
    {
        $getCursorRequest = new \Aliyun\Log\Models\Request\GetCursorRequest($project,$logstore,$shardId,null, time() - 60);
        $response = $client -> getCursor($getCursorRequest);
        $cursor = $response-> getCursor();
        $count = 100;
        while(true)
        {
            $batchGetDataRequest = new \Aliyun\Log\Models\Request\BatchGetLogsRequest($project,$logstore,$shardId,$count,$cursor);
            logVarDump($batchGetDataRequest);
            $response = $client -> batchGetLogs($batchGetDataRequest);
            if($cursor == $response -> getNextCursor())
            {
                break;
            }
            $logGroupList = $response -> getLogGroupList();
            foreach($logGroupList as $logGroup)
            {
                print ($logGroup->getCategory());

                foreach($logGroup -> getLogsArray() as $log)
                {
                    foreach($log -> getContentsArray() as $content)
                    {
                        print($content-> getKey().":".$content->getValue()."\t");
                    }
                    print("\n");
                }
            }
            $cursor = $response -> getNextCursor();
        }
    }
}

function batchGetLogsWithRange(\Aliyun\Log\Client $client,$project,$logstore)
{
    $listShardRequest = new \Aliyun\Log\Models\Request\ListShardsRequest($project,$logstore);
    $listShardResponse = $client -> listShards($listShardRequest);
    foreach($listShardResponse-> getShardIds()  as $shardId)
    {
        //pull data which reached server at time range [now - 60s, now) for every shard
        $curTime = time();
        $beginCursorResponse = $client->getCursor(new \Aliyun\Log\Models\Request\GetCursorRequest($project,$logstore,$shardId,null,$curTime - 60));
        $beginCursor = $beginCursorResponse-> getCursor();
        $endCursorResponse = $client -> getCursor(new \Aliyun\Log\Models\Request\GetCursorRequest($project,$logstore,$shardId,null,$curTime));
        $endCursor = $endCursorResponse-> getCursor();
        $cursor = $beginCursor;
        print("-----------------------------------------\nbatchGetLogs for shard: ".$shardId.", cursor range: [".$beginCursor.", ".$endCursor.")\n");
        $count = 100;
        while(true)
        {
            $batchGetDataRequest = new \Aliyun\Log\Models\Request\BatchGetLogsRequest($project,$logstore,$shardId,$count,$cursor,$endCursor);
            $response = $client -> batchGetLogs($batchGetDataRequest);
            $logGroupList = $response -> getLogGroupList();
            $logGroupCount = 0;
            $logCount = 0;
            foreach($logGroupList as $logGroup)
            {
                $logGroupCount += 1;
                foreach($logGroup -> getLogsArray() as $log)
                {
                    $logCount += 1;
                    foreach($log -> getContentsArray() as $content)
                    {
                        print($content-> getKey().":".$content->getValue()."\t");
                    }
                    print("\n");
                }
            }
            $nextCursor = $response -> getNextCursor();
            print("batchGetLogs once, cursor: ".$cursor.", nextCursor: ".nextCursor.", logGroups: ".$logGroupCount.", logs: ".$logCount."\n");
            if($cursor == $nextCursor)
            {
                //read data finished
                break;
            }
            $cursor = $nextCursor;
        }
    }
}

function mergeShard(\Aliyun\Log\Client $client,$project,$logstore,$shardId)
{
    $request = new \Aliyun\Log\Models\Request\MergeShardsRequest($project,$logstore,$shardId);
    try
    {
        $response = $client -> mergeShards($request);
        logVarDump($response);
    }catch (\Aliyun\Log\Exception $ex) {
        logVarDump($ex);
    } catch (\Exception $ex) {
        logVarDump($ex);
    }
}
function splitShard(\Aliyun\Log\Client $client,$project,$logstore,$shardId,$midHash)
{
    $request = new \Aliyun\Log\Models\Request\SplitShardRequest($project,$logstore,$shardId,$midHash);
    try
    {
        $response = $client -> splitShard($request);
        logVarDump($response);
    }catch (\Aliyun\Log\Exception $ex) {
        logVarDump($ex);
    } catch (\Exception $ex) {
        logVarDump($ex);
    }
}

function logVarDump($expression){
    print "<br>loginfo begin = ".get_class($expression)."<br>";
    var_dump($expression);
    print "<br>loginfo end<br>";
}

/*
 * please refer to aliyun sdk document for detail:
 * http://help.aliyun-inc.com/internaldoc/detail/29074.html?spm=0.0.0.0.tqUNn5
 */
$endpoint = 'http://cn-shanghai-corp.sls.aliyuncs.com';
$accessKeyId = '';
$accessKey = '';
$project = '';
$logstore = '';
$token = "";

$client = new \Aliyun\Log\Client($endpoint, $accessKeyId, $accessKey,$token);
listShard($client,$project,$logstore);
mergeShard($client,$project,$logstore,2);
deleteShard($client,$project,$logstore,2);
splitShard($client,$project,$logstore,2,"80000000000000000000000000000001");
putLogs($client, $project, $logstore);
listShard($client,$project,$logstore);
batchGetLogs($client,$project,$logstore);
batchGetLogsWithRange($client,$project,$logstore);
listLogstores($client, $project);
listTopics($client, $project, $logstore);
getHistograms($client, $project, $logstore);
getLogs($client, $project, $logstore);
