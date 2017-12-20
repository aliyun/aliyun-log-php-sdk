<?php
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */

abstract class Aliyun_Log_Models_logLevel_LogLevel{
    const debug = 'debug';
    const info = 'info';
    const warn = 'warn';
    const error = 'error';

    private static $constCacheArray = NULL;

    private static function getConstants(){
        if(self::$constCacheArray == NULL){
            self::$constCacheArray = [];
        }
        $calledClass = get_called_class();
        if(!array_key_exists($calledClass, self::$constCacheArray)){
            $reflect = new ReflectionClass($calledClass);
            self::$constCacheArray[$calledClass] = $reflect ->getConstants();
        }
        return self::$constCacheArray[$calledClass];
    }

    public static function isValidName($name, $strict = false){
        $constans = self::getConstants();

        if($strict){
            return array_key_exists($name, $constans);
        }

        $keys = array_map('strtolower', array_keys($constans));
        return in_array(strtolower($name), $keys);
    }

    public static function isValidValue($value, $strict = true){
        $values = array_values(self::getConstants());
        return in_array($value, $values, $strict);
    }
}