<?php

namespace Aliyun\Log;

/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */
date_default_timezone_set ( 'Asia/Shanghai' );
if(!defined('API_VERSION'))
    define('API_VERSION', '0.6.0');
if(!defined('USER_AGENT'))
    define('USER_AGENT', 'log-php-sdk-v-0.6.0');
/**
 * Aliyun_Log_Client class is the main class in the SDK. It can be used to
 * communicate with LOG server to put/get data.
 *
 * @author log_dev
 */
class Client {

    /**
     * @var string aliyun accessKey
     */
    protected $accessKey;
    
    /**
     * @var string aliyun accessKeyId
     */
    protected $accessKeyId;

    /**
     *@var string aliyun sts token
     */
    protected $stsToken;

    /**
     * @var string LOG endpoint
     */
    protected $endpoint;

    /**
     * @var string Check if the host if row ip.
     */
    protected $isRowIp;

    /**
     * @var integer Http send port. The dafault value is 80.
     */
    protected $port;

    /**
     * @var string log sever host.
     */
    protected $logHost;

    /**
     * @var string the local machine ip address.
     */
    protected $source;
    
    /**
     * Aliyun_Log_Client constructor
     *
     * @param string $endpoint
     *            LOG host name, for example, http://cn-hangzhou.sls.aliyuncs.com
     * @param string $accessKeyId
     *            aliyun accessKeyId
     * @param string $accessKey
     *            aliyun accessKey
     */
    public function __construct($endpoint, $accessKeyId, $accessKey,$token = "") {
        $this->setEndpoint ( $endpoint ); // set $this->logHost
        $this->accessKeyId = $accessKeyId;
        $this->accessKey = $accessKey;
        $this->stsToken = $token;
        $this->source = \Aliyun\Log\Util::getLocalIp();
    }
    private function setEndpoint($endpoint) {
        $pos = strpos ( $endpoint, "://" );
        if ($pos !== false) { // be careful, !==
            $pos += 3;
            $endpoint = substr ( $endpoint, $pos );
        }
        $pos = strpos ( $endpoint, "/" );
        if ($pos !== false) // be careful, !==
            $endpoint = substr ( $endpoint, 0, $pos );
        $pos = strpos ( $endpoint, ':' );
        if ($pos !== false) { // be careful, !==
            $this->port = ( int ) substr ( $endpoint, $pos + 1 );
            $endpoint = substr ( $endpoint, 0, $pos );
        } else
            $this->port = 80;
        $this->isRowIp = \Aliyun\Log\Util::isIp ( $endpoint );
        $this->logHost = $endpoint;
        $this->endpoint = $endpoint . ':' . ( string ) $this->port;
    }
     
    /**
     * GMT format time string.
     * 
     * @return string
     */
    protected function getGMT() {
        return gmdate ( 'D, d M Y H:i:s' ) . ' GMT';
    }
    

    /**
				 * Decodes a JSON string to a JSON Object. 
				 * Unsuccessful decode will cause an Aliyun_Log_Exception.
				 *
				 * @return string
				 * @throws \Aliyun\Log\Exception
				 */
				protected function parseToJson($resBody, $requestId) {
        if (! $resBody)
          return NULL;
        
        $result = json_decode ( $resBody, true );
        if ($result === NULL){
          throw new \Aliyun\Log\Exception ( 'BadResponse', "Bad format,not json: $resBody", $requestId );
        }
        return $result;
    }
    
    /**
     * @return array
     */
    protected function getHttpResponse($method, $url, $body, $headers) {
        $request = new \Aliyun\Log\RequestCore ( $url );
        foreach ( $headers as $key => $value )
            $request->add_header ( $key, $value );
        $request->set_method ( $method );
        $request->set_useragent(USER_AGENT);
        if ($method == "POST" || $method == "PUT")
            $request->set_body ( $body );
        $request->send_request ();
        $response = array ();
        $response [] = ( int ) $request->get_response_code ();
        $response [] = $request->get_response_header ();
        $response [] = $request->get_response_body ();
        return $response;
    }
    
    /**
				 * @return array
				 * @throws \Aliyun\Log\Exception
				 */
				private function sendRequest($method, $url, $body, $headers) {
        try {
            list ( $responseCode, $header, $resBody ) = 
                    $this->getHttpResponse ( $method, $url, $body, $headers );
        } catch ( \Exception $ex ) {
            throw new \Aliyun\Log\Exception ( $ex->getMessage (), $ex->__toString () );
        }
        
        $requestId = isset ( $header ['x-log-requestid'] ) ? $header ['x-log-requestid'] : '';

        if ($responseCode == 200) {
          return array ($resBody,$header);
        } 
        else {
            $exJson = $this->parseToJson ( $resBody, $requestId );
            if (isset($exJson ['error_code']) && isset($exJson ['error_message'])) {
                throw new \Aliyun\Log\Exception ( $exJson ['error_code'], 
                        $exJson ['error_message'], $requestId );
            } else {
                if ($exJson) {
                    $exJson = ' The return json is ' . json_encode($exJson);
                } else {
                    $exJson = '';
                }
                throw new \Aliyun\Log\Exception ( 'RequestError',
                        "Request is failed. Http code is $responseCode.$exJson", $requestId );
            }
        }
    }
    
