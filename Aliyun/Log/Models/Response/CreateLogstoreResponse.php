<?php
namespace Aliyun\Log\Models\Response;

/**
 * The response of the CreateLogstore API from log service.
 *
 * @author log service dev
 */
class CreateLogstoreResponse extends \Aliyun\Log\Models\Response\Response {
    
    /**
     * Aliyun_Log_Models_CreateLogstoreResponse constructor
     *
     * @param array $resp
     *            CreateLogstore HTTP response body
     * @param array $header
     *            CreateLogstore HTTP response header
     */
    public function __construct($resp, $header) {
        parent::__construct ( $header );
    }
    
}
