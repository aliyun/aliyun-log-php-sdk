<?php
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */

require_once realpath(dirname(__FILE__) . '/Request.php');

/**
 * The request used to delete logstore from log service.
 *
 * @author log service dev
 */
class Aliyun_Log_Models_DeleteLogstoreRequest extends Aliyun_Log_Models_Request{

    private  $logstore;
    /**
     * Aliyun_Log_Models_DeleteLogstoreRequest constructor
     * 
     * @param string $project project name
     */
    public function __construct($project=null,$logstore = null) {
        parent::__construct($project);
        $this -> logstore = $logstore;
    }
    public function getLogstore()
    {
        return $this -> logstore;
    }
}
