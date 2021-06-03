<?php
namespace Aliyun\Log\Models\Response;

/**
 * The response of the PutLogs API from log service.
 *
 * @author log service dev
 */
class PutLogsResponse extends \Aliyun\Log\Models\Response\Response {
    /**
     * Aliyun_Log_Models_PutLogsResponse constructor
     *
     * @param array $header
     *            PutLogs HTTP response header
     */
    public function __construct($headers) {
        parent::__construct ( $headers );
    }
}
