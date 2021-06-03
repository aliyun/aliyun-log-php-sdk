<?php
namespace Aliyun\Log\Models\Response;

/**
 * The response of the GetLog API from log service.
 *
 * @author log service dev
 */
class GetConfigResponse extends \Aliyun\Log\Models\Response\Response {


    private $config;

    /**
     * Aliyun_Log_Models_GetConfigResponse constructor
     *
     * @param array $resp
     *            GetLogs HTTP response body
     * @param array $header
     *            GetLogs HTTP response header
     */
    public function __construct($resp, $header) {
        parent::__construct ( $header );
        $this->config = new \Aliyun\Log\Models\Config();
        $this->config->setFromArray($resp);
    }

    public function getConfig(){
        return $this->config;
    }

}
