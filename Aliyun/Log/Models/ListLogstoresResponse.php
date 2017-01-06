<?php
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */

require_once realpath(dirname(__FILE__) . '/Response.php');

/**
 * The response of the ListLogstores API from log service.
 *
 * @author log service dev
 */
class Aliyun_Log_Models_ListLogstoresResponse extends Aliyun_Log_Models_Response {
    
    /**
     * @var integer the number of total logstores from the response
     */
    private $count;
    
    /**
     * @var array all logstore
     */
    private $logstores;
    
    /**
     * Aliyun_Log_Models_ListLogstoresResponse constructor
     *
     * @param array $resp
     *            ListLogstores HTTP response body
     * @param array $header
     *            ListLogstores HTTP response header
     */
    public function __construct($resp, $header) {
        parent::__construct ( $header );
        $this->count = $resp ['total'];
        $this->logstores = $resp ['logstores'];
    }
    
    /**
     * Get total count of logstores from the response
     *
     * @return integer the number of total logstores from the response
     */
    public function getCount() {
        return $this->count;
    }
    
    /**
     * Get all the logstores from the response
     *
     * @return array all logstore
     */
    public function getLogstores() {
        return $this->logstores;
    }
}
