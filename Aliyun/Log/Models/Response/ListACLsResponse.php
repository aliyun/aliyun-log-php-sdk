<?php
namespace Aliyun\Log\Models\Response;

/**
 * The response of the GetLog API from log service.
 *
 * @author log service dev
 */
class ListACLsResponse extends \Aliyun\Log\Models\Response\Response {


    private $acls; 
    /**
     * Aliyun_Log_Models_ListACLsResponse constructor
     *
     * @param array $resp
     *            GetLogs HTTP response body
     * @param array $header
     *            GetLogs HTTP response header
     */
    public function __construct($resp, $header) {
        parent::__construct ( $header );
        $aclArr = array();
        if(isset($resp['acls'])){
            foreach($resp['acls'] as $value){
                $aclObj = new \Aliyun\Log\Models\ACL();
                $aclObj->setFromArray($value);
                $aclArr[]=$aclObj;
            }
        }
        $this->acls = $aclArr;
    }

    public function getAcls(){
        return $this->acls;
    }
   

}
