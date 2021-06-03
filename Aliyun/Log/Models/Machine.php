<?php

namespace Aliyun\Log\Models;

class Machine
{
    private $uuid;
    private $lastHeartbeatTime;
    private $info;
    private $status;
    private $createTime;
    private $lastModifyTime;
    public function __construct($uuid = \null, $lastHeartbeatTime = \null, $info = \null, $status = \null, $createTime = \null, $lastModifyTime = \null)
    {
        $this->uuid = $uuid;
        $this->lastHeartbeatTime = $lastHeartbeatTime;
        $this->info = $info;
        $this->status = $status;
        $this->createTime = $createTime;
        $this->lastModifyTime = $lastModifyTime;
    }
    public function getUuid()
    {
        return $this->uuid;
    }
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }
    public function getLastHeartbeatTime()
    {
        return $this->lastHeartbeatTime;
    }
    public function setLastHeartbeatTime($lastHeartbeatTime)
    {
        $this->lastHeartbeatTime = $lastHeartbeatTime;
    }
    public function getInfo()
    {
        return $this->info;
    }
    public function setInfo($info)
    {
        $this->info = $info;
    }
    public function getStatus()
    {
        return $this->status;
    }
    public function setStatus($status)
    {
        $this->status = $status;
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
        $resArr = array();
        if ($this->uuid !== \null) {
            $resArr['uuid'] = $this->uuid;
        }
        if ($this->lastHeartbeatTime !== \null) {
            $resArr['lastHeartbeatTime'] = $this->lastHeartbeatTime;
        }
        if ($this->info !== \null) {
            $resArr['info'] = $this->info->toArray();
        }
        if ($this->status !== \null) {
            $resArr['status'] = $this->status->toArray();
        }
        if ($this->createTime !== \null) {
            $resArr['createTime'] = $this->createTime;
        }
        if ($this->lastModifyTime !== \null) {
            $resArr['lastModifyTime'] = $this->lastModifyTime;
        }
        return $resArr;
    }
    public function setFromArray($resp)
    {
        $info = \null;
        if (isset($resp['info'])) {
            $ip = isset($resp['info']['ip']) ? $resp['info']['ip'] : \null;
            $os = isset($resp['info']['os']) ? $resp['info']['os'] : \null;
            $hostName = isset($resp['info']['hostName']) ? $resp['info']['hostName'] : \null;
            $info = new \Aliyun\Log\Models\MachineInfo($ip, $os, $hostName);
        }
        $status = \null;
        if (isset($resp['status'])) {
            $binaryCurVersion = isset($resp['status']['binaryCurVersion']) ? $resp['status']['binaryCurVersion'] : \null;
            $binaryDeployVersion = isset($resp['status']['binaryDeployVersion']) ? $resp['status']['binaryDeployVersion'] : \null;
            $status = new \Aliyun\Log\Models\MachineStatus($binaryCurVersion, $binaryDeployVersion);
        }
        $uuid = isset($resp['uuid']) ? $resp['uuid'] : \null;
        $lastHeartbeatTime = isset($resp['lastHeartbeatTime']) ? $resp['lastHeartbeatTime'] : \null;
        $createTime = isset($resp['createTime']) ? $resp['createTime'] : \null;
        $lastModifyTime = isset($resp['lastModifyTime']) ? $resp['lastModifyTime'] : \null;
        $this->setUuid($uuid);
        $this->setLastHeartbeatTime($lastHeartbeatTime);
        $this->setInfo($info);
        $this->setStatus($status);
        $this->setCreateTime($createTime);
        $this->setLastModifyTime($lastModifyTime);
    }
}
