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
class Aliyun_Log_Models_DeleteACLResponse extends Aliyun_Log_Models_Response {
    
    /**
     * Aliyun_Log_Models_DeleteACLResponse constructor
     *
     * @param array $resp
     *            GetLogs HTTP response body
     * @param array $header
     *            GetLogs HTTP response header
     */
    public function __construct($header) {
        parent::__construct ( $header );
    }
   

}
