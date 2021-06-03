<?php
namespace Aliyun\Log\Models\Request;

/**
 * 
 *
 * @author log service dev
 */
class ListShardsRequest extends \Aliyun\Log\Models\Request\Request {

    private $logstore;

    /**
     * Aliyun_Log_Models_ListShardsRequest Constructor
     *
     */
    public function __construct($project,$logstore) {
        parent::__construct ( $project );
        $this->logstore = $logstore;
    }

    public function getLogstore(){
      return $this->logstore;
    }

    public function setLogstore($logstore){
      $this->logstore = $logstore;
    }
    
    
}
