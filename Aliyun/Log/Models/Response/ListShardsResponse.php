<?php
namespace Aliyun\Log\Models\Response;

/**
 * The response of the GetLog API from log service.
 *
 * @author log service dev
 */
class ListShardsResponse extends \Aliyun\Log\Models\Response\Response {

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
            $this->shards[] = new \Aliyun\Log\Models\Response\Shard($value['shardID'],$value["status"],$value["inclusiveBeginKey"],$value["exclusiveEndKey"],$value["createTime"]);
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
