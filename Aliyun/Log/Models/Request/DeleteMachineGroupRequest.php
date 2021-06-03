<?php
namespace Aliyun\Log\Models\Request;

/**
 * 
 *
 * @author log service dev
 */
class DeleteMachineGroupRequest extends \Aliyun\Log\Models\Request\Request {


    private $groupName; 
    /**
     * Aliyun_Log_Models_DeleteMachineGroupRequest Constructor
     *
     */
    public function __construct($groupName) {
        $this->groupName = $groupName;
    }

    public function getGroupName(){
        return $this->groupName;
    }

    public function setGroupName($groupName){
        $this->groupName = $groupName;
    }
    
}
