<?php
namespace Aliyun\Log\Models\Request;

/**
 * 
 *
 * @author log service dev
 */
class GetMachineRequest extends \Aliyun\Log\Models\Request\Request {
    
    private $uuid;

    /**
     * Aliyun_Log_Models_GetMachineRequest Constructor
     *
     */
    public function __construct($uuid=null) {
        $this->uuid = $uuid;
    }

    public function getUuid(){
        return $this->uuid;
    }

    public function setUuid($uuid){
        $this->uuid = $uuid;
    }
    
}
