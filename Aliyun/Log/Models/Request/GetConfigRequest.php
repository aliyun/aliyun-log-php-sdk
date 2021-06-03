<?php
namespace Aliyun\Log\Models\Request;

/**
 * 
 *
 * @author log service dev
 */
class GetConfigRequest extends \Aliyun\Log\Models\Request\Request {

    private $configName;

    /**
     * Aliyun_Log_Models_GetConfigRequest Constructor
     *
     */
    public function __construct($configName = null) {
        $this->configName = $configName;
    }

    public function getConfigName(){
      return $this->configName;
    }

    public function setConfigName($configName){
      $this->configName = $configName;
    }
    
}
