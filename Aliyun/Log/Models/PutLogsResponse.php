<?php
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */

require_once realpath(dirname(__FILE__) . '/Response.php');

/**
 * The response of the PutLogs API from log service.
 *
 * @author log service dev
 */
class Aliyun_Log_Models_PutLogsResponse extends Aliyun_Log_Models_Response {
    /**
     * Aliyun_Log_Models_PutLogsResponse constructor
     *
     * @param array $header
     *            PutLogs HTTP response header
     */
    public function __construct($headers) {
        parent::__construct ( $headers );
    }
}
