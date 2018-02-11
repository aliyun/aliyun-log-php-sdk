<?php
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */

require_once realpath(dirname(__FILE__) . '/Request.php');

/**
 * 
 *
 * @author log service dev
 */
class Aliyun_Log_Models_SplitShardRequest extends Aliyun_Log_Models_Request {

    private $logstore;

    /**
     * Aliyun_Log_Models_SplitShardRequest Constructor
     *
     */
    public function __construct($project,$logstore,$shardId,$midHash) {
        parent::__construct ( $project );
        $this->logstore = $logstore;
        $this->shardId = $shardId;
        $this->midHash = $midHash;
    }

    public function getLogstore(){
      return $this->logstore;
    }

    public function setLogstore($logstore){
      $this->logstore = $logstore;
    }

    public function getShardId(){
        return $this->shardId;
    }
    public function getMidHash(){
        return $this->midHash;
    }
    
}
