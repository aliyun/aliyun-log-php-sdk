<?php
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */

class Aliyun_Log_Models_MachineGroup_GroupAttribute {
    public $externalName;
    public $groupTopic;
    public function __construct($externalName=null,$groupTopic=null){
      $this->externalName = $externalName;
      $this->groupTopic = $groupTopic;
    }
    public function toArray(){
      $resArray = array();
      if($this->externalName!==null)
        $resArray['externalName'] = $this->externalName;
      if($this->groupTopic!==null)
        $resArray['groupTopic'] = $this->groupTopic;
      return $resArray;
    }
}

class Aliyun_Log_Models_MachineGroup {
    private $groupName;
    private $groupType;
    private $groupAttribute;
    private $machineList;

    private $createTime;
    private $lastModifyTime;

    public function __construct($groupName='',$groupType='',$groupAttribute=null,
            $machineList=null,$createTime=null,$lastModifyTime=null) {
        $this->groupName = $groupName;
        $this->groupType = $groupType;
        $this->groupAttribute = $groupAttribute;
        $this->machineList = $machineList;
        $this->createTime = $createTime;
        $this->lastModifyTime = $lastModifyTime;
    }
    
    public function getGroupName(){
        return $this->groupName;
    }
    public function setGroupName($groupName){
        $this->groupName = $groupName;
    }
    public function getGroupType(){
        return $this->groupType;
    }
    public function setGroupType($groupType){
        $this->groupType = $groupType;
    }
    public function getGroupAttribute(){
        return $this->groupAttribute;
    }
    public function setGroupAttribute($groupAttribute){
        $this->groupAttribute = $groupAttribute;
    }
    public function getMachineList(){
        return $this->machineList;
    }
    public function setMachineList($machineList){
        $this->machineList = $machineList;
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
      if($this->groupName!==null)
        $format_array['groupName'] = $this->groupName;
      if($this->groupType!==null)
        $format_array['groupType'] = $this->groupType;
      if($this->groupAttribute!==null)
        $format_array['groupAttribute'] = $this->groupAttribute->toArray();
      if($this->machineList!==null){
        $mlArr = array();
        foreach($this->machineList as $value){
            $mlArr[] = $value->toArray();
        }
        $format_array['machineList'] = $mlArr;
      }  
      if($this->createTime!==null)
        $format_array['createTime'] = $this->createTime;
      if($this->lastModifyTime!==null)
        $format_array['lastModifyTime'] = $this->lastModifyTime;
      return $format_array;
    }

    public function setFromArray($resp){
        $groupAttribute = null;
        if(isset($resp['groupAttribute'])){
            $groupAttributeArr = $resp['groupAttribute'];
            $groupAttribute = new Aliyun_Log_Models_MachineGroup_GroupAttribute();
            if(isset($groupAttributeArr['externalName']))
              $groupAttribute->externalName = $groupAttributeArr['externalName'];
            if(isset($groupAttributeArr['groupTopic']))
              $groupAttribute->groupTopic = $groupAttributeArr['groupTopic'];
        }
        $groupName = ($resp['groupName']!==null)?$resp['groupName']:null;
        $groupType = ($resp['groupType']!==null)?$resp['groupType']:null;
        $machineList = array();
        if(isset($resp['machineList']) && is_array($resp['machineList']) && count($resp['machineList'])>0){
          foreach($resp['machineList'] as $value){
            $machine = new Aliyun_Log_Models_Machine();
            $machine->setFromArray($value);
            $machineList[] = $machine;
          }
        }

        $createTime = ($resp['createTime']!==null)?$resp['createTime']:null;
        $lastModifyTime = ($resp['lastModifyTime']!==null)?$resp['lastModifyTime']:null;
        $this->setGroupName($groupName);
        $this->setGroupType($groupType);
        $this->setGroupAttribute($groupAttribute);
        $this->setMachineList($machineList);
        $this->setCreateTime($createTime);
        $this->setLastModifyTime($lastModifyTime);
    }
}
