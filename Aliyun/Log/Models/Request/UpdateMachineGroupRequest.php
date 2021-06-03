<?php
namespace Aliyun\Log\Models\Request;

/**
 * 
 *
 * @author log service dev
 */
class UpdateMachineGroupRequest extends \Aliyun\Log\Models\Request\Request {

    private $machineGroup; 
    /**
     * Aliyun_Log_Models_UpdateMachineGroupRequest Constructor
     *
     */
    public function __construct($machineGroup) {
        $this->machineGroup = $machineGroup;
    }

    public function getMachineGroup(){
        return $this->machineGroup;
    }

    public function setMachineGroup($machineGroup){
        $this->machineGroup = $machineGroup;
    }

    
}
