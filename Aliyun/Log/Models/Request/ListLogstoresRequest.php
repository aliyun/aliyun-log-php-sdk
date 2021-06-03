<?php
namespace Aliyun\Log\Models\Request;

/**
 * The request used to list logstore from log service.
 *
 * @author log service dev
 */
class ListLogstoresRequest extends \Aliyun\Log\Models\Request\Request{
    
    /**
     * Aliyun_Log_Models_ListLogstoresRequest constructor
     * 
     * @param string $project project name
     */
    public function __construct($project=null) {
        parent::__construct($project);
    }
}
