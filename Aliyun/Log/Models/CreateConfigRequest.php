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
class Aliyun_Log_Models_CreateConfigRequest extends Aliyun_Log_Models_Request {

    private $config;

    /**
     * Aliyun_Log_Models_CreateConfigRequest Constructor
     *
     */
    public function __construct($config) {
        $this->config = $config;
    }

    public function getConfig(){
        return $this->config;
        
    }

    public function setConfig($config){
        $this->config = $config;
    }
    
}
