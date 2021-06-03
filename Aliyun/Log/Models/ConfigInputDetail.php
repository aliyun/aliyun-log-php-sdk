<?php

namespace Aliyun\Log\Models;

/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */
class ConfigInputDetail
{
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
    public function __construct($filePattern = '', $key = array(), $localStorage = \true, $logBeginRegex = '', $logPath = '', $logType = '', $regex = '', $timeFormat = '', $filterRegex = array(), $filterKey = array(), $topicFormat = 'none')
    {
        $this->filePattern = $filePattern;
        $this->key = $key;
        $this->localStorage = $localStorage;
        $this->logBeginRegex = $logBeginRegex;
        $this->logPath = $logPath;
        $this->logType = $logType;
        $this->regex = $regex;
        $this->timeFormat = $timeFormat;
        $this->filterRegex = $filterRegex;
        $this->filterKey = $filterKey;
        $this->topicFormat = $topicFormat;
    }
    public function toArray()
    {
        $resArray = array();
        if ($this->filePattern !== \null) {
            $resArray['filePattern'] = $this->filePattern;
        }
        if ($this->key !== \null) {
            $resArray['key'] = $this->key;
        }
        if ($this->localStorage !== \null) {
            $resArray['localStorage'] = $this->localStorage;
        }
        if ($this->logBeginRegex !== \null) {
            $resArray['logBeginRegex'] = $this->logBeginRegex;
        }
        if ($this->logPath !== \null) {
            $resArray['logPath'] = $this->logPath;
        }
        if ($this->logType !== \null) {
            $resArray['logType'] = $this->logType;
        }
        if ($this->regex !== \null) {
            $resArray['regex'] = $this->regex;
        }
        if ($this->timeFormat !== \null) {
            $resArray['timeFormat'] = $this->timeFormat;
        }
        if ($this->filterRegex !== \null) {
            $resArray['filterRegex'] = $this->filterRegex;
        }
        if ($this->filterKey !== \null) {
            $resArray['filterKey'] = $this->filterKey;
        }
        if ($this->topicFormat !== \null) {
            $resArray['topicFormat'] = $this->topicFormat;
        }
        return $resArray;
    }
}
