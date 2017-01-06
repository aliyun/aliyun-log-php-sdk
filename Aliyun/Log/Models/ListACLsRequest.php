<?php
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */

require_once realpath(dirname(__FILE__) . '/Request.php');

/**
 * 
 *
 * @author log service dev
 */
class Aliyun_Log_Models_ListACLsRequest extends Aliyun_Log_Models_Request {

    private $offset;
    private $size;
    private $principleId;

    /**
     * Aliyun_Log_Models_ListACLsRequest Constructor
     *
     */
    public function __construct($principleId=null,$offset=null,$size=null) {
        $this->offset = $offset;
        $this->size = $size;
        $this->principleId = $principleId;
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
    
    public function getPrincipleId(){
        return $this->principleId;
    }
    public function setPrincipleId($principleId){
        $this->principleId = $principleId;
    }

}
