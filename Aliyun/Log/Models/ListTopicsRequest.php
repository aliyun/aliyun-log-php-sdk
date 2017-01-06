<?php
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */

require_once realpath(dirname(__FILE__) . '/Request.php');

/**
 * The Request used to list topics from log service.
 *
 * @author log service dev
 */
class Aliyun_Log_Models_ListTopicsRequest extends Aliyun_Log_Models_Request {
    
    /**
     * @var string $logstore logstore name
     */
    private $logstore;

    /**
     * @var string $token the start token to list topics
     */
    private $token;

    /**
     * @var integer $line max topic counts to return
     */
    private $line;
    
    /**
     * Aliyun_Log_Models_ListTopicsRequest constructor
     * 
     * @param string $project project name
     * @param string $logstore logstore name
     * @param string $token the start token to list topics
     * @param integer $line max topic counts to return
     */
    public function __construct($project=null, $logstore=null, $token=null, $line=null) {
        parent::__construct($project);
        $this->logstore = $logstore;
        $this->token = $token;
        $this->line = $line;
    }

    /**
     * Get logstroe name
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
     * Get start token to list topics
     * 
     * @return string start token to list topics
     */
    public function getToken() {
        return $this->token;
    }
    
    /**
     * Set start token to list topics
     * 
     * @param string $token start token to list topics
     */
    public function setToken($token) {
        $this->token = $token;
    }
    
    
    /**
     * Get max topic counts to return
     * 
     * @return integer max topic counts to return
     */
    public function getLine() {
        return $this->line;
    }
    
    /**
     * Set max topic counts to return
     * 
     * @param integer $line max topic counts to return
     */
    public function setLine($line) {
        $this->line = $line;
    }
}