    /**
				 * @return array
				 * @throws \Aliyun\Log\Exception
				 */
				private function send($method, $project, $body, $resource, $params, $headers) {
        if ($body) {
            $headers ['Content-Length'] = strlen ( $body );
            if(isset($headers ["x-log-bodyrawsize"])==false)
                $headers ["x-log-bodyrawsize"] = 0;
            $headers ['Content-MD5'] = \Aliyun\Log\Util::calMD5 ( $body );
        } else {
            $headers ['Content-Length'] = 0;
            $headers ["x-log-bodyrawsize"] = 0;
            $headers ['Content-Type'] = ''; // If not set, http request will add automatically.
        }
        
        $headers ['x-log-apiversion'] = API_VERSION;
        $headers ['x-log-signaturemethod'] = 'hmac-sha1';
        if(strlen($this->stsToken) >0)
            $headers ['x-acs-security-token'] = $this -> stsToken;
        if(is_null($project))$headers ['Host'] = $this->logHost;
        else $headers ['Host'] = "$project.$this->logHost";
        $headers ['Date'] = $this->GetGMT ();
        $signature = \Aliyun\Log\Util::getRequestAuthorization ( $method, $resource, $this->accessKey,$this->stsToken, $params, $headers );
        $headers ['Authorization'] = "LOG $this->accessKeyId:$signature";
        
        $url = $resource;
        if ($params)
            $url .= '?' . \Aliyun\Log\Util::urlEncode ( $params );
        if ($this->isRowIp)
            $url = "http://$this->endpoint$url";
        else{
          if(is_null($project))
              $url = "http://$this->endpoint$url";
          else  $url = "http://$project.$this->endpoint$url";           
        }
        return $this->sendRequest ( $method, $url, $body, $headers );
    }
    
    /**
				 * Put logs to Log Service.
				 * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
				 *
				 * @param \Aliyun\Log\Models\Request\PutLogsRequest $request the PutLogs request parameters class
				 * @throws \Aliyun\Log\Exception
				 * @return \Aliyun\Log\Models\Response\PutLogsResponse
				 */
				public function putLogs(\Aliyun\Log\Models\Request\PutLogsRequest $request) {
        if (count ( $request->getLogitems () ) > 4096)
            throw new \Aliyun\Log\Exception ( 'InvalidLogSize', "logItems' length exceeds maximum limitation: 4096 lines." );
        
        $logGroup = new \Aliyun\Log\LogGroup ();
        $topic = $request->getTopic () !== null ? $request->getTopic () : '';
        $logGroup->setTopic ( $request->getTopic () );
        $source = $request->getSource ();
        
        if ( ! $source )
            $source = $this->source;
        $logGroup->setSource ( $source );
        $logitems = $request->getLogitems ();
        foreach ( $logitems as $logItem ) {
            $log = new \Aliyun\Log\Log ();
            $log->setTime ( $logItem->getTime () );
            $content = $logItem->getContents ();
            foreach ( $content as $key => $value ) {
                $content = new \Aliyun\Log\LogContent ();
                $content->setKey ( $key );
                $content->setValue ( $value );
                $log->addContents ( $content );
            }

            $logGroup->addLogs ( $log );
        }

        $body = \Aliyun\Log\Util::toBytes( $logGroup );
        unset ( $logGroup );
        
        $bodySize = strlen ( $body );
        if ($bodySize > 3 * 1024 * 1024) // 3 MB
            throw new \Aliyun\Log\Exception ( 'InvalidLogSize', "logItems' size exceeds maximum limitation: 3 MB." );
        $params = array ();
        $headers = array ();
        $headers ["x-log-bodyrawsize"] = $bodySize;
        $headers ['x-log-compresstype'] = 'deflate';
        $headers ['Content-Type'] = 'application/x-protobuf';
        $body = gzcompress ( $body, 6 );
        
        $logstore = $request->getLogstore () !== null ? $request->getLogstore () : '';
        $project = $request->getProject () !== null ? $request->getProject () : '';
        $shardKey = $request -> getShardKey();
        $resource = "/logstores/" . $logstore.($shardKey== null?"/shards/lb":"/shards/route");
        if($shardKey)
            $params["key"]=$shardKey;
        list ( $resp, $header ) = $this->send ( "POST", $project, $body, $resource, $params, $headers );
        $requestId = isset ( $header ['x-log-requestid'] ) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson ( $resp, $requestId );
        return new \Aliyun\Log\Models\Response\PutLogsResponse ( $header );
    }

    /**
				 * create shipper service
				 * @param \Aliyun\Log\Models\Request\CreateShipperRequest $request
				 * return Aliyun_Log_Models_CreateShipperResponse
				 */
				public function createShipper(\Aliyun\Log\Models\Request\CreateShipperRequest $request){
        $headers = array();
        $params = array();
        $resource = "/logstores/".$request->getLogStore()."/shipper";
        $project = $request->getProject () !== null ? $request->getProject () : '';
        $headers["Content-Type"] = "application/json";

        $body = array(
            "shipperName" => $request->getShipperName(),
            "targetType" => $request->getTargetType(),
            "targetConfiguration" => $request->getTargetConfigration()
        );
        $body_str = json_encode($body);
        $headers["x-log-bodyrawsize"] = strlen($body_str);
        list($resp, $header) = $this->send("POST", $project,$body_str,$resource,$params,$headers);
        $requestId = isset($header['x-log-requestid']) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson($resp, $requestId);
        return new \Aliyun\Log\Models\Response\CreateShipperResponse($resp, $header);
    }

    /**
				 * create shipper service
				 * @param \Aliyun\Log\Models\Request\UpdateShipperRequest $request
				 * return Aliyun_Log_Models_UpdateShipperResponse
				 */
				public function updateShipper(\Aliyun\Log\Models\Request\UpdateShipperRequest $request){
        $headers = array();
        $params = array();
        $resource = "/logstores/".$request->getLogStore()."/shipper/".$request->getShipperName();
        $project = $request->getProject () !== null ? $request->getProject () : '';
        $headers["Content-Type"] = "application/json";

        $body = array(
            "shipperName" => $request->getShipperName(),
            "targetType" => $request->getTargetType(),
            "targetConfiguration" => $request->getTargetConfigration()
        );
        $body_str = json_encode($body);
        $headers["x-log-bodyrawsize"] = strlen($body_str);
        list($resp, $header) = $this->send("PUT", $project,$body_str,$resource,$params,$headers);
        $requestId = isset($header['x-log-requestid']) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson($resp, $requestId);
        return new \Aliyun\Log\Models\Response\UpdateShipperResponse($resp, $header);
    }

