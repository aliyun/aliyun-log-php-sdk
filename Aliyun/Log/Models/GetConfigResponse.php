<?php
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */

require_once realpath(dirname(__FILE__) . '/Response.php');
require_once realpath(dirname(__FILE__) . '/Config.php');
/**
 * The response of the GetLog API from log service.
 *
 * @author log service dev
 */
class Aliyun_Log_Models_GetConfigResponse extends Aliyun_Log_Models_Response {


    private $config;

    /**
     * Aliyun_Log_Models_GetConfigResponse constructor
     *
     * @param array $resp
     *            GetLogs HTTP response body
     * @param array $header
     *            GetLogs HTTP response header
     */
    public function __construct($resp, $header) {
        parent::__construct ( $header );
        $this->config = new Aliyun_Log_Models_Config();
        $this->config->setFromArray($resp);
    }

    public function getConfig(){
        return $this->config;
    }
   
}
