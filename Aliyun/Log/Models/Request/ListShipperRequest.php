<?php
namespace Aliyun\Log\Models\Request;

class ListShipperRequest extends \Aliyun\Log\Models\Request\Request{
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
