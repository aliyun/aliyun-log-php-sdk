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
class Aliyun_Log_Models_GetMachineGroupRequest extends Aliyun_Log_Models_Request {

    private $groupName;
    /**
     * Aliyun_Log_Models_GetMachineGroupRequest Constructor
     *
     */
    public function __construct($groupName=null) {
        $this->groupName = $groupName;
    }
    public function getGroupName(){
        return $this->groupName;
    } 
    public function setGroupName($groupName){
        $this->groupName = $groupName;
    }
    
}