    /**
				 * get shipper tasks list, max 48 hours duration supported
				 * @param \Aliyun\Log\Models\Request\GetShipperTasksRequest $request
				 * return Aliyun_Log_Models_GetShipperTasksResponse
				 */
				public function getShipperTasks(\Aliyun\Log\Models\Request\GetShipperTasksRequest $request){
        $headers = array();
        $params = array(
            'from' => $request->getStartTime(),
            'to' => $request->getEndTime(),
            'status' => $request->getStatusType(),
            'offset' => $request->getOffset(),
            'size' => $request->getSize()
        );
        $resource = "/logstores/".$request->getLogStore()."/shipper/".$request->getShipperName()."/tasks";
        $project = $request->getProject () !== null ? $request->getProject () : '';
        $headers["x-log-bodyrawsize"] = 0;
        $headers["Content-Type"] = "application/json";

        list($resp, $header) = $this->send("GET", $project,null,$resource,$params,$headers);
        $requestId = isset($header['x-log-requestid']) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson($resp, $requestId);
        return new \Aliyun\Log\Models\Response\GetShipperTasksResponse($resp, $header);
    }

    /**
				 * retry shipper tasks list by task ids
				 * @param \Aliyun\Log\Models\Request\RetryShipperTasksRequest $request
				 * return Aliyun_Log_Models_RetryShipperTasksResponse
				 */
				public function retryShipperTasks(\Aliyun\Log\Models\Request\RetryShipperTasksRequest $request){
        $headers = array();
        $params = array();
        $resource = "/logstores/".$request->getLogStore()."/shipper/".$request->getShipperName()."/tasks";
        $project = $request->getProject () !== null ? $request->getProject () : '';

        $headers["Content-Type"] = "application/json";
        $body = $request->getTaskLists();
        $body_str = json_encode($body);
        $headers["x-log-bodyrawsize"] = strlen($body_str);
        list($resp, $header) = $this->send("PUT", $project,$body_str,$resource,$params,$headers);
        $requestId = isset($header['x-log-requestid']) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson($resp, $requestId);
        return new \Aliyun\Log\Models\Response\RetryShipperTasksResponse($resp, $header);
    }

    /**
				 * delete shipper service
				 * @param \Aliyun\Log\Models\Request\DeleteShipperRequest $request
				 * return Aliyun_Log_Models_DeleteShipperResponse
				 */
				public function deleteShipper(\Aliyun\Log\Models\Request\DeleteShipperRequest $request){
        $headers = array();
        $params = array();
        $resource = "/logstores/".$request->getLogStore()."/shipper/".$request->getShipperName();
        $project = $request->getProject () !== null ? $request->getProject () : '';
        $headers["x-log-bodyrawsize"] = 0;
        $headers["Content-Type"] = "application/json";

        list($resp, $header) = $this->send("DELETE", $project,null,$resource,$params,$headers);
        $requestId = isset($header['x-log-requestid']) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson($resp, $requestId);
        return new \Aliyun\Log\Models\Response\DeleteShipperResponse($resp, $header);
    }

    /**
				 * get shipper config service
				 * @param \Aliyun\Log\Models\Request\GetShipperConfigRequest $request
				 * return Aliyun_Log_Models_GetShipperConfigResponse
				 */
				public function getShipperConfig(\Aliyun\Log\Models\Request\GetShipperConfigRequest $request){
        $headers = array();
        $params = array();
        $resource = "/logstores/".$request->getLogStore()."/shipper/".$request->getShipperName();
        $project = $request->getProject () !== null ? $request->getProject () : '';
        $headers["x-log-bodyrawsize"] = 0;
        $headers["Content-Type"] = "application/json";

        list($resp, $header) = $this->send("GET", $project,null,$resource,$params,$headers);
        $requestId = isset($header['x-log-requestid']) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson($resp, $requestId);
        return new \Aliyun\Log\Models\Response\GetShipperConfigResponse($resp, $header);
    }

    /**
				 * list shipper service
				 * @param \Aliyun\Log\Models\Request\ListShipperRequest $request
				 * return Aliyun_Log_Models_ListShipperResponse
				 */
				public function listShipper(\Aliyun\Log\Models\Request\ListShipperRequest $request){
        $headers = array();
        $params = array();
        $resource = "/logstores/".$request->getLogStore()."/shipper";
        $project = $request->getProject () !== null ? $request->getProject () : '';
        $headers["x-log-bodyrawsize"] = 0;
        $headers["Content-Type"] = "application/json";

        list($resp, $header) = $this->send("GET", $project,null,$resource,$params,$headers);
        $requestId = isset($header['x-log-requestid']) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson($resp, $requestId);
        return new \Aliyun\Log\Models\Response\ListShipperResponse($resp, $header);
    }

