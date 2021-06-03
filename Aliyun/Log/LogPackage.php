<?php

namespace Aliyun\Log;

// message LogPackage
class LogPackage
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
            //var_dump("LogPackage: Found $field type " . Protobuf::get_wiretype($wire) . " $limit bytes left");
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
                    $this->data_ = $tmp;
                    $limit -= $len;
                    break;
                case 2:
                    \ASSERT('$wire == 0');
                    $tmp = \Aliyun\Log\Protobuf::read_varint($fp, $limit);
                    if ($tmp === \false) {
                        throw new \Exception('Protobuf::read_varint returned false');
                    }
                    $this->uncompressSize_ = $tmp;
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
        if (!\is_null($this->data_)) {
            \fwrite($fp, "\n");
            \Aliyun\Log\Protobuf::write_varint($fp, \strlen($this->data_));
            \fwrite($fp, $this->data_);
        }
        if (!\is_null($this->uncompressSize_)) {
            \fwrite($fp, "\x10");
            \Aliyun\Log\Protobuf::write_varint($fp, $this->uncompressSize_);
        }
    }
    public function size()
    {
        $size = 0;
        if (!\is_null($this->data_)) {
            $l = \strlen($this->data_);
            $size += 1 + \Aliyun\Log\Protobuf::size_varint($l) + $l;
        }
        if (!\is_null($this->uncompressSize_)) {
            $size += 1 + \Aliyun\Log\Protobuf::size_varint($this->uncompressSize_);
        }
        return $size;
    }
    public function validateRequired()
    {
        if ($this->data_ === \null) {
            return \false;
        }
        return \true;
    }
    public function __toString()
    {
        return '' . \Aliyun\Log\Protobuf::toString('unknown', $this->_unknown) . \Aliyun\Log\Protobuf::toString('data_', $this->data_) . \Aliyun\Log\Protobuf::toString('uncompressSize_', $this->uncompressSize_);
    }
    // required bytes data = 1;
    private $data_ = \null;
    public function clearData()
    {
        $this->data_ = \null;
    }
    public function hasData()
    {
        return $this->data_ !== \null;
    }
    public function getData()
    {
        if ($this->data_ === \null) {
            return "";
        } else {
            return $this->data_;
        }
    }
    public function setData($value)
    {
        $this->data_ = $value;
    }
    // optional int32 uncompress_size = 2;
    private $uncompressSize_ = \null;
    public function clearUncompressSize()
    {
        $this->uncompressSize_ = \null;
    }
    public function hasUncompressSize()
    {
        return $this->uncompressSize_ !== \null;
    }
    public function getUncompressSize()
    {
        if ($this->uncompressSize_ === \null) {
            return 0;
        } else {
            return $this->uncompressSize_;
        }
    }
    public function setUncompressSize($value)
    {
        $this->uncompressSize_ = $value;
    }
    // @@protoc_insertion_point(class_scope:LogPackage)
}
