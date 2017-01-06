<?php
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */

require_once realpath(dirname(__FILE__) . '/Request.php');

/**
 * 
 *
 * @author log service dev
 */
class Aliyun_Log_Models_CreateACLRequest extends Aliyun_Log_Models_Request {

    private $acl;
    /**
     * Aliyun_Log_Models_CreateACLRequest Constructor
     *
     */
    public function __construct($acl=null) {
        $this->acl = $acl;
    }

    public function getAcl(){
        return $this->acl;
    }
    public function setAcl($acl){
        $this->acl = $acl;
    }
    
}
