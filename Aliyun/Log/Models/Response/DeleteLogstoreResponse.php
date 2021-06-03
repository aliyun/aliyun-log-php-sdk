<?php
namespace Aliyun\Log\Models\Response;

/**
 * The response of the DeleteLogstore API from log service.
 *
 * @author log service dev
 */
class DeleteLogstoreResponse extends \Aliyun\Log\Models\Response\Response {
    
    /**
     * Aliyun_Log_Models_DeleteLogstoreResponse constructor
     *
     * @param array $resp
     *            DeleteLogstore HTTP response body
     * @param array $header
     *            DeleteLogstore HTTP response header
     */
    public function __construct($resp, $header) {
        parent::__construct ( $header );
    }
    
}
