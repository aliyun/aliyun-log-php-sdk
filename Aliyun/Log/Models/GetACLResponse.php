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
class Aliyun_Log_Models_GetACLResponse extends Aliyun_Log_Models_Response {
    

    private $acl;
    /**
     * Aliyun_Log_Models_GetACLResponse constructor
     *
     * @param array $resp
     *            GetLogs HTTP response body
     * @param array $header
     *            GetLogs HTTP response header
     */
    public function __construct($resp, $header) {
        parent::__construct ( $header );
        $this->acl = null;
        if($resp!==null){
            $this->acl = new Aliyun_Log_Models_ACL();
            $this->acl->setFromArray($resp); 
        }
    }

    public function getAcl(){
        return $this->acl;
    }
   

}
