Copyright
=========

Copyright (C) Alibaba Cloud Computing
All rights reserved


Aliyun SLS Client SDK for PHP
=============================

This project provides SLS client SDK libraries that make it easy to access Simple Log Service put logs, get log stores, get topics, get histograms and get logs. For more information about the SDK, you can see about the html files in the docs file.
The SDK supports PHP version>=5.2.


Sample
======

```PHP
<?php
	require_once 'Log_Autoload.php'; // or wherever Log_Autoload.php is located
	
	$endpoint = 'http://cn-hangzhou.sls.aliyuncs.com/';
	$accessKeyId = 'your_access_key_id';
	$accessKey = 'your_access_key';
	$client = new Aliyun_Log_Client($endpoint, $accessKeyId, $accessKey);
	
	$project = 'your_project';
	$request = new Aliyun_Log_Models_ListLogstoresRequest($project);
	
	try {
		$response = $client->ListLogstores($request);
		var_dump($response);
	} catch (SLSException $ex) {
        var_dump($ex);
    } catch (Exception $ex) {
        var_dump($ex);
    }
```

First, you need to require_once Log_Autoload.php wherever Log_Autoload.php is located.
Second, you need to declare Aliyun_Log_Client $client.
Third, you need to build a Aliyun_Log_Models_Request $request.
Fourth, you can make a function call like $client->ListLogstores($request), and get SLS response $response.
In the end, you can get what you want $response.
If you get SLSException, you can get the error code, error message, request id if SLS sever has a response.
You can see the sample/sample.php for more information about the key client features.


About the html files
====================

The html files were created by PHP Document.
http://www.phpdoc.org
php phpDocumentor.phar --title="SLS_PHP_SDK" --defaultpackagename="SLS_PHP_SDK" --template="responsive" -d Aliyun -t docs
