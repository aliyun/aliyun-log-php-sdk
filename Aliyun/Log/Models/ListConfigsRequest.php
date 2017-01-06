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
class Aliyun_Log_Models_ListConfigsRequest extends Aliyun_Log_Models_Request {
    
    private $configName;
    private $offset;
    private $size; 
    
    /**
     * Aliyun_Log_Models_ListConfigsRequest Constructor
     *
     */
    public function __construct($configName=null,$offset=null,$size=null) {
      //parent::__construct ( $project );
        $this->configName = $configName;
        $this->offset = $offset;
        $this->size = $size;
    }

    public function getConfigName(){
        return $this->configName;
    }

    public function setConfigName($configName){
        $this->configName = $configName;
    }
 
    public function getOffset(){
        return $this->offset;
    }

    public function setOffset($offset){
        $this->offset = $offset;
    }

    public function getSize(){
        return $this->size;
    }

    public function setSize($size){
        $this->size = $size;
    }
}
