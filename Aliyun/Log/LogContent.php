<?php

namespace Aliyun\Log;

// Please include the below file before sls_logs.proto.php
//require('protocolbuffers.inc.php');
// message Log.Content
class LogContent
{
    private $_unknown;
    function __construct($in = \NULL, &$limit = \PHP_INT_MAX)
    {
        if ($in !== \NULL) {
            if (\is_string($in)) {
                $fp = \fopen('php://memory', 'r+b');
                \fwrite($fp, $in);
                \rewind($fp);
            } else {
                if (\is_resource($in)) {
                    $fp = $in;
                } else {
                    throw new \Exception('Invalid in parameter');
                }
            }
            $this->read($fp, $limit);
        }
    }
    function read($fp, &$limit = \PHP_INT_MAX)
    {
        while (!\feof($fp) && $limit > 0) {
            $tag = \Aliyun\Log\Protobuf::read_varint($fp, $limit);
            if ($tag === \false) {
                break;
            }
            $wire = $tag & 0x7;
            $field = $tag >> 3;
            //var_dump("Log_Content: Found $field type " . Protobuf::get_wiretype($wire) . " $limit bytes left");
            switch ($field) {
                case 1:
                    \ASSERT('$wire == 2');
                    $len = \Aliyun\Log\Protobuf::read_varint($fp, $limit);
                    if ($len === \false) {
                        throw new \Exception('Protobuf::read_varint returned false');
                    }
                    if ($len > 0) {
                        $tmp = \fread($fp, $len);
                    } else {
                        $tmp = '';
                    }
                    if ($tmp === \false) {
                        throw new \Exception("fread({$len}) returned false");
                    }
                    $this->key_ = $tmp;
                    $limit -= $len;
                    break;
                case 2:
                    \ASSERT('$wire == 2');
                    $len = \Aliyun\Log\Protobuf::read_varint($fp, $limit);
                    if ($len === \false) {
                        throw new \Exception('Protobuf::read_varint returned false');
                    }
                    if ($len > 0) {
                        $tmp = \fread($fp, $len);
                    } else {
                        $tmp = '';
                    }
                    if ($tmp === \false) {
                        throw new \Exception("fread({$len}) returned false");
                    }
                    $this->value_ = $tmp;
                    $limit -= $len;
                    break;
                default:
                    $this->_unknown[$field . '-' . \Aliyun\Log\Protobuf::get_wiretype($wire)][] = \Aliyun\Log\Protobuf::read_field($fp, $wire, $limit);
            }
        }
        if (!$this->validateRequired()) {
            throw new \Exception('Required fields are missing');
        }
    }
    function write($fp)
    {
        if (!$this->validateRequired()) {
            throw new \Exception('Required fields are missing');
        }
        if (!\is_null($this->key_)) {
            \fwrite($fp, "\n");
            \Aliyun\Log\Protobuf::write_varint($fp, \strlen($this->key_));
            \fwrite($fp, $this->key_);
        }
        if (!\is_null($this->value_)) {
            \fwrite($fp, "\x12");
            \Aliyun\Log\Protobuf::write_varint($fp, \strlen($this->value_));
            \fwrite($fp, $this->value_);
        }
    }
    public function size()
    {
        $size = 0;
        if (!\is_null($this->key_)) {
            $l = \strlen($this->key_);
            $size += 1 + \Aliyun\Log\Protobuf::size_varint($l) + $l;
        }
        if (!\is_null($this->value_)) {
            $l = \strlen($this->value_);
            $size += 1 + \Aliyun\Log\Protobuf::size_varint($l) + $l;
        }
        return $size;
    }
    public function validateRequired()
    {
        if ($this->key_ === \null) {
            return \false;
        }
        if ($this->value_ === \null) {
            return \false;
        }
        return \true;
    }
    public function __toString()
    {
        return '' . \Aliyun\Log\Protobuf::toString('unknown', $this->_unknown) . \Aliyun\Log\Protobuf::toString('key_', $this->key_) . \Aliyun\Log\Protobuf::toString('value_', $this->value_);
    }
    // required string Key = 1;
    private $key_ = \null;
    public function clearKey()
    {
        $this->key_ = \null;
    }
    public function hasKey()
    {
        return $this->key_ !== \null;
    }
    public function getKey()
    {
        if ($this->key_ === \null) {
            return "";
        } else {
            return $this->key_;
        }
    }
    public function setKey($value)
    {
        $this->key_ = $value;
    }
    // required string Value = 2;
    private $value_ = \null;
    public function clearValue()
    {
        $this->value_ = \null;
    }
    public function hasValue()
    {
        return $this->value_ !== \null;
    }
    public function getValue()
    {
        if ($this->value_ === \null) {
            return "";
        } else {
            return $this->value_;
        }
    }
    public function setValue($value)
    {
        $this->value_ = $value;
    }
    // @@protoc_insertion_point(class_scope:Log.Content)
}
