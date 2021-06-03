<?php
namespace Aliyun\Log\Models\Response;

/**
 * The response of the GetCursor API from log service.
 *
 * @author log service dev
 */
class GetCursorResponse extends \Aliyun\Log\Models\Response\Response {
    /**
     * @var string cursor
     *
     */
    private $cursor;
    /**
     * Aliyun_Log_Models_GetCursorResponse constructor
     *
     * @param array $resp
     *            GetLogs HTTP response body
     * @param array $header
     *            GetLogs HTTP response header
     */
    public function __construct($resp, $header) {
        parent::__construct ( $header );
        $this->cursor = $resp['cursor'];
    }
    
    /**
     * Get cursor from the response
     *
     * @return string cursor
     */
    public function getCursor(){
      return $this->cursor;
    } 
}
