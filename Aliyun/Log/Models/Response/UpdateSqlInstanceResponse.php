<?php
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */

require_once realpath(dirname(__FILE__) . '/Response.php');

/**
 * The response of the UpdateSqlInstance API from log service.
 *
 * @author log service dev
 */
class Aliyun_Log_Models_UpdateSqlInstanceResponse extends Aliyun_Log_Models_Response {
    
    /**
     * Aliyun_Log_Models_UpdateSqlInstanceResponse constructor
     *
     * @param array $resp
     *            UpdateSqlInstance HTTP response body
     * @param array $header
     *            UpdateSqlInstance HTTP response header
     */
    public function __construct($resp, $header) {
        parent::__construct ( $header );
    }
    
}
