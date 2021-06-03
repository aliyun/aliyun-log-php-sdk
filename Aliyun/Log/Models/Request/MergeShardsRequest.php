<?php
namespace Aliyun\Log\Models\Request;

/**
 * 
 *
 * @author log service dev
 */
class MergeShardsRequest extends \Aliyun\Log\Models\Request\Request {

    private $logstore;

    /**
     * Aliyun_Log_Models_MergeShardsRequest Constructor
     *
     */
    public function __construct($project,$logstore,$shardId) {
        parent::__construct ( $project );
        $this->logstore = $logstore;
        $this->shardId = $shardId;
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
}
