<?php

namespace Aliyun\Log\Models;

class Config
{
    private $configName;
    private $inputType;
    private $inputDetail;
    private $outputType;
    private $outputDetail;
    private $createTime;
    private $lastModifyTime;
    public function __construct($configName = '', $inputType = '', $inputDetail = \null, $outputType = '', $outputDetail = \null, $createTime = \null, $lastModifyTime = \null)
    {
        $this->configName = $configName;
        $this->inputType = $inputType;
        $this->inputDetail = $inputDetail;
        $this->outputType = $outputType;
        $this->outputDetail = $outputDetail;
        $this->createTime = $createTime;
        $this->lastModifyTime = $lastModifyTime;
    }
    public function getConfigName()
    {
        return $this->configName;
    }
    public function setConfigName($configName)
    {
        $this->configName = $configName;
    }
    public function getInputType()
    {
        return $this->inputType;
    }
    public function setInputType($inputType)
    {
        $this->inputType = $inputType;
    }
    public function getInputDetail()
    {
        return $this->inputDetail;
    }
    public function setInputDetail($inputDetail)
    {
        $this->inputDetail = $inputDetail;
    }
    public function getOutputType()
    {
        return $this->outputType;
    }
    public function setOutputType($outputType)
    {
        $this->outputType = $outputType;
    }
    public function getOutputDetail()
    {
        return $this->outputDetail;
    }
    public function setOutputDetail($outputDetail)
    {
        $this->outputDetail = $outputDetail;
    }
    public function getCreateTime()
    {
        return $this->createTime;
    }
    public function setCreateTime($createTime)
    {
        $this->createTime = $createTime;
    }
    public function getLastModifyTime()
    {
        return $this->lastModifyTime;
    }
    public function setLastModifyTime($lastModifyTime)
    {
        $this->lastModifyTime = $lastModifyTime;
    }
    public function toArray()
    {
        $format_array = array();
        if ($this->configName !== \null) {
            $format_array['configName'] = $this->configName;
        }
        if ($this->inputType !== \null) {
            $format_array['inputType'] = $this->inputType;
        }
        if ($this->inputDetail !== \null) {
            $format_array['inputDetail'] = $this->inputDetail->toArray();
        }
        if ($this->outputType !== \null) {
            $format_array['outputType'] = $this->outputType;
        }
        if ($this->outputDetail !== \null) {
            $format_array['outputDetail'] = $this->outputDetail->toArray();
        }
        if ($this->createTime !== \null) {
            $format_array['createTime'] = $this->createTime;
        }
        if ($this->lastModifyTime !== \null) {
            $format_array['lastModifyTime'] = $this->lastModifyTime;
        }
        return $format_array;
    }
    public function setFromArray($resp)
    {
        $inputDetail = new \Aliyun\Log\Models\ConfigInputDetail();
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
        $outputDetail = new \Aliyun\Log\Models\ConfigOutputDetail();
        $outputDetail->projectName = $resp['outputDetail']['projectName'];
        $outputDetail->logstoreName = $resp['outputDetail']['logstoreName'];
        $configName = $resp['configName'];
        $inputType = $resp['inputType'];
        $outputType = $resp['outputType'];
        $this->setConfigName($configName);
        $this->setInputType($inputType);
        $this->setInputDetail($inputDetail);
        $this->setOutputType($outputType);
        $this->setOutputDetail($outputDetail);
    }
}
