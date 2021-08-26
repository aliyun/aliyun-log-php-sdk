<?php
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */

require_once realpath(dirname(__FILE__) . '/Response.php');
require_once realpath(dirname(__FILE__) . '/SqlInstance.php');

/**
 * The response of the ListSqlInstance API from log service.
 *
 * @author log service dev
 */
class Aliyun_Log_Models_ListSqlInstanceResponse extends Aliyun_Log_Models_Response {

    private $sqlInstances;
    /**
     * Aliyun_Log_Models_ListSqlInstanceResponse constructor
     *
     * @param array $resp
     *            ListSqlInstance HTTP response body
     * @param array $header
     *            ListSqlInstance HTTP response header
     */
    public function __construct($resp, $header) {
        parent::__construct ( $header );
        $arr = $resp;
        if($arr != null)
        {
            foreach($arr as $data)
            {
                $name = $data["name"];
                $cu = $data["cu"];
                $createTime = $data["createTime"];
                $updateTime = $data["updateTime"];
                $this -> sqlInstances [] = new Aliyun_Log_Models_SqlInstance($name,$cu,$createTime,$updateTime);
            }
        }
    }
    
}
