<?php
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */

require_once realpath(dirname(__FILE__) . '/Response.php');
require_once realpath(dirname(__FILE__) . '/QueriedLog.php');

/**
 * The response of the execute sql API from log service.
 *
 * @author log service dev
 */
class Aliyun_Log_Models_ProjectSqlResponse extends Aliyun_Log_Models_Response {
    
    /**
     * @var integer log number
     */
    private $count;

    /**
     * @var string logs query status(Complete or InComplete)
     */
    private $progress;

    /**
     * @var array Aliyun_Log_Models_QueriedLog array, all log data
     */
    private $logs;

    /**
     * @var rows proccesed in this request
     */
    private $processedRows;

    /**
     * @var execution latency in milliseconds
     */
    private $elapsedMilli;

    /**
     * @var used cpu sec for this request
     */
    private $cpuSec;

    /**
     * @var used cpu core number for this request
     */
    private $cpuCores;
    
    
    /**
     * Aliyun_Log_Models_GetLogsResponse constructor
     *
     * @param array $resp
     *            GetLogs HTTP response body
     * @param array $header
     *            GetLogs HTTP response header
     */
    public function __construct($resp, $header) {
        parent::__construct ( $header );
        $this->count = $header['x-log-count'];
        $this->progress = $header ['x-log-progress'];
        $this->processedRows = $header['x-log-processed-rows'];
        $this->elapsedMilli = $header['x-log-elapsed-millisecond'];
        $this->cpuSec = $header['x-log-cpu-sec'];
        $this->cpuCores = $header['x-log-cpu-cores'];
        $this->logs = array ();
        foreach ( $resp  as $data ) {
            $contents = $data;
            $time = $data ['__time__'];
            $source = $data ['__source__'];
            unset ( $contents ['__time__'] );
            unset ( $contents ['__source__'] );
            $this->logs [] = new Aliyun_Log_Models_QueriedLog ( $time, $source, $contents );
        }
    }
    
    /**
     * Get log number from the response
     *
     * @return integer log number
     */
    public function getCount() {
        return $this->count;
    }
    
    /**
     * Check if the get logs query is completed
     *
     * @return bool true if this logs query is completed
     */
    public function isCompleted() {
        return $this->progress == 'Complete';
    }
    
    /**
     * Get all logs from the response
     *
     * @return array Aliyun_Log_Models_QueriedLog array, all log data
     */
    public function getLogs() {
        return $this->logs;
    }

    /**
     * get proccesedRows
     */
    public function getProcessedRows()
    {
        return $this ->processedRows;
    }

    /**
     * get elapsedMilli
     */
    public function getElapsedMilli()
    {
        return $this -> elapsedMilli;
    }

    /**
     * get cpuSec
     */
    public function getCpuSec()
    {
        return $this->cpuSec;
    }

    /**
     * get cpuCores
     */
    public function getCpuCores()
    {
        return $this-> cpuCores;
    }
}