    /**
				 * create logstore 
				 * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
				 *
				 * @param \Aliyun\Log\Models\Request\CreateLogstoreRequest $request the CreateLogStore request parameters class.
				 * @throws \Aliyun\Log\Exception
				 * return Aliyun_Log_Models_CreateLogstoreResponse
				 */
				public function createLogstore(\Aliyun\Log\Models\Request\CreateLogstoreRequest $request){
        $headers = array ();
        $params = array ();
        $resource = '/logstores';
        $project = $request->getProject () !== null ? $request->getProject () : '';
        $headers["x-log-bodyrawsize"] = 0;
        $headers["Content-Type"] = "application/json";
        $body = array(
            "logstoreName" => $request -> getLogstore(),
            "ttl" => (int)($request -> getTtl()),
            "shardCount" => (int)($request -> getShardCount())
        );
        $body_str =  json_encode($body);
        $headers["x-log-bodyrawsize"] = strlen($body_str);
        list($resp,$header)  = $this -> send("POST",$project,$body_str,$resource,$params,$headers);
        $requestId = isset ( $header ['x-log-requestid'] ) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson ( $resp, $requestId );
        return new \Aliyun\Log\Models\Response\CreateLogstoreResponse($resp,$header);
    }
    /**
				 * update logstore 
				 * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
				 *
				 * @param \Aliyun\Log\Models\Request\UpdateLogstoreRequest $request the UpdateLogStore request parameters class.
				 * @throws \Aliyun\Log\Exception
				 * return Aliyun_Log_Models_UpdateLogstoreResponse
				 */
				public function updateLogstore(\Aliyun\Log\Models\Request\UpdateLogstoreRequest $request){
        $headers = array ();
        $params = array ();
        $project = $request->getProject () !== null ? $request->getProject () : '';
        $headers["Content-Type"] = "application/json";
        $body = array(
            "logstoreName" => $request -> getLogstore(),
            "ttl" => (int)($request -> getTtl()),
            "shardCount" => (int)($request -> getShardCount())
        );
        $resource = '/logstores/'.$request -> getLogstore();
        $body_str =  json_encode($body);
        $headers["x-log-bodyrawsize"] = strlen($body_str);
        list($resp,$header)  = $this -> send("PUT",$project,$body_str,$resource,$params,$headers);
        $requestId = isset ( $header ['x-log-requestid'] ) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson ( $resp, $requestId );
        return new \Aliyun\Log\Models\Response\UpdateLogstoreResponse($resp,$header);
    }
    /**
				 * List all logstores of requested project.
				 * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
				 *
				 * @param \Aliyun\Log\Models\Request\ListLogstoresRequest $request the ListLogstores request parameters class.
				 * @throws \Aliyun\Log\Exception
				 * @return \Aliyun\Log\Models\Response\ListLogstoresResponse
				 */
				public function listLogstores(\Aliyun\Log\Models\Request\ListLogstoresRequest $request) {
        $headers = array ();
        $params = array ();
        $resource = '/logstores';
        $project = $request->getProject () !== null ? $request->getProject () : '';
        list ( $resp, $header ) = $this->send ( "GET", $project, NULL, $resource, $params, $headers );
        $requestId = isset ( $header ['x-log-requestid'] ) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson ( $resp, $requestId );
        return new \Aliyun\Log\Models\Response\ListLogstoresResponse ( $resp, $header );
    }

    /**
				 * Delete logstore
				 * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
				 *
				 * @param \Aliyun\Log\Models\Request\DeleteLogstoreRequest $request the DeleteLogstores request parameters class.
				 * @throws \Aliyun\Log\Exception
				 * @return Aliyun_Log_Models_DeleteLogstoresResponse
				 */
				public function deleteLogstore(\Aliyun\Log\Models\Request\DeleteLogstoreRequest $request) {
        $headers = array ();
        $params = array ();
        $project = $request->getProject () !== null ? $request->getProject () : '';
        $logstore = $request -> getLogstore() != null ? $request -> getLogstore() :"";
        $resource = "/logstores/$logstore";
        list ( $resp, $header ) = $this->send ( "DELETE", $project, NULL, $resource, $params, $headers );
        $requestId = isset ( $header ['x-log-requestid'] ) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson ( $resp, $requestId );
        return new \Aliyun\Log\Models\Response\DeleteLogstoreResponse ( $resp, $header );
    }

    /**
				 * List all topics in a logstore.
				 * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
				 *
				 * @param \Aliyun\Log\Models\Request\ListTopicsRequest $request the ListTopics request parameters class.
				 * @throws \Aliyun\Log\Exception
				 * @return \Aliyun\Log\Models\Response\ListTopicsResponse
				 */
				public function listTopics(\Aliyun\Log\Models\Request\ListTopicsRequest $request) {
        $headers = array ();
        $params = array ();
        if ($request->getToken () !== null)
            $params ['token'] = $request->getToken ();
        if ($request->getLine () !== null)
            $params ['line'] = $request->getLine ();
        $params ['type'] = 'topic';
        $logstore = $request->getLogstore () !== null ? $request->getLogstore () : '';
        $project = $request->getProject () !== null ? $request->getProject () : '';
        $resource = "/logstores/$logstore";
        list ( $resp, $header ) = $this->send ( "GET", $project, NULL, $resource, $params, $headers );
        $requestId = isset ( $header ['x-log-requestid'] ) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson ( $resp, $requestId );
        return new \Aliyun\Log\Models\Response\ListTopicsResponse ( $resp, $header );
    }

    /**
				 * Get histograms of requested query from log service.
				 * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
				 *
				 * @param \Aliyun\Log\Models\Request\GetHistogramsRequest $request the GetHistograms request parameters class.
				 * @throws \Aliyun\Log\Exception
				 * @return array(json body, http header)
				 */
				public function getHistogramsJson(\Aliyun\Log\Models\Request\GetHistogramsRequest $request) {
        $headers = array ();
        $params = array ();
        if ($request->getTopic () !== null)
            $params ['topic'] = $request->getTopic ();
        if ($request->getFrom () !== null)
            $params ['from'] = $request->getFrom ();
        if ($request->getTo () !== null)
            $params ['to'] = $request->getTo ();
        if ($request->getQuery () !== null)
            $params ['query'] = $request->getQuery ();
        $params ['type'] = 'histogram';
        $logstore = $request->getLogstore () !== null ? $request->getLogstore () : '';
        $project = $request->getProject () !== null ? $request->getProject () : '';
        $resource = "/logstores/$logstore";
        list ( $resp, $header ) = $this->send ( "GET", $project, NULL, $resource, $params, $headers );
        $requestId = isset ( $header ['x-log-requestid'] ) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson ( $resp, $requestId );
        return array($resp, $header);
    }
    
