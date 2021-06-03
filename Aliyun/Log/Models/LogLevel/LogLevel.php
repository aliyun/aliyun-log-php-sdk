<?php
namespace Aliyun\Log\Models\LogLevel;

/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */
class LogLevel{
    const debug = 'debug';
    const info = 'info';
    const warn = 'warn';
    const error = 'error';

    private static $constCacheArray = NULL;

    private $level;

    /**
     * Constructor
     *
     * @param string $level
     */
    private function __construct($level) {
        $this->level = $level;
    }

    /**
     * Compares two logger levels.
     *
     * @param LoggerLevels $other
     * @return boolean
     */
    public function equals($other) {
        if($other instanceof \Aliyun\Log\Models\LogLevel\LogLevel) {
            if($this->level == $other->level) {
                return true;
            }
        } else {
            return false;
        }
    }

    public static function getLevelDebug(){
        if(!isset(self::$constCacheArray[\Aliyun\Log\Models\LogLevel\LogLevel::debug])){
            self::$constCacheArray[\Aliyun\Log\Models\LogLevel\LogLevel::debug] = new \Aliyun\Log\Models\LogLevel\LogLevel('debug');
        }
        return self::$constCacheArray[\Aliyun\Log\Models\LogLevel\LogLevel::debug];
    }

    public static function getLevelInfo(){
        if(!isset(self::$constCacheArray[\Aliyun\Log\Models\LogLevel\LogLevel::info])){
            self::$constCacheArray[\Aliyun\Log\Models\LogLevel\LogLevel::info] = new \Aliyun\Log\Models\LogLevel\LogLevel('info');
        }
        return self::$constCacheArray[\Aliyun\Log\Models\LogLevel\LogLevel::info];
    }

    public static function getLevelWarn(){
        if(!isset(self::$constCacheArray[\Aliyun\Log\Models\LogLevel\LogLevel::warn])){
            self::$constCacheArray[\Aliyun\Log\Models\LogLevel\LogLevel::warn] = new \Aliyun\Log\Models\LogLevel\LogLevel('warn');
        }
        return self::$constCacheArray[\Aliyun\Log\Models\LogLevel\LogLevel::warn];
    }

    public static function getLevelError(){
        if(!isset(self::$constCacheArray[\Aliyun\Log\Models\LogLevel\LogLevel::error])){
            self::$constCacheArray[\Aliyun\Log\Models\LogLevel\LogLevel::error] = new \Aliyun\Log\Models\LogLevel\LogLevel('error');
        }
        return self::$constCacheArray[\Aliyun\Log\Models\LogLevel\LogLevel::error];
    }

    public static function getLevelStr(\Aliyun\Log\Models\LogLevel\LogLevel $logLevel){

        $logLevelStr = '';
        if(null === $logLevel){
            $logLevelStr = 'info';
        }
        switch ($logLevel->level){
            case "error":
                $logLevelStr= 'error';
                break;
            case "warn":
                $logLevelStr= 'warn';
                break;
            case "info":
                $logLevelStr= 'info';
                break;
            case "debug":
                $logLevelStr= 'debug';
                break;
        }
        return $logLevelStr;
    }
}