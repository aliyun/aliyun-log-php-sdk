<?php
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */

require_once realpath(dirname(__FILE__) . '/Response.php');

/**
 * The response of the GetLog API from log service.
 *
 * @author log service dev
 */
class Aliyun_Log_Models_GetMachineGroupResponse extends Aliyun_Log_Models_Response {


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
        $this->machineGroup = new Aliyun_Log_Models_MachineGroup();
        $this->machineGroup->setFromArray($resp);
    }

    public function getMachineGroup(){
        return $this->machineGroup;
    } 

}
