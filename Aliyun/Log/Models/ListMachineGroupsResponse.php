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
class Aliyun_Log_Models_ListMachineGroupsResponse extends Aliyun_Log_Models_Response {

    private $offset;
    private $size;
    private $machineGroups;
    /**
     * Aliyun_Log_Models_ListMachineGroupsResponse constructor
     *
     * @param array $resp
     *            GetLogs HTTP response body
     * @param array $header
     *            GetLogs HTTP response header
     */
    public function __construct($resp, $header) {
        parent::__construct ( $header );
        $this->offset = $resp['offset'];
        $this->size = $resp['size'];
        $this->machineGroups = $resp['machinegroups'];
    }

    public function getOffset(){
        return $this->offset;
    }

    public function getSize(){
        return $this->size;
    } 
    
    public function getMachineGroups(){
        return $this->machineGroups;
    } 
}
