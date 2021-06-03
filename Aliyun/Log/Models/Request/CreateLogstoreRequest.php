<?php
namespace Aliyun\Log\Models\Request;

/**
 * The request used to create logstore from log service.
 *
 * @author log service dev
 */
class CreateLogstoreRequest extends \Aliyun\Log\Models\Request\Request{

    private  $logstore;
    private  $ttl;
    private  $shardCount;
    /**
     * Aliyun_Log_Models_CreateLogstoreRequest constructor
     * 
     * @param string $project project name
     */
    public function __construct($project=null,$logstore = null,$ttl = null,$shardCount = null) {
        parent::__construct($project);
        $this -> logstore = $logstore;
        $this -> ttl = $ttl;
        $this -> shardCount = $shardCount;
    }
    public function getLogstore()
    {
        return $this -> logstore;
    }
    public function getTtl()
    {
        return $this -> ttl;
    }
    public function getShardCount()
    {
        return $this -> shardCount;
    }
}
