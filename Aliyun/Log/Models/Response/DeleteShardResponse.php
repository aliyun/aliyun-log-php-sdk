<?php
namespace Aliyun\Log\Models\Response;

/**
 * The response of the DeleteShard API from log service.
 *
 * @author log service dev
 */
class DeleteShardResponse extends \Aliyun\Log\Models\Response\Response {
    /**
     * Aliyun_Log_Models_DeleteShardResponse constructor
     *
     * @param array $header
     *            DeleteShard HTTP response header
     */
    public function __construct($headers) {
        parent::__construct ( $headers );
    }
}