    /**
				 * Get histograms of requested query from log service.
				 * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
				 *
				 * @param \Aliyun\Log\Models\Request\GetHistogramsRequest $request the GetHistograms request parameters class.
				 * @throws \Aliyun\Log\Exception
				 * @return \Aliyun\Log\Models\Response\GetHistogramsResponse
				 */
				public function getHistograms(\Aliyun\Log\Models\Request\GetHistogramsRequest $request) {
        $ret = $this->getHistogramsJson($request);
        $resp = $ret[0];
        $header = $ret[1];
        return new \Aliyun\Log\Models\Response\GetHistogramsResponse ( $resp, $header );
    }

    /**
				 * Get logs from Log service.
				 * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
				 *
				 * @param \Aliyun\Log\Models\Request\GetLogsRequest $request the GetLogs request parameters class.
				 * @throws \Aliyun\Log\Exception
				 * @return array(json body, http header)
				 */
				public function getLogsJson(\Aliyun\Log\Models\Request\GetLogsRequest $request) {
        $headers = array ();
        $params = array ();
        if ($request->getTopic () !== null)
            $params ['topic'] = $request->getTopic ();
        if ($request->getFrom () !== null)
            $params ['from'] = $request->getFrom ();
        if ($request->getTo () !== null)
            $params ['to'] = $request->getTo ();
        if ($request->getQuery () !== null)
            $params ['query'] = $request->getQuery ();
        $params ['type'] = 'log';
        if ($request->getLine () !== null)
            $params ['line'] = $request->getLine ();
        if ($request->getOffset () !== null)
            $params ['offset'] = $request->getOffset ();
        if ($request->getOffset () !== null)
            $params ['reverse'] = $request->getReverse () ? 'true' : 'false';
        $logstore = $request->getLogstore () !== null ? $request->getLogstore () : '';
        $project = $request->getProject () !== null ? $request->getProject () : '';
        $resource = "/logstores/$logstore";
        list ( $resp, $header ) = $this->send ( "GET", $project, NULL, $resource, $params, $headers );
        $requestId = isset ( $header ['x-log-requestid'] ) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson ( $resp, $requestId );
        return array($resp, $header);
        //return new Aliyun_Log_Models_GetLogsResponse ( $resp, $header );
    }
    
    /**
				 * Get logs from Log service.
				 * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
				 *
				 * @param \Aliyun\Log\Models\Request\GetLogsRequest $request the GetLogs request parameters class.
				 * @throws \Aliyun\Log\Exception
				 * @return \Aliyun\Log\Models\Response\GetLogsResponse
				 */
				public function getLogs(\Aliyun\Log\Models\Request\GetLogsRequest $request) {
        $ret = $this->getLogsJson($request);
        $resp = $ret[0];
        $header = $ret[1];
        return new \Aliyun\Log\Models\Response\GetLogsResponse ( $resp, $header );
    }

    /**
				 * Get logs from Log service.
				 * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
				 *
				 * @param \Aliyun\Log\Models\Request\GetProjectLogsRequest $request the GetLogs request parameters class.
				 * @throws \Aliyun\Log\Exception
				 * @return array(json body, http header)
				 */
				public function getProjectLogsJson(\Aliyun\Log\Models\Request\GetProjectLogsRequest $request) {
        $headers = array ();
        $params = array ();
        if ($request->getQuery () !== null)
            $params ['query'] = $request->getQuery ();
        $project = $request->getProject () !== null ? $request->getProject () : '';
        $resource = "/logs";
        list ( $resp, $header ) = $this->send ( "GET", $project, NULL, $resource, $params, $headers );
        $requestId = isset ( $header ['x-log-requestid'] ) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson ( $resp, $requestId );
        return array($resp, $header);
        //return new Aliyun_Log_Models_GetLogsResponse ( $resp, $header );
    }
     /**
				 * Get logs from Log service.
				 * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
				 *
				 * @param \Aliyun\Log\Models\Request\GetProjectLogsRequest $request the GetLogs request parameters class.
				 * @throws \Aliyun\Log\Exception
				 * @return \Aliyun\Log\Models\Response\GetLogsResponse
				 */
				public function getProjectLogs(\Aliyun\Log\Models\Request\GetProjectLogsRequest $request) {
        $ret = $this->getProjectLogsJson($request);
        $resp = $ret[0];
        $header = $ret[1];
        return new \Aliyun\Log\Models\Response\GetLogsResponse ( $resp, $header );
    }
    
    /**
				 * Get logs from Log service with shardid conditions.
				 * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
				 *
				 * @param \Aliyun\Log\Models\Request\BatchGetLogsRequest $request the BatchGetLogs request parameters class.
				 * @throws \Aliyun\Log\Exception
				 * @return \Aliyun\Log\Models\Response\BatchGetLogsResponse
				 */
				public function batchGetLogs(\Aliyun\Log\Models\Request\BatchGetLogsRequest $request) {
      $params = array();
      $headers = array();
      $project = $request->getProject()!==null?$request->getProject():'';
      $logstore = $request->getLogstore()!==null?$request->getLogstore():'';
      $shardId = $request->getShardId()!==null?$request->getShardId():'';
      if($request->getCount()!==null)
          $params['count']=$request->getCount();
      if($request->getCursor()!==null)
          $params['cursor']=$request->getCursor();
	  if($request->getEndCursor()!==null)
          $params['end_cursor']=$request->getEndCursor();
      $params['type']='log';
      $headers['Accept-Encoding']='gzip';
      $headers['accept']='application/x-protobuf';

      $resource = "/logstores/$logstore/shards/$shardId";
      list($resp,$header) = $this->send("GET",$project,NULL,$resource,$params,$headers);
      //$resp is a byteArray
      $resp =  gzuncompress($resp);
      if($resp===false)$resp = new \Aliyun\Log\LogGroupList();
      
      else {
          $resp = new \Aliyun\Log\LogGroupList($resp);
      }
      return new \Aliyun\Log\Models\Response\BatchGetLogsResponse ( $resp, $header );
    }

