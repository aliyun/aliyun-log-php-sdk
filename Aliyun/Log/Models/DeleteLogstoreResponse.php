<?php
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */

require_once realpath(dirname(__FILE__) . '/Response.php');

/**
 * The response of the DeleteLogstore API from log service.
 *
 * @author log service dev
 */
class Aliyun_Log_Models_DeleteLogstoreResponse extends Aliyun_Log_Models_Response {
    
    /**
     * Aliyun_Log_Models_DeleteLogstoreResponse constructor
     *
     * @param array $resp
     *            DeleteLogstore HTTP response body
     * @param array $header
     *            DeleteLogstore HTTP response header
     */
    public function __construct($resp, $header) {
        parent::__construct ( $header );
    }
    
}
