<?php
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */

require_once realpath(dirname(__FILE__) . '/Response.php');
require_once realpath(dirname(__FILE__) . '/Shard.php');

/**
 * The response of the GetLog API from log service.
 *
 * @author log service dev
 */
class Aliyun_Log_Models_ListShardsResponse extends Aliyun_Log_Models_Response {

    private $shardIds; 
    /**
     * Aliyun_Log_Models_ListShardsResponse constructor
     *
     * @param array $resp
     *            GetLogs HTTP response body
     * @param array $header
     *            GetLogs HTTP response header
     */
    public function __construct($resp, $header) {
        parent::__construct ( $header );
        foreach($resp as $key=>$value){
            $this->shardIds[] = $value['shardID'];
            $this->shards[] = new Aliyun_Log_Models_Shard($value['shardID'],$value["status"],$value["inclusiveBeginKey"],$value["exclusiveEndKey"],$value["createTime"]);
        }
    }

    public function getShardIds(){
      return $this-> shardIds;
    }
    public function getShards()
    {
        return $this -> shards;
    }
   
}
