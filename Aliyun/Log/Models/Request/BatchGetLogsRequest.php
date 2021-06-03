<?php
namespace Aliyun\Log\Models\Request;

/**
 * The request used to get logs by logstore and shardId from log service.
 *
 * @author log service dev
 */
class BatchGetLogsRequest extends \Aliyun\Log\Models\Request\Request {
    
    /**
     * @var string logstore name
     */
    private $logstore;

    /**
     * @var string shard ID
     */
    private $shardId;
    
    /**
     * @var integer max line number of return logs
     */
    private $count;
    
    /**
     * @var string start cursor
     */
    private $cursor;

    /**
     * @var string end cursor
     */
    private $endCursor;
    
    /**
     * Aliyun_Log_Models_BatchGetLogsRequest Constructor
     *
     * @param string $project
     *            project name
     * @param string $logStore
     *            logstore name
     * @param string $shardId
     *            shard ID
     * @param integer $count
     *            return max loggroup numbers
     * @param string $cursor
     *            start cursor
     * @param string $end_cursor
     *            end cursor
     */
    public function __construct($project = null, $logstore = null, $shardId = null, $count = null, $cursor = null, $end_cursor = null) {
        parent::__construct ( $project );    
        $this->logstore = $logstore;
        $this->shardId = $shardId;
        $this->count = $count;
        $this->cursor = $cursor;
        $this->endCursor = $end_cursor;
    }
    
    /**
     * Get logstore name
     *
     * @return string logstore name
     */
    public function getLogstore() {
        return $this->logstore;
    }
    
    /**
     * Set logstore name
     *
     * @param string $logstore
     *            logstore name
     */
    public function setLogstore($logstore) {
        $this->logstore = $logstore;
    }
    
    /**
     * Get shard ID
     *
     * @return string shardId 
     */
    public function getShardId() {
        return $this->shardId;
    }
    
    /**
     * Set shard ID
     *
     * @param string $shardId
     *            shard ID
     */
    public function setShardId($shardId) {
        $this->shardId = $shardId;
    }
    
    /**
     * Get max return loggroup number
     *
     * @return integer count
     */
    public function getCount() {
        return $this->count;
    }
    
    /**
     * Set max return loggroup number
     *
     * @param integer $count
     *            max return loggroup number
     */
    public function setCount($count) {
        $this->count = $count;
    }
    
    /**
     * Get start cursor
     *
     * @return string cursor
     */
    public function getCursor() {
        return $this->cursor;
    }

    /**
     * Get end cursor
     *
     * @return string cursor
     */
    public function getEndCursor() {
        return $this->endCursor;
    }
    
    /**
     * Set start cursor
     *
     * @param string $cursor
     *            start cursor
     */
    public function setCursor($cursor) {
        $this->cursor = $cursor;
    }

    /**
     * Set end cursor
     *
     * @param string $cursor
     *            end cursor
     */
    public function setEndCursor($cursor) {
        $this->endCursor = $cursor;
    }
}
