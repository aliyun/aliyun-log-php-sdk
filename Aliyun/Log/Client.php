<?php
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */
date_default_timezone_set ( 'Asia/Shanghai' );

require_once realpath ( dirname ( __FILE__ ) . '/../../Log_Autoload.php' );
require_once realpath ( dirname ( __FILE__ ) . '/requestcore.class.php' );
require_once realpath ( dirname ( __FILE__ ) . '/sls.proto.php' );
require_once realpath ( dirname ( __FILE__ ) . '/protocolbuffers.inc.php' );

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
class Aliyun_Log_Client {

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
        $this->source = Aliyun_Log_Util::getLocalIp();
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
        $this->isRowIp = Aliyun_Log_Util::isIp ( $endpoint );
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
     * @throws Aliyun_Log_Exception
     */
    protected function parseToJson($resBody, $requestId) {
        if (! $resBody)
          return NULL;
        
        $result = json_decode ( $resBody, true );
        if ($result === NULL){
          throw new Aliyun_Log_Exception ( 'BadResponse', "Bad format,not json: $resBody", $requestId );
        }
        return $result;
    }
    
    /**
     * @return array
     */
    protected function getHttpResponse($method, $url, $body, $headers) {
        $request = new RequestCore ( $url );
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
     * @throws Aliyun_Log_Exception
     */
    private function sendRequest($method, $url, $body, $headers) {
        try {
            list ( $responseCode, $header, $resBody ) = 
                    $this->getHttpResponse ( $method, $url, $body, $headers );
        } catch ( Exception $ex ) {
            throw new Aliyun_Log_Exception ( $ex->getMessage (), $ex->__toString () );
        }
        
        $requestId = isset ( $header ['x-log-requestid'] ) ? $header ['x-log-requestid'] : '';

        if ($responseCode == 200) {
          return array ($resBody,$header);
        } 
        else {
            $exJson = $this->parseToJson ( $resBody, $requestId );
            if (isset($exJson ['error_code']) && isset($exJson ['error_message'])) {
                throw new Aliyun_Log_Exception ( $exJson ['error_code'], 
                        $exJson ['error_message'], $requestId );
            } else {
                if ($exJson) {
                    $exJson = ' The return json is ' . json_encode($exJson);
                } else {
                    $exJson = '';
                }
                throw new Aliyun_Log_Exception ( 'RequestError',
                        "Request is failed. Http code is $responseCode.$exJson", $requestId );
            }
        }
    }
    
    /**
     * @return array
     * @throws Aliyun_Log_Exception
     */
    private function send($method, $project, $body, $resource, $params, $headers) {
        if ($body) {
            $headers ['Content-Length'] = strlen ( $body );
            if(isset($headers ["x-log-bodyrawsize"])==false)
                $headers ["x-log-bodyrawsize"] = 0;
            $headers ['Content-MD5'] = Aliyun_Log_Util::calMD5 ( $body );
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
        $signature = Aliyun_Log_Util::getRequestAuthorization ( $method, $resource, $this->accessKey,$this->stsToken, $params, $headers );
        $headers ['Authorization'] = "LOG $this->accessKeyId:$signature";
        
        $url = $resource;
        if ($params)
            $url .= '?' . Aliyun_Log_Util::urlEncode ( $params );
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
     * @param Aliyun_Log_Models_PutLogsRequest $request the PutLogs request parameters class
     * @throws Aliyun_Log_Exception
     * @return Aliyun_Log_Models_PutLogsResponse
     */
    public function putLogs(Aliyun_Log_Models_PutLogsRequest $request) {
        if (count ( $request->getLogitems () ) > 4096)
            throw new Aliyun_Log_Exception ( 'InvalidLogSize', "logItems' length exceeds maximum limitation: 4096 lines." );
        
        $logGroup = new LogGroup ();
        $topic = $request->getTopic () !== null ? $request->getTopic () : '';
        $logGroup->setTopic ( $request->getTopic () );
        $source = $request->getSource ();
        
        if ( ! $source )
            $source = $this->source;
        $logGroup->setSource ( $source );
        $logitems = $request->getLogitems ();
        foreach ( $logitems as $logItem ) {
            $log = new Aliyun_Log ();
            $log->setTime ( $logItem->getTime () );
            $content = $logItem->getContents ();
            foreach ( $content as $key => $value ) {
                $content = new Log_Content ();
                $content->setKey ( $key );
                $content->setValue ( $value );
                $log->addContents ( $content );
            }

            $logGroup->addLogs ( $log );
        }

        $body = Aliyun_Log_Util::toBytes( $logGroup );
        unset ( $logGroup );
        
        $bodySize = strlen ( $body );
        if ($bodySize > 3 * 1024 * 1024) // 3 MB
            throw new Aliyun_Log_Exception ( 'InvalidLogSize', "logItems' size exceeds maximum limitation: 3 MB." );
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
        return new Aliyun_Log_Models_PutLogsResponse ( $header );
    }

    /**
     * create shipper service
     * @param Aliyun_Log_Models_CreateShipperRequest $request
     * return Aliyun_Log_Models_CreateShipperResponse
     */
    public function createShipper(Aliyun_Log_Models_CreateShipperRequest $request){
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
        return new Aliyun_Log_Models_CreateShipperResponse($resp, $header);
    }

    /**
     * create shipper service
     * @param Aliyun_Log_Models_UpdateShipperRequest $request
     * return Aliyun_Log_Models_UpdateShipperResponse
     */
    public function updateShipper(Aliyun_Log_Models_UpdateShipperRequest $request){
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
        return new Aliyun_Log_Models_UpdateShipperResponse($resp, $header);
    }

    /**
     * get shipper tasks list, max 48 hours duration supported
     * @param Aliyun_Log_Models_GetShipperTasksRequest $request
     * return Aliyun_Log_Models_GetShipperTasksResponse
     */
    public function getShipperTasks(Aliyun_Log_Models_GetShipperTasksRequest $request){
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
        return new Aliyun_Log_Models_GetShipperTasksResponse($resp, $header);
    }

    /**
     * retry shipper tasks list by task ids
     * @param Aliyun_Log_Models_RetryShipperTasksRequest $request
     * return Aliyun_Log_Models_RetryShipperTasksResponse
     */
    public function retryShipperTasks(Aliyun_Log_Models_RetryShipperTasksRequest $request){
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
        return new Aliyun_Log_Models_RetryShipperTasksResponse($resp, $header);
    }

    /**
     * delete shipper service
     * @param Aliyun_Log_Models_DeleteShipperRequest $request
     * return Aliyun_Log_Models_DeleteShipperResponse
     */
    public function deleteShipper(Aliyun_Log_Models_DeleteShipperRequest $request){
        $headers = array();
        $params = array();
        $resource = "/logstores/".$request->getLogStore()."/shipper/".$request->getShipperName();
        $project = $request->getProject () !== null ? $request->getProject () : '';
        $headers["x-log-bodyrawsize"] = 0;
        $headers["Content-Type"] = "application/json";

        list($resp, $header) = $this->send("DELETE", $project,null,$resource,$params,$headers);
        $requestId = isset($header['x-log-requestid']) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson($resp, $requestId);
        return new Aliyun_Log_Models_DeleteShipperResponse($resp, $header);
    }

    /**
     * get shipper config service
     * @param Aliyun_Log_Models_GetShipperConfigRequest $request
     * return Aliyun_Log_Models_GetShipperConfigResponse
     */
    public function getShipperConfig(Aliyun_Log_Models_GetShipperConfigRequest $request){
        $headers = array();
        $params = array();
        $resource = "/logstores/".$request->getLogStore()."/shipper/".$request->getShipperName();
        $project = $request->getProject () !== null ? $request->getProject () : '';
        $headers["x-log-bodyrawsize"] = 0;
        $headers["Content-Type"] = "application/json";

        list($resp, $header) = $this->send("GET", $project,null,$resource,$params,$headers);
        $requestId = isset($header['x-log-requestid']) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson($resp, $requestId);
        return new Aliyun_Log_Models_GetShipperConfigResponse($resp, $header);
    }

    /**
     * list shipper service
     * @param Aliyun_Log_Models_ListShipperRequest $request
     * return Aliyun_Log_Models_ListShipperResponse
     */
    public function listShipper(Aliyun_Log_Models_ListShipperRequest $request){
        $headers = array();
        $params = array();
        $resource = "/logstores/".$request->getLogStore()."/shipper";
        $project = $request->getProject () !== null ? $request->getProject () : '';
        $headers["x-log-bodyrawsize"] = 0;
        $headers["Content-Type"] = "application/json";

        list($resp, $header) = $this->send("GET", $project,null,$resource,$params,$headers);
        $requestId = isset($header['x-log-requestid']) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson($resp, $requestId);
        return new Aliyun_Log_Models_ListShipperResponse($resp, $header);
    }

    /**
     * create logstore 
     * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
     *
     * @param Aliyun_Log_Models_CreateLogstoreRequest $request the CreateLogStore request parameters class.
     * @throws Aliyun_Log_Exception
     * return Aliyun_Log_Models_CreateLogstoreResponse
     */
    public function createLogstore(Aliyun_Log_Models_CreateLogstoreRequest $request){
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
        return new Aliyun_Log_Models_CreateLogstoreResponse($resp,$header);
    }
    /**
     * update logstore 
     * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
     *
     * @param Aliyun_Log_Models_UpdateLogstoreRequest $request the UpdateLogStore request parameters class.
     * @throws Aliyun_Log_Exception
     * return Aliyun_Log_Models_UpdateLogstoreResponse
     */
    public function updateLogstore(Aliyun_Log_Models_UpdateLogstoreRequest $request){
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
        return new Aliyun_Log_Models_UpdateLogstoreResponse($resp,$header);
    }
    /**
     * List all logstores of requested project.
     * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
     *
     * @param Aliyun_Log_Models_ListLogstoresRequest $request the ListLogstores request parameters class.
     * @throws Aliyun_Log_Exception
     * @return Aliyun_Log_Models_ListLogstoresResponse
     */
    public function listLogstores(Aliyun_Log_Models_ListLogstoresRequest $request) {
        $headers = array ();
        $params = array ();
        $resource = '/logstores';
        $project = $request->getProject () !== null ? $request->getProject () : '';
        list ( $resp, $header ) = $this->send ( "GET", $project, NULL, $resource, $params, $headers );
        $requestId = isset ( $header ['x-log-requestid'] ) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson ( $resp, $requestId );
        return new Aliyun_Log_Models_ListLogstoresResponse ( $resp, $header );
    }

    /**
     * Delete logstore
     * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
     *
     * @param Aliyun_Log_Models_DeleteLogstoreRequest $request the DeleteLogstores request parameters class.
     * @throws Aliyun_Log_Exception
     * @return Aliyun_Log_Models_DeleteLogstoresResponse
     */
    public function deleteLogstore(Aliyun_Log_Models_DeleteLogstoreRequest $request) {
        $headers = array ();
        $params = array ();
        $project = $request->getProject () !== null ? $request->getProject () : '';
        $logstore = $request -> getLogstore() != null ? $request -> getLogstore() :"";
        $resource = "/logstores/$logstore";
        list ( $resp, $header ) = $this->send ( "DELETE", $project, NULL, $resource, $params, $headers );
        $requestId = isset ( $header ['x-log-requestid'] ) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson ( $resp, $requestId );
        return new Aliyun_Log_Models_DeleteLogstoreResponse ( $resp, $header );
    }

    /**
     * List all topics in a logstore.
     * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
     *
     * @param Aliyun_Log_Models_ListTopicsRequest $request the ListTopics request parameters class.
     * @throws Aliyun_Log_Exception
     * @return Aliyun_Log_Models_ListTopicsResponse
     */
    public function listTopics(Aliyun_Log_Models_ListTopicsRequest $request) {
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
        return new Aliyun_Log_Models_ListTopicsResponse ( $resp, $header );
    }

    /**
     * Get histograms of requested query from log service.
     * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
     *
     * @param Aliyun_Log_Models_GetHistogramsRequest $request the GetHistograms request parameters class.
     * @throws Aliyun_Log_Exception
     * @return array(json body, http header)
     */
    public function getHistogramsJson(Aliyun_Log_Models_GetHistogramsRequest $request) {
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
     * @param Aliyun_Log_Models_GetHistogramsRequest $request the GetHistograms request parameters class.
     * @throws Aliyun_Log_Exception
     * @return Aliyun_Log_Models_GetHistogramsResponse
     */
    public function getHistograms(Aliyun_Log_Models_GetHistogramsRequest $request) {
        $ret = $this->getHistogramsJson($request);
        $resp = $ret[0];
        $header = $ret[1];
        return new Aliyun_Log_Models_GetHistogramsResponse ( $resp, $header );
    }

    /**
     * Get logs from Log service.
     * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
     *
     * @param Aliyun_Log_Models_GetLogsRequest $request the GetLogs request parameters class.
     * @throws Aliyun_Log_Exception
     * @return array(json body, http header)
     */
    public function getLogsJson(Aliyun_Log_Models_GetLogsRequest $request) {
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
     * @param Aliyun_Log_Models_GetLogsRequest $request the GetLogs request parameters class.
     * @throws Aliyun_Log_Exception
     * @return Aliyun_Log_Models_GetLogsResponse
     */
    public function getLogs(Aliyun_Log_Models_GetLogsRequest $request) {
        $ret = $this->getLogsJson($request);
        $resp = $ret[0];
        $header = $ret[1];
        return new Aliyun_Log_Models_GetLogsResponse ( $resp, $header );
    }

    /**
     * Get logs from Log service.
     * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
     *
     * @param Aliyun_Log_Models_GetProjectLogsRequest $request the GetLogs request parameters class.
     * @throws Aliyun_Log_Exception
     * @return array(json body, http header)
     */
    public function getProjectLogsJson(Aliyun_Log_Models_GetProjectLogsRequest $request) {
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
     * @param Aliyun_Log_Models_GetProjectLogsRequest $request the GetLogs request parameters class.
     * @throws Aliyun_Log_Exception
     * @return Aliyun_Log_Models_GetLogsResponse
     */
    public function getProjectLogs(Aliyun_Log_Models_GetProjectLogsRequest $request) {
        $ret = $this->getProjectLogsJson($request);
        $resp = $ret[0];
        $header = $ret[1];
        return new Aliyun_Log_Models_GetLogsResponse ( $resp, $header );
    }
    
    /**
     * Get logs from Log service with shardid conditions.
     * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
     *
     * @param Aliyun_Log_Models_BatchGetLogsRequest $request the BatchGetLogs request parameters class.
     * @throws Aliyun_Log_Exception
     * @return Aliyun_Log_Models_BatchGetLogsResponse
     */
    public function batchGetLogs(Aliyun_Log_Models_BatchGetLogsRequest $request) {
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
      if($resp===false)$resp = new LogGroupList();
      
      else {
          $resp = new LogGroupList($resp);
      }
      return new Aliyun_Log_Models_BatchGetLogsResponse ( $resp, $header );
    }

    /**
     * List Shards from Log service with Project and logstore conditions.
     * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
     *
     * @param Aliyun_Log_Models_ListShardsRequest $request the ListShards request parameters class.
     * @throws Aliyun_Log_Exception
     * @return Aliyun_Log_Models_ListShardsResponse
     */
    public function listShards(Aliyun_Log_Models_ListShardsRequest $request) {
        $params = array();
        $headers = array();
        $project = $request->getProject()!==null?$request->getProject():'';
        $logstore = $request->getLogstore()!==null?$request->getLogstore():'';

        $resource='/logstores/'.$logstore.'/shards';
        list($resp,$header) = $this->send("GET",$project,NULL,$resource,$params,$headers); 
        $requestId = isset ( $header ['x-log-requestid'] ) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson ( $resp, $requestId );
        return new Aliyun_Log_Models_ListShardsResponse ( $resp, $header );
    }

    /**
     * split a shard into two shards  with Project and logstore and shardId and midHash conditions.
     * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
     *
     * @param Aliyun_Log_Models_SplitShardRequest $request the SplitShard request parameters class.
     * @throws Aliyun_Log_Exception
     * @return Aliyun_Log_Models_ListShardsResponse
     */
    public function splitShard(Aliyun_Log_Models_SplitShardRequest $request) {
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
        return new Aliyun_Log_Models_ListShardsResponse ( $resp, $header );
    }
    /**
     * merge two shards into one shard with Project and logstore and shardId and conditions.
     * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
     *
     * @param Aliyun_Log_Models_MergeShardsRequest $request the MergeShards request parameters class.
     * @throws Aliyun_Log_Exception
     * @return Aliyun_Log_Models_ListShardsResponse
     */
    public function MergeShards(Aliyun_Log_Models_MergeShardsRequest $request) {
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
        return new Aliyun_Log_Models_ListShardsResponse ( $resp, $header );
    }
    /**
     * delete a read only shard with Project and logstore and shardId conditions.
     * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
     *
     * @param Aliyun_Log_Models_DeleteShardRequest $request the DeleteShard request parameters class.
     * @throws Aliyun_Log_Exception
     * @return Aliyun_Log_Models_ListShardsResponse
     */
    public function DeleteShard(Aliyun_Log_Models_DeleteShardRequest $request) {
        $params = array();
        $headers = array();
        $project = $request->getProject()!==null?$request->getProject():'';
        $logstore = $request->getLogstore()!==null?$request->getLogstore():'';
        $shardId = $request -> getShardId()!= null ? $request -> getShardId():-1;

        $resource='/logstores/'.$logstore.'/shards/'.$shardId;
        list($resp,$header) = $this->send("DELETE",$project,NULL,$resource,$params,$headers); 
        $requestId = isset ( $header ['x-log-requestid'] ) ? $header ['x-log-requestid'] : '';
        return new Aliyun_Log_Models_DeleteShardResponse ( $header );
    }

    /**
     * Get cursor from Log service.
     * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
     *
     * @param Aliyun_Log_Models_GetCursorRequest $request the GetCursor request parameters class.
     * @throws Aliyun_Log_Exception
     * @return Aliyun_Log_Models_GetCursorResponse
     */
    public function getCursor(Aliyun_Log_Models_GetCursorRequest $request){
      $params = array();
      $headers = array();
      $project = $request->getProject()!==null?$request->getProject():'';
      $logstore = $request->getLogstore()!==null?$request->getLogstore():'';
      $shardId = $request->getShardId()!==null?$request->getShardId():'';
      $mode = $request->getMode()!==null?$request->getMode():'';
      $fromTime = $request->getFromTime()!==null?$request->getFromTime():-1;

      if((empty($mode) xor $fromTime==-1)==false){
        if(!empty($mode))
          throw new Aliyun_Log_Exception ( 'RequestError',"Request is failed. Mode and fromTime can not be not empty simultaneously");
        else
          throw new Aliyun_Log_Exception ( 'RequestError',"Request is failed. Mode and fromTime can not be empty simultaneously");
      }
      if(!empty($mode) && strcmp($mode,'begin')!==0 && strcmp($mode,'end')!==0)
        throw new Aliyun_Log_Exception ( 'RequestError',"Request is failed. Mode value invalid:$mode");
      if($fromTime!==-1 && (is_integer($fromTime)==false || $fromTime<0))
        throw new Aliyun_Log_Exception ( 'RequestError',"Request is failed. FromTime value invalid:$fromTime");
      $params['type']='cursor';
      if($fromTime!==-1)$params['from']=$fromTime;
      else $params['mode'] = $mode;
      $resource='/logstores/'.$logstore.'/shards/'.$shardId;
      list($resp,$header) = $this->send("GET",$project,NULL,$resource,$params,$headers); 
      $requestId = isset ( $header ['x-log-requestid'] ) ? $header ['x-log-requestid'] : '';
      $resp = $this->parseToJson ( $resp, $requestId );
      return new Aliyun_Log_Models_GetCursorResponse($resp,$header);
    }

    public function createConfig(Aliyun_Log_Models_CreateConfigRequest $request){
        $params = array();
        $headers = array();
        $body=null;
        if($request->getConfig()!==null){
          $body = json_encode($request->getConfig()->toArray());
        }
        $headers ['Content-Type'] = 'application/json';
        $resource = '/configs';
        list($resp,$header) = $this->send("POST",NULL,$body,$resource,$params,$headers); 
        return new Aliyun_Log_Models_CreateConfigResponse($header);
    }

    public function updateConfig(Aliyun_Log_Models_UpdateConfigRequest $request){
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
        return new Aliyun_Log_Models_UpdateConfigResponse($header);
    }

    public function getConfig(Aliyun_Log_Models_GetConfigRequest $request){
        $params = array();
        $headers = array();

        $configName = ($request->getConfigName()!==null)?$request->getConfigName():'';
        
        $resource = '/configs/'.$configName;
        list($resp,$header) = $this->send("GET",NULL,NULL,$resource,$params,$headers); 
        $requestId = isset ( $header ['x-log-requestid'] ) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson ( $resp, $requestId );
        return new Aliyun_Log_Models_GetConfigResponse($resp,$header);
    }

    public function deleteConfig(Aliyun_Log_Models_DeleteConfigRequest $request){
        $params = array();
        $headers = array();
        $configName = ($request->getConfigName()!==null)?$request->getConfigName():'';
        $resource = '/configs/'.$configName;
        list($resp,$header) = $this->send("DELETE",NULL,NULL,$resource,$params,$headers); 
        return new Aliyun_Log_Models_DeleteConfigResponse($header);
    }

    public function listConfigs(Aliyun_Log_Models_ListConfigsRequest $request){
        $params = array();
        $headers = array();

        if($request->getConfigName()!==null)$params['configName'] = $request->getConfigName();
        if($request->getOffset()!==null)$params['offset'] = $request->getOffset();
        if($request->getSize()!==null)$params['size'] = $request->getSize();

        $resource = '/configs';
        list($resp,$header) = $this->send("GET",NULL,NULL,$resource,$params,$headers); 
        $requestId = isset ( $header ['x-log-requestid'] ) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson ( $resp, $requestId );
        return new Aliyun_Log_Models_ListConfigsResponse($resp,$header);
    }
    
    public function createMachineGroup(Aliyun_Log_Models_CreateMachineGroupRequest $request){
        $params = array();
        $headers = array();
        $body=null;
        if($request->getMachineGroup()!==null){
          $body = json_encode($request->getMachineGroup()->toArray());
        }
        $headers ['Content-Type'] = 'application/json';
        $resource = '/machinegroups';
        list($resp,$header) = $this->send("POST",NULL,$body,$resource,$params,$headers); 

        return new Aliyun_Log_Models_CreateMachineGroupResponse($header);
    }

    public function updateMachineGroup(Aliyun_Log_Models_UpdateMachineGroupRequest $request){
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
        return new Aliyun_Log_Models_UpdateMachineGroupResponse($header);
    }

    public function getMachineGroup(Aliyun_Log_Models_GetMachineGroupRequest $request){
        $params = array();
        $headers = array();

        $groupName = ($request->getGroupName()!==null)?$request->getGroupName():'';
        
        $resource = '/machinegroups/'.$groupName;
        list($resp,$header) = $this->send("GET",NULL,NULL,$resource,$params,$headers); 
        $requestId = isset ( $header ['x-log-requestid'] ) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson ( $resp, $requestId );
        return new Aliyun_Log_Models_GetMachineGroupResponse($resp,$header);
    }

    public function deleteMachineGroup(Aliyun_Log_Models_DeleteMachineGroupRequest $request){
        $params = array();
        $headers = array();

        $groupName = ($request->getGroupName()!==null)?$request->getGroupName():'';
        $resource = '/machinegroups/'.$groupName;
        list($resp,$header) = $this->send("DELETE",NULL,NULL,$resource,$params,$headers); 
        return new Aliyun_Log_Models_DeleteMachineGroupResponse($header);
    }

    public function listMachineGroups(Aliyun_Log_Models_ListMachineGroupsRequest $request){
        $params = array();
        $headers = array();

        if($request->getGroupName()!==null)$params['groupName'] = $request->getGroupName();
        if($request->getOffset()!==null)$params['offset'] = $request->getOffset();
        if($request->getSize()!==null)$params['size'] = $request->getSize();

        $resource = '/machinegroups';
        list($resp,$header) = $this->send("GET",NULL,NULL,$resource,$params,$headers); 
        $requestId = isset ( $header ['x-log-requestid'] ) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson ( $resp, $requestId );

        return new Aliyun_Log_Models_ListMachineGroupsResponse($resp,$header);
    }

    public function applyConfigToMachineGroup(Aliyun_Log_Models_ApplyConfigToMachineGroupRequest $request){
        $params = array();
        $headers = array();
        $configName=$request->getConfigName();
        $groupName=$request->getGroupName();
        $headers ['Content-Type'] = 'application/json';
        $resource = '/machinegroups/'.$groupName.'/configs/'.$configName;
        list($resp,$header) = $this->send("PUT",NULL,NULL,$resource,$params,$headers);  
        return new Aliyun_Log_Models_ApplyConfigToMachineGroupResponse($header);
    }

    public function removeConfigFromMachineGroup(Aliyun_Log_Models_RemoveConfigFromMachineGroupRequest $request){
        $params = array();
        $headers = array();
        $configName=$request->getConfigName();
        $groupName=$request->getGroupName();
        $headers ['Content-Type'] = 'application/json';
        $resource = '/machinegroups/'.$groupName.'/configs/'.$configName;
        list($resp,$header) = $this->send("DELETE",NULL,NULL,$resource,$params,$headers);  
        return new Aliyun_Log_Models_RemoveConfigFromMachineGroupResponse($header);
    }

    public function getMachine(Aliyun_Log_Models_GetMachineRequest $request){
        $params = array();
        $headers = array();

        $uuid = ($request->getUuid()!==null)?$request->getUuid():'';

        $resource = '/machines/'.$uuid;
        list($resp,$header) = $this->send("GET",NULL,NULL,$resource,$params,$headers);
        $requestId = isset ( $header ['x-log-requestid'] ) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson ( $resp, $requestId );
        return new Aliyun_Log_Models_GetMachineResponse($resp,$header);
    }

    public function createACL(Aliyun_Log_Models_CreateACLRequest $request){
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
        return new Aliyun_Log_Models_CreateACLResponse($resp,$header);
    }

    public function updateACL(Aliyun_Log_Models_UpdateACLRequest $request){
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
        return new Aliyun_Log_Models_UpdateACLResponse($header);
    }
    
    public function getACL(Aliyun_Log_Models_GetACLRequest $request){
        $params = array();
        $headers = array();

        $aclId = ($request->getAclId()!==null)?$request->getAclId():'';
        
        $resource = '/acls/'.$aclId;
        list($resp,$header) = $this->send("GET",NULL,NULL,$resource,$params,$headers); 
        $requestId = isset ( $header ['x-log-requestid'] ) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson ( $resp, $requestId );

        return new Aliyun_Log_Models_GetACLResponse($resp,$header);
    }
    
    public function deleteACL(Aliyun_Log_Models_DeleteACLRequest $request){
        $params = array();
        $headers = array();
        $aclId = ($request->getAclId()!==null)?$request->getAclId():'';
        $resource = '/acls/'.$aclId;
        list($resp,$header) = $this->send("DELETE",NULL,NULL,$resource,$params,$headers); 
        return new Aliyun_Log_Models_DeleteACLResponse($header);
    }
    
    public function listACLs(Aliyun_Log_Models_ListACLsRequest $request){
        $params = array();
        $headers = array();
        if($request->getPrincipleId()!==null)$params['principleId'] = $request->getPrincipleId();
        if($request->getOffset()!==null)$params['offset'] = $request->getOffset();
        if($request->getSize()!==null)$params['size'] = $request->getSize();

        $resource = '/acls';
        list($resp,$header) = $this->send("GET",NULL,NULL,$resource,$params,$headers); 
        $requestId = isset ( $header ['x-log-requestid'] ) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson ( $resp, $requestId );
        return new Aliyun_Log_Models_ListACLsResponse($resp,$header);
    }

}

