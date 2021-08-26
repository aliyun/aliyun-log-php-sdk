<?php
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */

require_once realpath(dirname(__FILE__) . '/Response.php');

/**
 * The response of the CreateSqlInstance API from log service.
 *
 * @author log service dev
 */
class Aliyun_Log_Models_CreateSqlInstanceResponse extends Aliyun_Log_Models_Response {
    
    /**
     * Aliyun_Log_Models_CreateSqlInstanceResponse constructor
     *
     * @param array $resp
     *            CreateSqlInstance HTTP response body
     * @param array $header
     *            CreateSqlInstance HTTP response header
     */
    public function __construct($resp, $header) {
        parent::__construct ( $header );
    }
    
}
