<?php
/**
 * this class is used to represent the sql instance detail
 * for each sql instance, it contains name, cu, create time,update time
 * @author yunlei 
 */

class Aliyun_Log_Models_SqlInstance{

    /**
     * @var string name
     */
    private $name;

    /**
     * @var integer cu
     */
    private $cu;

    /**
     * @var integer createTime
     */
    private $createTime;

    /**
     * @var integer updateTime
     */
    private $updateTime;
    /**
     * Aliyun_Log_Models_SqlInstance constructor
     * @param string $name
     *                the name
     * @param integer $cu
     *                  cu 
     * @param integer createTime
     *                  create time
     * @param integer updateTime
     *                  update time
     */
    public function __construct($name, $cu,$createTime, $updateTime)
    {
        $this -> name = $name;
        $this -> cu = $cu;
        $this -> createTime = $createTime;
        $this -> updateTime = $updateTime;
    }
    public function getName(){
        return $this -> name;
    }
    public function getCu(){
        return $this -> cu;
    }
    public function getCreateTime(){
        return $this -> createTime;
    }
    public function getUpdateTime(){
        return $this -> updateTime;
    }
}
