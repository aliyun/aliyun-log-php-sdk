<?php
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */

require_once realpath(dirname(__FILE__) . '/Request.php');

/**
 * The request used to get cursor by fromTime or begin/end mode 
 *
 * @author log service dev
 */
class Aliyun_Log_Models_GetCursorRequest extends Aliyun_Log_Models_Request {
    
    /**
     * @var string logstore name
     */
    private $logstore;
    
    /**
     * @var string shard id
     */
    private $shardId;

    //mode and fromTime: choose one and another remains null
    /**
     * @var string value should be 'begin' or 'end'
     *         begin:return cursor point to first loggroup
     *         end:return cursor point to position after last loggroup
     *         if $mode is set to not null,$fromTime must be set null
     */        
    private $mode;
    
    /**
     * @var integer unix_timestamp
     *         return cursor point to first loggroup whose time after $fromTime 
     */        
    private $fromTime;

    /**
     * Aliyun_Log_Models_GetCursorRequest Constructor
     * @param string $project
     *            project name
     * @param string $logstore
     *            logstore name
     * @param string $shardId
     *            shard id
     * @param string $mode
     *            query mode,value must be 'begin' or 'end' 
     * @param string $fromTime
     *            query by from time,unix_timestamp
     */
    public function __construct($project,$logstore,$shardId,$mode=null,$fromTime=-1) {
      parent::__construct ( $project );
      $this->logstore = $logstore;
      $this->shardId = $shardId;
      $this->mode = $mode;
      $this->fromTime = $fromTime;
    }

    /**
     * Get logstore name
     *
     * @return string logstore name
     */
    public function getLogstore(){
      return $this->logstore;
    }
    
    /**
     * Set logstore name
     *
     * @param string $logstore
     *            logstore name
     */
    public function setLogstore($logstore){
      $this->logstore = $logstore;
    }

    /**
     * Get shard id
     *
     * @return string shard id
     */
    public function getShardId(){
      return $this->shardId;
    }
    
    /**
     * Set shard id
     *
     * @param string $shardId
     *            shard id
     */
    public function setShardId($shardId){
      $this->shardId = $shardId;
    }

    /**
     * Get mode
     *
     * @return string mode
     */
    public function getMode(){
      return $this->mode;
    }
    
    /**
     * Set mode
     *  
     * @param string $mode
     *            value must be 'begin' or 'end'
     */
    public function setMode($mode){
      $this->mode = $mode;
    }

    /**
     * Get from time
     *
     * @return integer(unix_timestamp) from time
     */
    public function getFromTime(){
      return $this->fromTime;
    }

    /**
     * Set from time
     *
     * @param integer $fromTime
     *            from time (unix_timestamp)
     */
    public function setFromTime($fromTime){
      $this->fromTime = $fromTime;
    }

}
