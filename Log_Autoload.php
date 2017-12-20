<?php
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */

$version = '0.6.0';

function Aliyun_Log_PHP_Client_Autoload($className) {
    $classPath = explode('_', $className);
    if ($classPath[0] == 'Aliyun') {
        if(count($classPath)>5)
            $classPath = array_slice($classPath, 0, 5);
        if(strpos($className, 'Request') !== false){
            $lastPath = end($classPath);
            array_pop($classPath);
            array_push($classPath,'Request');
            array_push($classPath, $lastPath);
        }
        if(strpos($className, 'Response') !== false){
            $lastPath = end($classPath);
            array_pop($classPath);
            array_push($classPath,'Response');
            array_push($classPath, $lastPath);
        }
        $filePath = dirname(__FILE__) . '/' . implode('/', $classPath) . '.php';
        if (file_exists($filePath))
            require_once($filePath);
    }
}

spl_autoload_register('Aliyun_Log_PHP_Client_Autoload');
