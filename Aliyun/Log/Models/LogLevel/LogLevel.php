<?php
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */

class Aliyun_Log_Models_LogLevel_LogLevel{
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
        if($other instanceof Aliyun_Log_Models_LogLevel_LogLevel) {
            if($this->level == $other->level) {
                return true;
            }
        } else {
            return false;
        }
    }

    public static function getLevelDebug(){
        if(!isset(self::$constCacheArray[Aliyun_Log_Models_LogLevel_LogLevel::debug])){
            self::$constCacheArray[Aliyun_Log_Models_LogLevel_LogLevel::debug] = new Aliyun_Log_Models_LogLevel_LogLevel('debug');
        }
        return self::$constCacheArray[Aliyun_Log_Models_LogLevel_LogLevel::debug];
    }

    public static function getLevelInfo(){
        if(!isset(self::$constCacheArray[Aliyun_Log_Models_LogLevel_LogLevel::info])){
            self::$constCacheArray[Aliyun_Log_Models_LogLevel_LogLevel::info] = new Aliyun_Log_Models_LogLevel_LogLevel('info');
        }
        return self::$constCacheArray[Aliyun_Log_Models_LogLevel_LogLevel::info];
    }

    public static function getLevelWarn(){
        if(!isset(self::$constCacheArray[Aliyun_Log_Models_LogLevel_LogLevel::warn])){
            self::$constCacheArray[Aliyun_Log_Models_LogLevel_LogLevel::warn] = new Aliyun_Log_Models_LogLevel_LogLevel('warn');
        }
        return self::$constCacheArray[Aliyun_Log_Models_LogLevel_LogLevel::warn];
    }

    public static function getLevelError(){
        if(!isset(self::$constCacheArray[Aliyun_Log_Models_LogLevel_LogLevel::error])){
            self::$constCacheArray[Aliyun_Log_Models_LogLevel_LogLevel::error] = new Aliyun_Log_Models_LogLevel_LogLevel('error');
        }
        return self::$constCacheArray[Aliyun_Log_Models_LogLevel_LogLevel::error];
    }

    public static function getLevelStr(Aliyun_Log_Models_LogLevel_LogLevel $logLevel){

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