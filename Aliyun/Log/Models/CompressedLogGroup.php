<?php
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */

/**
 * Aliyun_Log_Models_CompressedLogGroup is compressed LogGroup, 
 * LogGroup infomation please refer to Aliyun_Log_Models_LogGroup
 *
 * @author log service dev
 */
class Aliyun_Log_Models_CompressedLogGroup {

    /**
     * @var integer uncompressed LogGroup size
     *
     */
    protected $uncompressedSize;

    /**
     * @var integer uncompressed LogGroup size
     *
     */
    protected $compressedData;


    public function __construct($time = null, $contents = null) {
        if (! $time)
            $time = time ();
        $this->time = $time;
        if ($contents)
            $this->contents = $contents;
        else
            $this->contents = array ();
    }
    
}
