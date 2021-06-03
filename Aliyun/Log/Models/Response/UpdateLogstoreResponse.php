<?php
namespace Aliyun\Log\Models\Response;

/**
 * The response of the UpdateLogstore API from log service.
 *
 * @author log service dev
 */
class UpdateLogstoreResponse extends \Aliyun\Log\Models\Response\Response {
    
    /**
     * Aliyun_Log_Models_UpdateLogstoreResponse constructor
     *
     * @param array $resp
     *            UpdateLogstore HTTP response body
     * @param array $header
     *            UpdateLogstore HTTP response header
     */
    public function __construct($resp, $header) {
        parent::__construct ( $header );
    }
    
}