    /**
				 * List Shards from Log service with Project and logstore conditions.
				 * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
				 *
				 * @param \Aliyun\Log\Models\Request\ListShardsRequest $request the ListShards request parameters class.
				 * @throws \Aliyun\Log\Exception
				 * @return \Aliyun\Log\Models\Response\ListShardsResponse
				 */
				public function listShards(\Aliyun\Log\Models\Request\ListShardsRequest $request) {
        $params = array();
        $headers = array();
        $project = $request->getProject()!==null?$request->getProject():'';
        $logstore = $request->getLogstore()!==null?$request->getLogstore():'';

        $resource='/logstores/'.$logstore.'/shards';
        list($resp,$header) = $this->send("GET",$project,NULL,$resource,$params,$headers); 
        $requestId = isset ( $header ['x-log-requestid'] ) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson ( $resp, $requestId );
        return new \Aliyun\Log\Models\Response\ListShardsResponse ( $resp, $header );
    }

    /**
				 * split a shard into two shards  with Project and logstore and shardId and midHash conditions.
				 * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
				 *
				 * @param \Aliyun\Log\Models\Request\SplitShardRequest $request the SplitShard request parameters class.
				 * @throws \Aliyun\Log\Exception
				 * @return \Aliyun\Log\Models\Response\ListShardsResponse
				 */
				public function splitShard(\Aliyun\Log\Models\Request\SplitShardRequest $request) {
        $params = array();
        $headers = array();
        $project = $request->getProject()!==null?$request->getProject():'';
        $logstore = $request->getLogstore()!==null?$request->getLogstore():'';
        $shardId = $request -> getShardId()!== null ? $request -> getShardId():-1;
        $midHash = $request -> getMidHash()!= null?$request -> getMidHash():"";

        $resource='/logstores/'.$logstore.'/shards/'.$shardId;
        $params["action"] = "split";
        $params["key"] = $midHash;
        list($resp,$header) = $this->send("POST",$project,NULL,$resource,$params,$headers); 
        $requestId = isset ( $header ['x-log-requestid'] ) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson ( $resp, $requestId );
        return new \Aliyun\Log\Models\Response\ListShardsResponse ( $resp, $header );
    }
    /**
				 * merge two shards into one shard with Project and logstore and shardId and conditions.
				 * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
				 *
				 * @param \Aliyun\Log\Models\Request\MergeShardsRequest $request the MergeShards request parameters class.
				 * @throws \Aliyun\Log\Exception
				 * @return \Aliyun\Log\Models\Response\ListShardsResponse
				 */
				public function MergeShards(\Aliyun\Log\Models\Request\MergeShardsRequest $request) {
        $params = array();
        $headers = array();
        $project = $request->getProject()!==null?$request->getProject():'';
        $logstore = $request->getLogstore()!==null?$request->getLogstore():'';
        $shardId = $request -> getShardId()!= null ? $request -> getShardId():-1;

        $resource='/logstores/'.$logstore.'/shards/'.$shardId;
        $params["action"] = "merge";
        list($resp,$header) = $this->send("POST",$project,NULL,$resource,$params,$headers); 
        $requestId = isset ( $header ['x-log-requestid'] ) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson ( $resp, $requestId );
        return new \Aliyun\Log\Models\Response\ListShardsResponse ( $resp, $header );
    }
    /**
				 * delete a read only shard with Project and logstore and shardId conditions.
				 * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
				 *
				 * @param \Aliyun\Log\Models\Request\DeleteShardRequest $request the DeleteShard request parameters class.
				 * @throws \Aliyun\Log\Exception
				 * @return \Aliyun\Log\Models\Response\ListShardsResponse
				 */
				public function DeleteShard(\Aliyun\Log\Models\Request\DeleteShardRequest $request) {
        $params = array();
        $headers = array();
        $project = $request->getProject()!==null?$request->getProject():'';
        $logstore = $request->getLogstore()!==null?$request->getLogstore():'';
        $shardId = $request -> getShardId()!= null ? $request -> getShardId():-1;

        $resource='/logstores/'.$logstore.'/shards/'.$shardId;
        list($resp,$header) = $this->send("DELETE",$project,NULL,$resource,$params,$headers); 
        $requestId = isset ( $header ['x-log-requestid'] ) ? $header ['x-log-requestid'] : '';
        return new \Aliyun\Log\Models\Response\DeleteShardResponse ( $header );
    }

    /**
				 * Get cursor from Log service.
				 * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
				 *
				 * @param \Aliyun\Log\Models\Request\GetCursorRequest $request the GetCursor request parameters class.
				 * @throws \Aliyun\Log\Exception
				 * @return \Aliyun\Log\Models\Response\GetCursorResponse
				 */
				public function getCursor(\Aliyun\Log\Models\Request\GetCursorRequest $request){
      $params = array();
      $headers = array();
      $project = $request->getProject()!==null?$request->getProject():'';
      $logstore = $request->getLogstore()!==null?$request->getLogstore():'';
      $shardId = $request->getShardId()!==null?$request->getShardId():'';
      $mode = $request->getMode()!==null?$request->getMode():'';
      $fromTime = $request->getFromTime()!==null?$request->getFromTime():-1;

      if((empty($mode) xor $fromTime==-1)==false){
        if(!empty($mode))
          throw new \Aliyun\Log\Exception ( 'RequestError',"Request is failed. Mode and fromTime can not be not empty simultaneously");
        else
          throw new \Aliyun\Log\Exception ( 'RequestError',"Request is failed. Mode and fromTime can not be empty simultaneously");
      }
      if(!empty($mode) && strcmp($mode,'begin')!==0 && strcmp($mode,'end')!==0)
        throw new \Aliyun\Log\Exception ( 'RequestError',"Request is failed. Mode value invalid:$mode");
      if($fromTime!==-1 && (is_integer($fromTime)==false || $fromTime<0))
        throw new \Aliyun\Log\Exception ( 'RequestError',"Request is failed. FromTime value invalid:$fromTime");
      $params['type']='cursor';
      if($fromTime!==-1)$params['from']=$fromTime;
      else $params['mode'] = $mode;
      $resource='/logstores/'.$logstore.'/shards/'.$shardId;
      list($resp,$header) = $this->send("GET",$project,NULL,$resource,$params,$headers); 
      $requestId = isset ( $header ['x-log-requestid'] ) ? $header ['x-log-requestid'] : '';
      $resp = $this->parseToJson ( $resp, $requestId );
      return new \Aliyun\Log\Models\Response\GetCursorResponse($resp,$header);
    }

