<?php

/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */

require_once realpath(dirname(__FILE__) . '/CredentialsProvider.php');


class Aliyun_Log_Models_StaticCredentialsProvider implements Aliyun_Log_Models_CredentialsProvider
{
    /**
     * @var Aliyun_Log_Models_Credentials
     */
    private $credentials;

    /**
     * @param string $accessKeyId
     * @param string $accessKeySecret
     * @param string $securityToken
     */
    public function __construct(string $accessKeyId, string $accessKeySecret, string $securityToken = '')
    {
        $this->credentials = new Aliyun_Log_Models_Credentials($accessKeyId, $accessKeySecret, $securityToken);
    }
    /**
     * @return Aliyun_Log_Models_Credentials
     */
    public function getCredentials(): Aliyun_Log_Models_Credentials
    {
        return $this->credentials;
    }
}