<?php
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */

require_once realpath(dirname(__FILE__) . '/Request.php');

/**
 * The request used to execute logstore sql by a query from log service.
 *
 * @author log service dev
 */
class Aliyun_Log_Models_LogStoreSqlRequest extends Aliyun_Log_Models_Request {
    
    /**
     * @var string logstore name
     */
    private $logstore;

    /**
     * @var integer the begin time
     */
    private $from;
    
    /**
     * @var integer the end time
     */
    private $to;
    
    /**
     * @var string user defined query
     */
    private $query;
    
    /**
     *
     * @var bool if power sql is true, then the query will be run with powered instance, which can handle large amountof data
     */
    private $powerSql;

    
    /**
     * Aliyun_Log_Models_GetLogsRequest Constructor
     *
     * @param string $project
     *            project name
     * @param string $logStore
     *            logstore name
     * @param integer $from
     *            the begin time
     * @param integer $to
     *            the end time
     * @param string $query
     *            user defined query
     * @param bool $powerSql
     *            whether use power sql to make sql faster
     */
    public function __construct($project = null, $logstore = null, $from = null, $to = null, $topic = null, $query = null, $line = null, $offset = null, $reverse = null,$powerSql = null) {
        parent::__construct ( $project );
        
        $this->logstore = $logstore;
        $this->from = $from;
        $this->to = $to;
        $this->topic = $topic;
        $this->query = $query;
        $this->powerSql = $powerSql;
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
     * Get begin time
     *
     * @return integer begin time
     */
    public function getFrom() {
        return $this->from;
    }
    
    /**
     * Set begin time
     *
     * @param integer $from
     *            begin time
     */
    public function setFrom($from) {
        $this->from = $from;
    }
    
    /**
     * Get end time
     *
     * @return integer end time
     */
    public function getTo() {
        return $this->to;
    }
    
    /**
     * Set end time
     *
     * @param integer $to
     *            end time
     */
    public function setTo($to) {
        $this->to = $to;
    }
    
    /**
     * Get user defined query
     *
     * @return string user defined query
     */
    public function getQuery() {
        return $this->query;
    }
    
    /**
     * Set user defined query
     *
     * @param string $query
     *            user defined query
     */
    public function setQuery($query) {
        $this->query = $query;
    }
    
    /**
     * Get request powerSql flag
     *
     * @reutnr bool powerSql flag
     */
    public function getPowerSql() {
        return $this -> powerSql;
    }

    /**
     *  Set request powerSql flag
     *
     *  @param bool $powerSql
     *               powerSql flag
     *
     */
    public function setPowerSql($powerSql)
    {
        $this -> powerSql = $powerSql;
    }
}
