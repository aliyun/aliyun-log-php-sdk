<?php


namespace Aliyun\Log\Tests;


use Aliyun\Log\Client;

class ClientTest extends TestCase
{
    public function testCreate()
    {
        new Client('', '', '');
        self::assertTrue(true);
    }
}
