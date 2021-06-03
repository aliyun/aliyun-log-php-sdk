<?php
namespace Aliyun\Log\Models\Request;

/**
 * 
 *
 * @author log service dev
 */
class GetMachineGroupRequest extends \Aliyun\Log\Models\Request\Request {

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
