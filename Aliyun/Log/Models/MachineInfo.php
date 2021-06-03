<?php

namespace Aliyun\Log\Models;

/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */
class MachineInfo
{
    public $ip;
    public $os;
    public $hostName;
    public function __construct($ip = \null, $os = \null, $hostName = \null)
    {
        $this->ip = $ip;
        $this->os = $os;
        $this->hostName = $hostName;
    }
    public function getIp()
    {
        return $this->ip;
    }
    public function setIp($ip)
    {
        $this->ip = $ip;
    }
    public function getOs()
    {
        return $this->os;
    }
    public function setOs($os)
    {
        $this->os = $os;
    }
    public function getHostName()
    {
        return $this->hostName;
    }
    public function setHostName($hostname)
    {
        $this->hostName = $hostName;
    }
    public function toArray()
    {
        $resArr = array();
        if ($this->ip !== \null) {
            $resArr['ip'] = $this->ip;
        }
        if ($this->os !== \null) {
            $resArr['os'] = $this->os;
        }
        if ($this->hostName !== \null) {
            $resArr['hostName'] = $this->hostName;
        }
        return $resArr;
    }
}
