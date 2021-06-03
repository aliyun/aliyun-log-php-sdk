<?php

namespace Aliyun\Log;

class ProtobufEnum
{
    public static function toString($value)
    {
        if (\is_null($value)) {
            return \null;
        }
        if (\array_key_exists($value, self::$_values)) {
            return self::$_values[$value];
        }
        return 'UNKNOWN';
    }
}
