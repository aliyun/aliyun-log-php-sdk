<?php
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */

class Aliyun_Log_Models_Config_InputDetail {
  public $filePattern;
  public $key;
  public $localStorage;
  public $logBeginRegex;
  public $logPath;
  public $logType;
  public $regex;
  public $timeFormat;
  public $filterRegex;
  public $filterKey;
  public $topicFormat;
  
  public function __construct($filePattern='',$key=array(),$localStorage=true,
    $logBeginRegex='',$logPath='',$logType='',$regex='',
    $timeFormat='',$filterRegex=array(),$filterKey=array(),$topicFormat='none'){
    $this->filePattern=$filePattern;
    $this->key=$key;
    $this->localStorage=$localStorage;
    $this->logBeginRegex=$logBeginRegex;
    $this->logPath=$logPath;
    $this->logType=$logType;
    $this->regex=$regex;
    $this->timeFormat=$timeFormat;
    $this->filterRegex=$filterRegex;
    $this->filterKey=$filterKey;
    $this->topicFormat=$topicFormat;
  }

  public function toArray(){
    $resArray = array();
    if($this->filePattern!==null)
        $resArray['filePattern'] = $this->filePattern;
    if($this->key!==null)
        $resArray['key'] = $this->key;
    if($this->localStorage!==null)
        $resArray['localStorage'] = $this->localStorage;
    if($this->logBeginRegex!==null)
        $resArray['logBeginRegex'] = $this->logBeginRegex;
    if($this->logPath!==null)
        $resArray['logPath'] = $this->logPath;
    if($this->logType!==null)
        $resArray['logType'] = $this->logType;
    if($this->regex!==null)
        $resArray['regex'] = $this->regex;
    if($this->timeFormat!==null)
        $resArray['timeFormat'] = $this->timeFormat;
    if($this->filterRegex!==null)
        $resArray['filterRegex'] = $this->filterRegex;
    if($this->filterKey!==null)
        $resArray['filterKey'] = $this->filterKey;
    if($this->topicFormat!==null)
        $resArray['topicFormat'] = $this->topicFormat;
    return $resArray;
  }
}

class Aliyun_Log_Models_Config_OutputDetail {
    public $projectName;
    public $logstoreName;

    public function __construct($projectName='',$logstoreName=''){
      $this->projectName = $projectName;
      $this->logstoreName = $logstoreName;
    }
    public function toArray(){
      $resArray = array();
      if($this->projectName!==null)
        $resArray['projectName'] = $this->projectName;
      if($this->logstoreName!==null)
        $resArray['logstoreName'] = $this->logstoreName;
      return $resArray;
    }
}

class Aliyun_Log_Models_Config {

    private $configName;
    private $inputType;
    private $inputDetail;
    private $outputType;
    private $outputDetail;
    
    private $createTime;
    private $lastModifyTime;

    public function __construct($configName='',$inputType='',$inputDetail=null,
            $outputType='',$outputDetail=null,$createTime=null,$lastModifyTime=null) {
        $this->configName = $configName;
        $this->inputType = $inputType;
        $this->inputDetail = $inputDetail;
        $this->outputType = $outputType;
        $this->outputDetail = $outputDetail;
        $this->createTime = $createTime;
        $this->lastModifyTime = $lastModifyTime;

    }
    
    public function getConfigName(){
        return $this->configName;
    }
    public function setConfigName($configName){
        $this->configName = $configName;
    }
    public function getInputType(){
        return $this->inputType;
    }
    public function setInputType($inputType){
        $this->inputType = $inputType;
    }
    public function getInputDetail(){
        return $this->inputDetail;
    }
    public function setInputDetail($inputDetail){
        $this->inputDetail = $inputDetail;
    }
    public function getOutputType(){
        return $this->outputType;
    }
    public function setOutputType($outputType){
        $this->outputType = $outputType;
    }    
    public function getOutputDetail(){
        return $this->outputDetail;
    }
    public function setOutputDetail($outputDetail){
        $this->outputDetail = $outputDetail;
    }
    public function getCreateTime(){
        return $this->createTime;
    }
    public function setCreateTime($createTime){
        $this->createTime = $createTime;
    }

    public function getLastModifyTime(){
        return $this->lastModifyTime;
    }
    public function setLastModifyTime($lastModifyTime){
        $this->lastModifyTime = $lastModifyTime;
    }

    public function toArray(){
      $format_array = array();
      if($this->configName!==null)
        $format_array['configName'] = $this->configName;
      if($this->inputType!==null)
        $format_array['inputType'] = $this->inputType;
      if($this->inputDetail!==null)
        $format_array['inputDetail'] = $this->inputDetail->toArray();
      if($this->outputType!==null)
        $format_array['outputType'] = $this->outputType;
      if($this->outputDetail!==null)
        $format_array['outputDetail'] = $this->outputDetail->toArray();
      if($this->createTime!==null)
        $format_array['createTime'] = $this->createTime;
      if($this->lastModifyTime!==null)
        $format_array['lastModifyTime'] = $this->lastModifyTime;
      return $format_array;
    }

    public function setFromArray($resp){
        $inputDetail = new Aliyun_Log_Models_Config_InputDetail();
        $inputDetail->filePattern = $resp['inputDetail']['filePattern'];
        $inputDetail->key = $resp['inputDetail']['key'];
        $inputDetail->localStorage = $resp['inputDetail']['localStorage'];
        $inputDetail->logBeginRegex = $resp['inputDetail']['logBeginRegex'];
        $inputDetail->logPath = $resp['inputDetail']['logPath'];
        $inputDetail->logType = $resp['inputDetail']['logType'];
        $inputDetail->regex = $resp['inputDetail']['regex'];
        $inputDetail->timeFormat = $resp['inputDetail']['timeFormat'];
        $inputDetail->filterRegex = $resp['inputDetail']['filterRegex'];
        $inputDetail->filterKey = $resp['inputDetail']['filterKey'];
        $inputDetail->topicFormat = $resp['inputDetail']['topicFormat'];

        $outputDetail = new Aliyun_Log_Models_Config_OutputDetail();
        $outputDetail->projectName = $resp['outputDetail']['projectName'];
        $outputDetail->logstoreName = $resp['outputDetail']['logstoreName'];

        $configName=$resp['configName'];
        $inputType=$resp['inputType'];
        $outputType=$resp['outputType'];

        $this->setConfigName($configName);
        $this->setInputType($inputType);
        $this->setInputDetail($inputDetail);
        $this->setOutputType($outputType);
        $this->setOutputDetail($outputDetail);

    }

}