    public function createConfig(\Aliyun\Log\Models\Request\CreateConfigRequest $request){
        $params = array();
        $headers = array();
        $body=null;
        if($request->getConfig()!==null){
          $body = json_encode($request->getConfig()->toArray());
        }
        $headers ['Content-Type'] = 'application/json';
        $resource = '/configs';
        list($resp,$header) = $this->send("POST",NULL,$body,$resource,$params,$headers); 
        return new \Aliyun\Log\Models\Response\CreateConfigResponse($header);
    }

    public function updateConfig(\Aliyun\Log\Models\Request\UpdateConfigRequest $request){
        $params = array();
        $headers = array();
        $body=null;
        $configName='';
        if($request->getConfig()!==null){
          $body = json_encode($request->getConfig()->toArray());
          $configName=($request->getConfig()->getConfigName()!==null)?$request->getConfig()->getConfigName():'';
        }
        $headers ['Content-Type'] = 'application/json';
        $resource = '/configs/'.$configName;
        list($resp,$header) = $this->send("PUT",NULL,$body,$resource,$params,$headers);  
        return new \Aliyun\Log\Models\Response\UpdateConfigResponse($header);
    }

    public function getConfig(\Aliyun\Log\Models\Request\GetConfigRequest $request){
        $params = array();
        $headers = array();

        $configName = ($request->getConfigName()!==null)?$request->getConfigName():'';
        
        $resource = '/configs/'.$configName;
        list($resp,$header) = $this->send("GET",NULL,NULL,$resource,$params,$headers); 
        $requestId = isset ( $header ['x-log-requestid'] ) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson ( $resp, $requestId );
        return new \Aliyun\Log\Models\Response\GetConfigResponse($resp,$header);
    }

    public function deleteConfig(\Aliyun\Log\Models\Request\DeleteConfigRequest $request){
        $params = array();
        $headers = array();
        $configName = ($request->getConfigName()!==null)?$request->getConfigName():'';
        $resource = '/configs/'.$configName;
        list($resp,$header) = $this->send("DELETE",NULL,NULL,$resource,$params,$headers); 
        return new \Aliyun\Log\Models\Response\DeleteConfigResponse($header);
    }

    public function listConfigs(\Aliyun\Log\Models\Request\ListConfigsRequest $request){
        $params = array();
        $headers = array();

        if($request->getConfigName()!==null)$params['configName'] = $request->getConfigName();
        if($request->getOffset()!==null)$params['offset'] = $request->getOffset();
        if($request->getSize()!==null)$params['size'] = $request->getSize();

        $resource = '/configs';
        list($resp,$header) = $this->send("GET",NULL,NULL,$resource,$params,$headers); 
        $requestId = isset ( $header ['x-log-requestid'] ) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson ( $resp, $requestId );
        return new \Aliyun\Log\Models\Response\ListConfigsResponse($resp,$header);
    }
    
    public function createMachineGroup(\Aliyun\Log\Models\Request\CreateMachineGroupRequest $request){
        $params = array();
        $headers = array();
        $body=null;
        if($request->getMachineGroup()!==null){
          $body = json_encode($request->getMachineGroup()->toArray());
        }
        $headers ['Content-Type'] = 'application/json';
        $resource = '/machinegroups';
        list($resp,$header) = $this->send("POST",NULL,$body,$resource,$params,$headers); 

        return new \Aliyun\Log\Models\Response\CreateMachineGroupResponse($header);
    }

    public function updateMachineGroup(\Aliyun\Log\Models\Request\UpdateMachineGroupRequest $request){
        $params = array();
        $headers = array();
        $body=null;
        $groupName='';
        if($request->getMachineGroup()!==null){
          $body = json_encode($request->getMachineGroup()->toArray());
          $groupName=($request->getMachineGroup()->getGroupName()!==null)?$request->getMachineGroup()->getGroupName():'';
        }
        $headers ['Content-Type'] = 'application/json';
        $resource = '/machinegroups/'.$groupName;
        list($resp,$header) = $this->send("PUT",NULL,$body,$resource,$params,$headers);  
        return new \Aliyun\Log\Models\Response\UpdateMachineGroupResponse($header);
    }

    public function getMachineGroup(\Aliyun\Log\Models\Request\GetMachineGroupRequest $request){
        $params = array();
        $headers = array();

        $groupName = ($request->getGroupName()!==null)?$request->getGroupName():'';
        
        $resource = '/machinegroups/'.$groupName;
        list($resp,$header) = $this->send("GET",NULL,NULL,$resource,$params,$headers); 
        $requestId = isset ( $header ['x-log-requestid'] ) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson ( $resp, $requestId );
        return new \Aliyun\Log\Models\Response\GetMachineGroupResponse($resp,$header);
    }

