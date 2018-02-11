<?php
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */

require_once realpath(dirname(__FILE__) . '/Request.php');

class Aliyun_Log_Models_ListShipperRequest extends Aliyun_Log_Models_Request{
    private $logStore;

    /**
     * Aliyun_Log_Models_CreateShipperRequest Constructor
     *
     */
    public function __construct($project) {
        parent::__construct ( $project );
    }

    /**
     * @return mixed
     */
    public function getLogStore()
    {
        return $this->logStore;
    }

    /**
     * @param mixed $logStore
     */
    public function setLogStore($logStore)
    {
        $this->logStore = $logStore;
    }


}