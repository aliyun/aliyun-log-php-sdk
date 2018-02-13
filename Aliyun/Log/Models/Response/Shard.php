<?php
/**
 * The class used to present the shard detail, for every shard ,it  contains id , status , begin Range, end range, createtime
 *
 * @author log service dev
 */
class Aliyun_Log_Models_Shard{
    /**
     * @var interger  the shard id
     */
    private $shardId;

    /**
     * @var string  shard status (readwrite or readonly)
     */
    private $status;

    /**
     * @var string shard inclusive begin key
     */
    private $inclusiveBeginKey;

    /**
     * @var string shard  exclusive begin key
     */
    private $exclusiveEndKey;

    /**
     * @var integer shard create time
     */
    private $createTime;

    /**
     * Aliyun_Log_Models_Shhard constructor
     *
     * @param integer $shardId
     *                  the shard id
     * @param string  $status
     *                  the shard status
     * @param string $inclusiveBeginKey
     *                  the shard inclusive begin key
     * @param string $exclusiveEndKey
     *                  the shard exclusive end key
     * @para integer @createTime
     *                  the shard create time
     */
    public function __construct($shardId,$status,$inclusiveBeginKey,$exclusiveEndKey,$createTime) {
        $this->shardId=$shardId;
        $this->status = $status;
        $this->inclusiveBeginKey = $inclusiveBeginKey;
        $this->exclusiveEndKey = $exclusiveEndKey;
        $this->createTime = $createTime;
    }

    /**
     * Get the shardId
     *
     * @return integer the shard id
     */
    public function getShardId(){
        return $this -> shardId;
    }
    /**
     * Get the shard status
     *
     * @return string the shard status
     */
    public function getStatus(){
        return $this -> status;
    }
    /**
     * Get the shard inclusive begin key
     *
     * @return string inclusive begin key
     */
    public function getInclusiveBeginKey(){
        return $this -> inclusiveBeginKey;
    }
    /**
     * Get the shard exclusive begin key
     *
     * @return string exclusive begin key
     */
    public function getExclusiveBeginKey(){
        return $this -> exclusiveBeginKey;
    }
    /**
     * Get the shard create time
     *
     * @return integer createTime
     */
    public function  getCreateTime(){
        return $this -> createTime;
    }
}
