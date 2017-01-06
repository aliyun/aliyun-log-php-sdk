<?php
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */

require_once realpath(dirname(__FILE__) . '/Request.php');

/**
 * The request used to create logstore from log service.
 *
 * @author log service dev
 */
class Aliyun_Log_Models_CreateLogstoreRequest extends Aliyun_Log_Models_Request{

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
