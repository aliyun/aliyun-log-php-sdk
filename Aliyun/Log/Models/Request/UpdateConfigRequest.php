<?php
namespace Aliyun\Log\Models\Request;

/**
 * 
 *
 * @author log service dev
 */
class UpdateConfigRequest extends \Aliyun\Log\Models\Request\Request {

    private $config;
    /**
     * Aliyun_Log_Models_UpdateConfigRequest Constructor
     *
     */
    public function __construct($config) {
        $this->config = $config;
    }

    public function getConfig(){
        return $this->config;
    }

    public function setConfig($config){
        $this->config = $config;
    }
    
}
