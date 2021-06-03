<?php
namespace Aliyun\Log\Models\Request;

/**
 * 
 *
 * @author log service dev
 */
class GetACLRequest extends \Aliyun\Log\Models\Request\Request {
    
    private $aclId;
    /**
     * Aliyun_Log_Models_GetACLRequest Constructor
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
