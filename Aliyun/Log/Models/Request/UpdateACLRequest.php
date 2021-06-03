<?php
namespace Aliyun\Log\Models\Request;

/**
 * 
 *
 * @author log service dev
 */
class UpdateACLRequest extends \Aliyun\Log\Models\Request\Request {

    private $acl;
    /**
     * Aliyun_Log_Models_UpdateACLRequest Constructor
     *
     */
    public function __construct($acl) {
        $this->acl = $acl;
    }

    public function getAcl(){
        return $this->acl;
    }
    public function setAcl($acl){
        $this->acl = $acl;
    }
}
