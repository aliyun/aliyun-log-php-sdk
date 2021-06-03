<?php
namespace Aliyun\Log\Models\Response;

/**
 * The response of the GetLog API from log service.
 *
 * @author log service dev
 */
class GetMachineGroupResponse extends \Aliyun\Log\Models\Response\Response {


    private $machineGroup;
    /**
     * Aliyun_Log_Models_GetMachineGroupResponse constructor
     *
     * @param array $resp
     *            GetLogs HTTP response body
     * @param array $header
     *            GetLogs HTTP response header
     */
    public function __construct($resp, $header) {
        parent::__construct ( $header );
        $this->machineGroup = new \Aliyun\Log\Models\MachineGroup();
        $this->machineGroup->setFromArray($resp);
    }

    public function getMachineGroup(){
        return $this->machineGroup;
    } 

}
