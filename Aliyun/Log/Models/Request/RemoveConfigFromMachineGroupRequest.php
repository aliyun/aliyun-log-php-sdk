<?php
namespace Aliyun\Log\Models\Request;

/**
 * 
 *
 * @author log service dev
 */
class RemoveConfigFromMachineGroupRequest extends \Aliyun\Log\Models\Request\Request {
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
