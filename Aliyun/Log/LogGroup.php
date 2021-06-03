<?php

namespace Aliyun\Log;

// message LogGroup
class LogGroup
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
            //var_dump("LogGroup: Found $field type " . Protobuf::get_wiretype($wire) . " $limit bytes left");
            switch ($field) {
                case 1:
                    \ASSERT('$wire == 2');
                    $len = \Aliyun\Log\Protobuf::read_varint($fp, $limit);
                    if ($len === \false) {
                        throw new \Exception('Protobuf::read_varint returned false');
                    }
                    $limit -= $len;
                    $this->logs_[] = new \Aliyun\Log\Log($fp, $len);
                    \ASSERT('$len == 0');
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
                    $this->category_ = $tmp;
                    $limit -= $len;
                    break;
                case 3:
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
                    $this->topic_ = $tmp;
                    $limit -= $len;
                    break;
                case 4:
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
                    $this->source_ = $tmp;
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
        if (!\is_null($this->logs_)) {
            foreach ($this->logs_ as $v) {
                \fwrite($fp, "\n");
                \Aliyun\Log\Protobuf::write_varint($fp, $v->size());
                // message
                $v->write($fp);
            }
        }
        if (!\is_null($this->category_)) {
            \fwrite($fp, "\x12");
            \Aliyun\Log\Protobuf::write_varint($fp, \strlen($this->category_));
            \fwrite($fp, $this->category_);
        }
        if (!\is_null($this->topic_)) {
            \fwrite($fp, "\x1a");
            \Aliyun\Log\Protobuf::write_varint($fp, \strlen($this->topic_));
            \fwrite($fp, $this->topic_);
        }
        if (!\is_null($this->source_)) {
            \fwrite($fp, "\"");
            \Aliyun\Log\Protobuf::write_varint($fp, \strlen($this->source_));
            \fwrite($fp, $this->source_);
        }
    }
    public function size()
    {
        $size = 0;
        if (!\is_null($this->logs_)) {
            foreach ($this->logs_ as $v) {
                $l = $v->size();
                $size += 1 + \Aliyun\Log\Protobuf::size_varint($l) + $l;
            }
        }
        if (!\is_null($this->category_)) {
            $l = \strlen($this->category_);
            $size += 1 + \Aliyun\Log\Protobuf::size_varint($l) + $l;
        }
        if (!\is_null($this->topic_)) {
            $l = \strlen($this->topic_);
            $size += 1 + \Aliyun\Log\Protobuf::size_varint($l) + $l;
        }
        if (!\is_null($this->source_)) {
            $l = \strlen($this->source_);
            $size += 1 + \Aliyun\Log\Protobuf::size_varint($l) + $l;
        }
        return $size;
    }
    public function validateRequired()
    {
        return \true;
    }
    public function __toString()
    {
        return '' . \Aliyun\Log\Protobuf::toString('unknown', $this->_unknown) . \Aliyun\Log\Protobuf::toString('logs_', $this->logs_) . \Aliyun\Log\Protobuf::toString('category_', $this->category_) . \Aliyun\Log\Protobuf::toString('topic_', $this->topic_) . \Aliyun\Log\Protobuf::toString('source_', $this->source_);
    }
    // repeated .Log Logs = 1;
    private $logs_ = \null;
    public function clearLogs()
    {
        $this->logs_ = \null;
    }
    public function getLogsCount()
    {
        if ($this->logs_ === \null) {
            return 0;
        } else {
            return \count($this->logs_);
        }
    }
    public function getLogs($index)
    {
        return $this->logs_[$index];
    }
    public function getLogsArray()
    {
        if ($this->logs_ === \null) {
            return array();
        } else {
            return $this->logs_;
        }
    }
    public function setLogs($index, $value)
    {
        $this->logs_[$index] = $value;
    }
    public function addLogs($value)
    {
        $this->logs_[] = $value;
    }
    public function addAllLogs(array $values)
    {
        foreach ($values as $value) {
            $this->logs_[] = $value;
        }
    }
    // optional string Category = 2;
    private $category_ = \null;
    public function clearCategory()
    {
        $this->category_ = \null;
    }
    public function hasCategory()
    {
        return $this->category_ !== \null;
    }
    public function getCategory()
    {
        if ($this->category_ === \null) {
            return "";
        } else {
            return $this->category_;
        }
    }
    public function setCategory($value)
    {
        $this->category_ = $value;
    }
    // optional string Topic = 3;
    private $topic_ = \null;
    public function clearTopic()
    {
        $this->topic_ = \null;
    }
    public function hasTopic()
    {
        return $this->topic_ !== \null;
    }
    public function getTopic()
    {
        if ($this->topic_ === \null) {
            return "";
        } else {
            return $this->topic_;
        }
    }
    public function setTopic($value)
    {
        $this->topic_ = $value;
    }
    // optional string Source = 4;
    private $source_ = \null;
    public function clearSource()
    {
        $this->source_ = \null;
    }
    public function hasSource()
    {
        return $this->source_ !== \null;
    }
    public function getSource()
    {
        if ($this->source_ === \null) {
            return "";
        } else {
            return $this->source_;
        }
    }
    public function setSource($value)
    {
        $this->source_ = $value;
    }
    // @@protoc_insertion_point(class_scope:LogGroup)
}
