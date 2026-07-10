<?php

use PHPUnit\Framework\TestCase;

require_once realpath(dirname(__FILE__) . '/../Log_Autoload.php');

class ClientCurlErrorCompatibilityTest extends TestCase
{
    /**
     * @requires extension curl
     */
    public function testCurlFailuresAreWrappedAsSdkExceptions()
    {
        $client = new Aliyun_Log_Client('http://example.com', 'access-key-id', 'access-key-secret');
        $method = new ReflectionMethod(Aliyun_Log_Client::class, 'sendRequest');
        $method->setAccessible(true);

        try {
            $method->invoke($client, 'GET', 'unsupported-sls-test://example', '', array());
            $this->fail('Expected Aliyun_Log_Exception to be thrown.');
        } catch (Aliyun_Log_Exception $exception) {
            $this->assertNotFalse(strpos($exception->getErrorCode(), 'cURL error:'));
            $this->assertFalse(strpos($exception->getErrorCode(), 'could not be converted to string'));
        }
    }
}
