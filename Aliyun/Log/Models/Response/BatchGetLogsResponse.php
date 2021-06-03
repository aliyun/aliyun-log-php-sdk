<?php
namespace Aliyun\Log\Models\Response;

/**
 * The response of the GetLog API from log service.
 *
 * @author log service dev
 */
class BatchGetLogsResponse extends \Aliyun\Log\Models\Response\Response {
    
    /**
     * @var array compressed Loggroup array
     */
    private $logPackageList;
    private $nextCursor;
    
    /**
     * Aliyun_Log_Models_BatchGetLogsResponse constructor
     *
     * @param array $resp
     *            GetLogs HTTP response body
     * @param array $header
     *            GetLogs HTTP response header
     */
    public function __construct($resp, $header) {
        parent::__construct ( $header );
        $this->logPackageList = $resp->getLogGroupListArray();
        $this->nextCursor = (isset($header['x-log-cursor']))?$header['x-log-cursor']:null;
        
    }

    public function getLogPackageList(){
      return $this->logPackageList;
    }

    public function getNextCursor(){
        return $this->nextCursor;
    }

    public function getCount() {
        return count($this->logPackageList);
    }

    public function getLogPackage($index){
        if($index<$this->getCount()){
            return $this->logPackageList[$index];
        }
        else{
            throw new \OutOfBoundsException('Index must less than size of logPackageList');
        }
    }

    public function getLogGroupList(){
        return $this->logPackageList;
    }

    public function getLogGroup($index){
        if($index<$this->getCount()){
            return  $this->logPackageList[$index];
        }
        else{
            throw new \OutOfBoundsException('Index must less than size of logPackageList');
        }
    }


}
