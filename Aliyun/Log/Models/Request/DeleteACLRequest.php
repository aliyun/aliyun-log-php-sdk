<?php
namespace Aliyun\Log\Models\Request;

/**
 * 
 *
 * @author log service dev
 */
class DeleteACLRequest extends \Aliyun\Log\Models\Request\Request {

    private $aclId;
    /**
     * Aliyun_Log_Models_DeleteACLRequest Constructor
     *
     */
    public function __construct($aclId=null) {
        $this->aclId = $aclId;
    }
    public function getAclId(){
        return $this->aclId;
    }
    public function setAclId($aclId){
        $this->aclId = $aclId;
    }
    
}
