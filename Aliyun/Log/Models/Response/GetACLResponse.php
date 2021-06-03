<?php
namespace Aliyun\Log\Models\Response;

/**
 * The response of the GetLog API from log service.
 *
 * @author log service dev
 */
class GetACLResponse extends \Aliyun\Log\Models\Response\Response {
    

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
            $this->acl = new \Aliyun\Log\Models\ACL();
            $this->acl->setFromArray($resp); 
        }
    }

    public function getAcl(){
        return $this->acl;
    }
   

}
