<?php
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */

require_once realpath(dirname(__FILE__) . '/Response.php');

/**
 *
 * @author log service dev
 */
class Aliyun_Log_Models_ListConfigsResponse extends Aliyun_Log_Models_Response {
    
    private $total;
    private $configs;
    /**
     * Aliyun_Log_Models_ListConfigsResponse constructor
     *
     * @param array $resp
     *            GetLogs HTTP response body
     * @param array $header
     *            GetLogs HTTP response header
     */
    public function __construct($resp, $header) {
        parent::__construct ( $header );
        $this->size = $resp['total'];
        $this->configs = $resp['configs']; 
    }

    public function getSize(){
      return count($this->configs)
    }

    public function getTotal(){
        return $this ->total;
    }

    public function getConfigs(){
      return $this->configs;
    }
   

}
