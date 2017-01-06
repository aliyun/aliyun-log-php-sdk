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
class Aliyun_Log_Models_GetMachineResponse extends Aliyun_Log_Models_Response {

    private $machine;

    /**
     * Aliyun_Log_Models_GetMachineResponse constructor
     *
     * @param array $resp
     *            GetLogs HTTP response body
     * @param array $header
     *            GetLogs HTTP response header
     */
    public function __construct($resp, $header) {
        parent::__construct ( $header );
        //echo json_encode($resp);
        $this->machine = new Aliyun_Log_Models_Machine();
        $this->machine->setFromArray($resp);
        
    }

    public function getMachine(){
        return $this->machine;
    }
   
}
