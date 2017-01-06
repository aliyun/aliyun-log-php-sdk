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
class Aliyun_Log_Models_CreateACLResponse extends Aliyun_Log_Models_Response {

    private $aclId; 
    /**
     * Aliyun_Log_Models_Response constructor
     *
     * @param array $resp
     *            GetLogs HTTP response body
     * @param array $header
     *            GetLogs HTTP response header
     */
    public function __construct($resp, $header) {
        parent::__construct ( $header );
        $this->aclId = $resp['aclId'];
    }
    public function getAclId(){
        return $this->aclId;
    }

}