    public function deleteMachineGroup(\Aliyun\Log\Models\Request\DeleteMachineGroupRequest $request){
        $params = array();
        $headers = array();

        $groupName = ($request->getGroupName()!==null)?$request->getGroupName():'';
        $resource = '/machinegroups/'.$groupName;
        list($resp,$header) = $this->send("DELETE",NULL,NULL,$resource,$params,$headers); 
        return new \Aliyun\Log\Models\Response\DeleteMachineGroupResponse($header);
    }

    public function listMachineGroups(\Aliyun\Log\Models\Request\ListMachineGroupsRequest $request){
        $params = array();
        $headers = array();

        if($request->getGroupName()!==null)$params['groupName'] = $request->getGroupName();
        if($request->getOffset()!==null)$params['offset'] = $request->getOffset();
        if($request->getSize()!==null)$params['size'] = $request->getSize();

        $resource = '/machinegroups';
        list($resp,$header) = $this->send("GET",NULL,NULL,$resource,$params,$headers); 
        $requestId = isset ( $header ['x-log-requestid'] ) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson ( $resp, $requestId );

        return new \Aliyun\Log\Models\Response\ListMachineGroupsResponse($resp,$header);
    }

    public function applyConfigToMachineGroup(\Aliyun\Log\Models\Request\ApplyConfigToMachineGroupRequest $request){
        $params = array();
        $headers = array();
        $configName=$request->getConfigName();
        $groupName=$request->getGroupName();
        $headers ['Content-Type'] = 'application/json';
        $resource = '/machinegroups/'.$groupName.'/configs/'.$configName;
        list($resp,$header) = $this->send("PUT",NULL,NULL,$resource,$params,$headers);  
        return new \Aliyun\Log\Models\Response\ApplyConfigToMachineGroupResponse($header);
    }

    public function removeConfigFromMachineGroup(\Aliyun\Log\Models\Request\RemoveConfigFromMachineGroupRequest $request){
        $params = array();
        $headers = array();
        $configName=$request->getConfigName();
        $groupName=$request->getGroupName();
        $headers ['Content-Type'] = 'application/json';
        $resource = '/machinegroups/'.$groupName.'/configs/'.$configName;
        list($resp,$header) = $this->send("DELETE",NULL,NULL,$resource,$params,$headers);  
        return new \Aliyun\Log\Models\Response\RemoveConfigFromMachineGroupResponse($header);
    }

    public function getMachine(\Aliyun\Log\Models\Request\GetMachineRequest $request){
        $params = array();
        $headers = array();

        $uuid = ($request->getUuid()!==null)?$request->getUuid():'';

        $resource = '/machines/'.$uuid;
        list($resp,$header) = $this->send("GET",NULL,NULL,$resource,$params,$headers);
        $requestId = isset ( $header ['x-log-requestid'] ) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson ( $resp, $requestId );
        return new \Aliyun\Log\Models\Response\GetMachineResponse($resp,$header);
    }

    public function createACL(\Aliyun\Log\Models\Request\CreateACLRequest $request){
        $params = array();
        $headers = array();
        $body=null;
        if($request->getAcl()!==null){
          $body = json_encode($request->getAcl()->toArray());
        }
        $headers ['Content-Type'] = 'application/json';
        $resource = '/acls';
        list($resp,$header) = $this->send("POST",NULL,$body,$resource,$params,$headers);
        $requestId = isset ( $header ['x-log-requestid'] ) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson ( $resp, $requestId );
        return new \Aliyun\Log\Models\Response\CreateACLResponse($resp,$header);
    }

    public function updateACL(\Aliyun\Log\Models\Request\UpdateACLRequest $request){
        $params = array();
        $headers = array();
        $body=null;
        $aclId='';
        if($request->getAcl()!==null){
          $body = json_encode($request->getAcl()->toArray());
          $aclId=($request->getAcl()->getAclId()!==null)?$request->getAcl()->getAclId():'';
        }
        $headers ['Content-Type'] = 'application/json';
        $resource = '/acls/'.$aclId;
        list($resp,$header) = $this->send("PUT",NULL,$body,$resource,$params,$headers);  
        return new \Aliyun\Log\Models\Response\UpdateACLResponse($header);
    }
    
    public function getACL(\Aliyun\Log\Models\Request\GetACLRequest $request){
        $params = array();
        $headers = array();

        $aclId = ($request->getAclId()!==null)?$request->getAclId():'';
        
        $resource = '/acls/'.$aclId;
        list($resp,$header) = $this->send("GET",NULL,NULL,$resource,$params,$headers); 
        $requestId = isset ( $header ['x-log-requestid'] ) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson ( $resp, $requestId );

        return new \Aliyun\Log\Models\Response\GetACLResponse($resp,$header);
    }
    
    public function deleteACL(\Aliyun\Log\Models\Request\DeleteACLRequest $request){
        $params = array();
        $headers = array();
        $aclId = ($request->getAclId()!==null)?$request->getAclId():'';
        $resource = '/acls/'.$aclId;
        list($resp,$header) = $this->send("DELETE",NULL,NULL,$resource,$params,$headers); 
        return new \Aliyun\Log\Models\Response\DeleteACLResponse($header);
    }
    
    public function listACLs(\Aliyun\Log\Models\Request\ListACLsRequest $request){
        $params = array();
        $headers = array();
        if($request->getPrincipleId()!==null)$params['principleId'] = $request->getPrincipleId();
        if($request->getOffset()!==null)$params['offset'] = $request->getOffset();
        if($request->getSize()!==null)$params['size'] = $request->getSize();

        $resource = '/acls';
        list($resp,$header) = $this->send("GET",NULL,NULL,$resource,$params,$headers); 
        $requestId = isset ( $header ['x-log-requestid'] ) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson ( $resp, $requestId );
        return new \Aliyun\Log\Models\Response\ListACLsResponse($resp,$header);
    }

}
