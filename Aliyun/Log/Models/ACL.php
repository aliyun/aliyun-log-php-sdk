<?php
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */

class Aliyun_Log_Models_ACL {
    private $principleType;
    private $principleId;
    private $object;
    private $privilege;
    private $aclId;

    private $createTime;
    private $lastModifyTime;

    public function __construct($principleType='',$principleId='',$object='',
            $privilege=array(),$aclId=null,$createTime=null,$lastModifyTime=null) {
        $this->principleType = $principleType;
        $this->principleId = $principleId;
        $this->object = $object;
        $this->privilege = $privilege;
        $this->aclId = $aclId;

        $this->createTime = $createTime;
        $this->lastModifyTime = $lastModifyTime;
    }
     
    public function getPrincipleType(){
        return $this->principleType;
    }
    public function setPrincipleType($principleType){
        $this->principleType = $principleType;
    }
    
    public function getPrincipleId(){
        return $this->principleId;
    }
    public function setPrincipleId($principleId){
        $this->principleId = $principleId;
    }

    public function getObject(){
        return $this->object;
    }
    public function setObject($object){
        $this->object = $object;
    }
    public function getPrivilege(){
        return $this->privilege;
    }
    public function setPrivilege($privilege){
        $this->privilege = $privilege;
    }
    public function getAclId(){
        return $this->aclId;
    }
    public function setAclId($aclId){
        $this->aclId = $aclId;
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
      if($this->principleType!==null)
        $format_array['principleType'] = $this->principleType;
      if($this->principleId!==null)
        $format_array['principleId'] = $this->principleId;
      if($this->object!==null)
        $format_array['object'] = $this->object;
      if($this->privilege!==null)
        $format_array['privilege'] = $this->privilege;
      if($this->aclId!==null)
        $format_array['aclId'] = $this->aclId;
      if($this->createTime!==null)
        $format_array['createTime'] = $this->createTime;
      if($this->lastModifyTime!==null)
        $format_array['lastModifyTime'] = $this->lastModifyTime;
      return $format_array;
    }

    public function setFromArray($resp){
        $principleType = ($resp['principleType']!==null)?$resp['principleType']:null;
        $principleId = ($resp['principleId']!==null)?$resp['principleId']:null;
        $object = ($resp['object']!==null)?$resp['object']:null;
        $privilege = ($resp['privilege']!==null)?$resp['privilege']:array();
        $aclId = ($resp['aclId']!==null)?$resp['aclId']:null;
        $createTime = ($resp['createTime']!==null)?$resp['createTime']:null;
        $lastModifyTime = ($resp['lastModifyTime']!==null)?$resp['lastModifyTime']:null;

        $this->setPrincipleType($principleType);
        $this->setPrincipleId($principleId);
        $this->setObject($object);
        $this->setPrivilege($privilege);
        $this->setAclId($aclId);
        $this->setCreateTime($createTime);
        $this->setLastModifyTime($lastModifyTime);
    }
}
