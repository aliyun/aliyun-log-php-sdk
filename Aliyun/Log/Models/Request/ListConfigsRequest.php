<?php
namespace Aliyun\Log\Models\Request;

/**
 * 
 *
 * @author log service dev
 */
class ListConfigsRequest extends \Aliyun\Log\Models\Request\Request {
    
    private $configName;
    private $offset;
    private $size; 
    
    /**
     * Aliyun_Log_Models_ListConfigsRequest Constructor
     *
     */
    public function __construct($configName=null,$offset=null,$size=null) {
      //parent::__construct ( $project );
        $this->configName = $configName;
        $this->offset = $offset;
        $this->size = $size;
    }

    public function getConfigName(){
        return $this->configName;
    }

    public function setConfigName($configName){
        $this->configName = $configName;
    }
 
    public function getOffset(){
        return $this->offset;
    }

    public function setOffset($offset){
        $this->offset = $offset;
    }

    public function getSize(){
        return $this->size;
    }

    public function setSize($size){
        $this->size = $size;
    }
}
