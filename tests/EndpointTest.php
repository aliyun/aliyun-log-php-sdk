<?php

use PHPUnit\Framework\TestCase;

require_once realpath(dirname(__FILE__) . '/../Log_Autoload.php');

class EndpointTest extends TestCase {
    public function testBuildUrl() {
        $this->assertEquals($this->getUrl('https://cn-hangzhou.log.aliyuncs.com', 'test', '/', array()), 'https://test.cn-hangzhou.log.aliyuncs.com/');
        $this->assertEquals($this->getUrl('cn-hangzhou.log.aliyuncs.com', 'test', '/', array()), 'http://test.cn-hangzhou.log.aliyuncs.com/');
        $this->assertEquals($this->getUrl('http://cn-hangzhou.log.aliyuncs.com', 'test', '/logstores', array()), 'http://test.cn-hangzhou.log.aliyuncs.com/logstores');
        $this->assertEquals($this->getUrl('https://cn-hangzhou.log.aliyuncs.com:443', 'test', '/logstores', array()), 'https://test.cn-hangzhou.log.aliyuncs.com:443/logstores');
        $this->assertEquals($this->getUrl('https://111.111.111.111:80', 'test', '/logstores', array()), 'https://111.111.111.111:80/logstores');
        $this->assertEquals($this->getUrl('111.111.111.111:442', 'test', '/test', array()), 'http://111.111.111.111:442/test');
        $this->assertEquals($this->getUrl('111.111.111.111:442', null, '/test', array()), 'http://111.111.111.111:442/test');
        $this->assertEquals($this->getUrl('http://111.111.111.111:442', 'test', '/cursor', array('type' => 'cursor')), 'http://111.111.111.111:442/cursor?type=cursor');
        $this->assertEquals($this->getUrl('https://cn-hangzhou.log.aliyuncs.com', null, '/cursor', array('type' => 'cursor')), 'https://cn-hangzhou.log.aliyuncs.com/cursor?type=cursor');
        $this->assertEquals($this->getUrl('cn-hangzhou.log.aliyuncs.com', null, '/', array()), 'http://cn-hangzhou.log.aliyuncs.com/');
    }

    public function getUrl($endpoint, $project, $resource, $params) {
        $accessKeyId = 'testKey';
        $accessKey = 'testAccessKey';
        $client = new Aliyun_Log_Client($endpoint, $accessKeyId, $accessKey);
        $reflection = new ReflectionClass($client);
        $method = $reflection->getMethod('buildUrl');
        $method->setAccessible(true);
        return $method->invokeArgs($client, [$project, $resource, $params]);
    }
}