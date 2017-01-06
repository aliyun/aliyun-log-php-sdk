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
class Aliyun_Log_Models_RemoveConfigFromMachineGroupRequest extends Aliyun_Log_Models_Request {
    private $groupName;
    private $configName; 
   
    /**
     * Aliyun_Log_Models_RemoveConfigFromMachineGroupRequest Constructor
     *
     */
    public function __construct($groupName=null,$configName=null) {
        $this->groupName = $groupName;
        $this->configName = $configName;
    }
    public function getGroupName(){
        return $this->groupName;
    }
    public function setGroupName($groupName){
        $this->groupName = $groupName;
    }

    public function getConfigName(){
        return $this->configName;
    }
    public function setConfigName($configName){
        $this->configName = $configName;
    }
    
}
