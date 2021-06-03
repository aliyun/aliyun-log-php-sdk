<?php
namespace Aliyun\Log\Models\Response;

/**
 * The response of the GetLog API from log service.
 *
 * @author log service dev
 */
class CreateACLResponse extends \Aliyun\Log\Models\Response\Response {

    private $aclId; 
    /**
     * Aliyun_Log_Models_Response constructor
     *
     * @param array $resp
     *            GetLogs HTTP response body
     * @param array $header
     *            GetLogs HTTP response header
     */
    public function __construct($resp, $header) {
        parent::__construct ( $header );
        $this->aclId = $resp['aclId'];
    }
    public function getAclId(){
        return $this->aclId;
    }

}
