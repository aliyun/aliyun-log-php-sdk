<?php
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */

class Aliyun_Log_Models_OssShipperConfig{

    private $ossBucket;
    private $ossPrefix;
    private $bufferInterval = 300;
    private $bufferSize;
    private $compressType;
    private $roleArn;
    private $pathFormat;
    private $storage;

    /**
     * @return mixed
     */
    public function getRoleArn()
    {
        return $this->roleArn;
    }

    /**
     * @param mixed $roleArn
     */
    public function setRoleArn($roleArn)
    {
        $this->roleArn = $roleArn;
    }

    /**
     * @return mixed
     */
    public function getPathFormat()
    {
        return $this->pathFormat;
    }

    /**
     * @param mixed $pathFormat
     */
    public function setPathFormat($pathFormat)
    {
        $this->pathFormat = $pathFormat;
    }

    /**
     * @return mixed
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * @param mixed $storage
     */
    public function setStorage($storage)
    {
        $this->storage = $storage;
    }

    /**
     * @return mixed
     */
    public function getOssBucket()
    {
        return $this->ossBucket;
    }

    /**
     * @param mixed $ossBucket
     */
    public function setOssBucket($ossBucket)
    {
        $this->ossBucket = $ossBucket;
    }

    /**
     * @return mixed
     */
    public function getOssPrefix()
    {
        return $this->ossPrefix;
    }

    /**
     * @param mixed $ossPrefix
     */
    public function setOssPrefix($ossPrefix)
    {
        $this->ossPrefix = $ossPrefix;
    }

    /**
     * @return mixed
     */
    public function getBufferInterval()
    {
        return $this->bufferInterval;
    }

    /**
     * @param mixed $bufferInterval
     */
    public function setBufferInterval($bufferInterval)
    {
        $this->bufferInterval = $bufferInterval;
    }

    /**
     * @return mixed
     */
    public function getBufferSize()
    {
        return $this->bufferSize;
    }

    /**
     * @param mixed $bufferSize
     */
    public function setBufferSize($bufferSize)
    {
        if($bufferSize > 256 || $bufferSize < 5){
            throw new Exception("buffSize is not valide, must between 5 and 256");
        }
        $this->bufferSize = $bufferSize;
    }

    /**
     * @return mixed
     */
    public function getCompressType()
    {
        return $this->compressType;
    }

    /**
     * @param mixed $compressType
     */
    public function setCompressType($compressType)
    {
        $this->compressType = $compressType;
    }


    public function to_json_object() {
        $json =  array(
            'ossBucket' => $this->ossBucket,
            'ossPrefix' => $this->ossPrefix,
            'bufferInterval' => $this->bufferInterval,
            'bufferSize' => $this->bufferSize,
            'compressType' => $this->compressType,
            'roleArn' => $this->roleArn,
            'pathFormat' => $this->pathFormat,
            'storage' => $this->storage->to_json_object()
        );
        if($this->storage->getFormat() == 'json'){
            unset($json['storage']);
        }
        return $json;
    }
}