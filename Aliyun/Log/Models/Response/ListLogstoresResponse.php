<?php
namespace Aliyun\Log\Models\Response;

/**
 * The response of the ListLogstores API from log service.
 *
 * @author log service dev
 */
class ListLogstoresResponse extends \Aliyun\Log\Models\Response\Response {
    
    /**
     * @var integer the number of total logstores from the response
     */
    private $count;
    
    /**
     * @var array all logstore
     */
    private $logstores;
    
    /**
     * Aliyun_Log_Models_ListLogstoresResponse constructor
     *
     * @param array $resp
     *            ListLogstores HTTP response body
     * @param array $header
     *            ListLogstores HTTP response header
     */
    public function __construct($resp, $header) {
        parent::__construct ( $header );
        $this->count = $resp ['total'];
        $this->logstores = $resp ['logstores'];
    }
    
    /**
     * Get total count of logstores from the response
     *
     * @return integer the number of total logstores from the response
     */
    public function getCount() {
        return $this->count;
    }
    
    /**
     * Get all the logstores from the response
     *
     * @return array all logstore
     */
    public function getLogstores() {
        return $this->logstores;
    }
}
