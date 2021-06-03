<?php

namespace Aliyun\Log\Models\Response;

/**
 * The response of the ListTopics API from log service.
 *
 * @author log service dev
 */
class ListTopicsResponse extends \Aliyun\Log\Models\Response\Response {

    /**
     * @var integer the number of all the topics from the response
     */
    private $count;

    /**
     * @var array topics list
     */
    private $topics;

    /**
     * @var string/null the next token from the response. If there is no more topic to list, it will return None
     */
    private $nextToken;
    
    /**
     * Aliyun_Log_Models_ListTopicsResponse constructor
     *
     * @param array $resp
     *            ListTopics HTTP response body
     * @param array $header
     *            ListTopics HTTP response header
     */
    public function __construct($resp, $header) {
        parent::__construct ( $header );
        $this->count = $header['x-log-count'];
        $this->topics = $resp ;
        $this->nextToken = isset ( $header['x-log-nexttoken'] ) ? $header['x-log-nexttoken'] : NULL;
    }
    
    /**
     * Get the number of all the topics from the response
     *
     * @return integer the number of all the topics from the response
     */
    public function getCount() {
        return $this->count;
    }
    
    /**
     * Get all the topics from the response
     *
     * @return array topics list
     */
    public function getTopics() {
        return $this->topics;
    }
    
    /**
     * Return the next token from the response. If there is no more topic to list, it will return None
     *
     * @return string/null next token used to list more topics
     */
    public function getNextToken() {
        return $this->nextToken;
    }
}
