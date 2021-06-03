<?php
namespace Aliyun\Log\Models\Response;

/**
 * The response of the GetLog API from log service.
 *
 * @author log service dev
 */
class GetMachineResponse extends \Aliyun\Log\Models\Response\Response {

    private $machine;

    /**
     * Aliyun_Log_Models_GetMachineResponse constructor
     *
     * @param array $resp
     *            GetLogs HTTP response body
     * @param array $header
     *            GetLogs HTTP response header
     */
    public function __construct($resp, $header) {
        parent::__construct ( $header );
        //echo json_encode($resp);
        $this->machine = new \Aliyun\Log\Models\Machine();
        $this->machine->setFromArray($resp);
        
    }

    public function getMachine(){
        return $this->machine;
    }
   
}
