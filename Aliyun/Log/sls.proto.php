<?php
// Please include the below file before sls_logs.proto.php
//require('protocolbuffers.inc.php');

// message Log.Content
class Log_Content {
  private $_unknown;

  function __construct($in = NULL, &$limit = PHP_INT_MAX) {
    if($in !== NULL) {
      if (is_string($in)) {
        $fp = fopen('php://memory', 'r+b');
        fwrite($fp, $in);
        rewind($fp);
      } else if (is_resource($in)) {
        $fp = $in;
      } else {
        throw new Exception('Invalid in parameter');
      }
      $this->read($fp, $limit);
    }
  }

  function read($fp, &$limit = PHP_INT_MAX) {
    while(!feof($fp) && $limit > 0) {
      $tag = Protobuf::read_varint($fp, $limit);
      if ($tag === false) break;
      $wire  = $tag & 0x07;
      $field = $tag >> 3;
      //var_dump("Log_Content: Found $field type " . Protobuf::get_wiretype($wire) . " $limit bytes left");
      switch($field) {
        case 1:
          ASSERT('$wire == 2');
          $len = Protobuf::read_varint($fp, $limit);
          if ($len === false)
            throw new Exception('Protobuf::read_varint returned false');
          if ($len > 0)
            $tmp = fread($fp, $len);
          else
            $tmp = '';
          if ($tmp === false)
            throw new Exception("fread($len) returned false");
          $this->key_ = $tmp;
          $limit-=$len;
          break;
        case 2:
          ASSERT('$wire == 2');
          $len = Protobuf::read_varint($fp, $limit);
          if ($len === false)
            throw new Exception('Protobuf::read_varint returned false');
          if ($len > 0)
            $tmp = fread($fp, $len);
          else
            $tmp = '';
          if ($tmp === false)
            throw new Exception("fread($len) returned false");
          $this->value_ = $tmp;
          $limit-=$len;
          break;
        default:
          $this->_unknown[$field . '-' . Protobuf::get_wiretype($wire)][] = Protobuf::read_field($fp, $wire, $limit);
      }
    }
    if (!$this->validateRequired())
      throw new Exception('Required fields are missing');
  }

  function write($fp) {
    if (!$this->validateRequired())
      throw new Exception('Required fields are missing');
    if (!is_null($this->key_)) {
      fwrite($fp, "\x0a");
      Protobuf::write_varint($fp, strlen($this->key_));
      fwrite($fp, $this->key_);
    }
    if (!is_null($this->value_)) {
      fwrite($fp, "\x12");
      Protobuf::write_varint($fp, strlen($this->value_));
      fwrite($fp, $this->value_);
    }
  }

  public function size() {
    $size = 0;
    if (!is_null($this->key_)) {
      $l = strlen($this->key_);
      $size += 1 + Protobuf::size_varint($l) + $l;
    }
    if (!is_null($this->value_)) {
      $l = strlen($this->value_);
      $size += 1 + Protobuf::size_varint($l) + $l;
    }
    return $size;
  }

  public function validateRequired() {
    if ($this->key_ === null) return false;
    if ($this->value_ === null) return false;
    return true;
  }

  public function __toString() {
    return ''
         . Protobuf::toString('unknown', $this->_unknown)
         . Protobuf::toString('key_', $this->key_)
         . Protobuf::toString('value_', $this->value_);
  }

  // required string Key = 1;

  private $key_ = null;
  public function clearKey() { $this->key_ = null; }
  public function hasKey() { return $this->key_ !== null; }
  public function getKey() { if($this->key_ === null) return ""; else return $this->key_; }
  public function setKey($value) { $this->key_ = $value; }

  // required string Value = 2;

  private $value_ = null;
  public function clearValue() { $this->value_ = null; }
  public function hasValue() { return $this->value_ !== null; }
  public function getValue() { if($this->value_ === null) return ""; else return $this->value_; }
  public function setValue($value) { $this->value_ = $value; }

  // @@protoc_insertion_point(class_scope:Log.Content)
}

// message Log
class Log {
  private $_unknown;

  function __construct($in = NULL, &$limit = PHP_INT_MAX) {
    if($in !== NULL) {
      if (is_string($in)) {
        $fp = fopen('php://memory', 'r+b');
        fwrite($fp, $in);
        rewind($fp);
      } else if (is_resource($in)) {
        $fp = $in;
      } else {
        throw new Exception('Invalid in parameter');
      }
      $this->read($fp, $limit);
    }
  }

  function read($fp, &$limit = PHP_INT_MAX) {
    while(!feof($fp) && $limit > 0) {
      $tag = Protobuf::read_varint($fp, $limit);
      if ($tag === false) break;
      $wire  = $tag & 0x07;
      $field = $tag >> 3;
      //var_dump("Log: Found $field type " . Protobuf::get_wiretype($wire) . " $limit bytes left");
      switch($field) {
        case 1:
          ASSERT('$wire == 0');
          $tmp = Protobuf::read_varint($fp, $limit);
          if ($tmp === false)
            throw new Exception('Protobuf::read_varint returned false');
          $this->time_ = $tmp;

          break;
        case 2:
          ASSERT('$wire == 2');
          $len = Protobuf::read_varint($fp, $limit);
          if ($len === false)
            throw new Exception('Protobuf::read_varint returned false');
          $limit-=$len;
          $this->contents_[] = new Log_Content($fp, $len);
          ASSERT('$len == 0');
          break;
        default:
          $this->_unknown[$field . '-' . Protobuf::get_wiretype($wire)][] = Protobuf::read_field($fp, $wire, $limit);
      }
    }
    if (!$this->validateRequired())
      throw new Exception('Required fields are missing');
  }

  function write($fp) {
    if (!$this->validateRequired())
      throw new Exception('Required fields are missing');
    if (!is_null($this->time_)) {
      fwrite($fp, "\x08");
      Protobuf::write_varint($fp, $this->time_);
    }
    if (!is_null($this->contents_))
      foreach($this->contents_ as $v) {
        fwrite($fp, "\x12");
        Protobuf::write_varint($fp, $v->size()); // message
        $v->write($fp);
      }
  }

  public function size() {
    $size = 0;
    if (!is_null($this->time_)) {
      $size += 1 + Protobuf::size_varint($this->time_);
    }
    if (!is_null($this->contents_))
      foreach($this->contents_ as $v) {
        $l = $v->size();
        $size += 1 + Protobuf::size_varint($l) + $l;
      }
    return $size;
  }

  public function validateRequired() {
    if ($this->time_ === null) return false;
    return true;
  }

  public function __toString() {
    return ''
         . Protobuf::toString('unknown', $this->_unknown)
         . Protobuf::toString('time_', $this->time_)
         . Protobuf::toString('contents_', $this->contents_);
  }

  // required uint32 Time = 1;

  private $time_ = null;
  public function clearTime() { $this->time_ = null; }
  public function hasTime() { return $this->time_ !== null; }
  public function getTime() { if($this->time_ === null) return 0; else return $this->time_; }
  public function setTime($value) { $this->time_ = $value; }

  // repeated .Log.Content Contents = 2;

  private $contents_ = null;
  public function clearContents() { $this->contents_ = null; }
  public function getContentsCount() { if ($this->contents_ === null ) return 0; else return count($this->contents_); }
  public function getContents($index) { return $this->contents_[$index]; }
  public function getContentsArray() { if ($this->contents_ === null ) return array(); else return $this->contents_; }
  public function setContents($index, $value) {$this->contents_[$index] = $value;	}
  public function addContents($value) { $this->contents_[] = $value; }
  public function addAllContents(array $values) { foreach($values as $value) {$this->contents_[] = $value;} }

  // @@protoc_insertion_point(class_scope:Log)
}

// message LogGroup
class LogGroup {
  private $_unknown;

  function __construct($in = NULL, &$limit = PHP_INT_MAX) {
    if($in !== NULL) {
      if (is_string($in)) {
        $fp = fopen('php://memory', 'r+b');
        fwrite($fp, $in);
        rewind($fp);
      } else if (is_resource($in)) {
        $fp = $in;
      } else {
        throw new Exception('Invalid in parameter');
      }
      $this->read($fp, $limit);
    }
  }

  function read($fp, &$limit = PHP_INT_MAX) {
    while(!feof($fp) && $limit > 0) {
      $tag = Protobuf::read_varint($fp, $limit);
      if ($tag === false) break;
      $wire  = $tag & 0x07;
      $field = $tag >> 3;
      //var_dump("LogGroup: Found $field type " . Protobuf::get_wiretype($wire) . " $limit bytes left");
      switch($field) {
        case 1:
          ASSERT('$wire == 2');
          $len = Protobuf::read_varint($fp, $limit);
          if ($len === false)
            throw new Exception('Protobuf::read_varint returned false');
          $limit-=$len;
          $this->logs_[] = new Log($fp, $len);
          ASSERT('$len == 0');
          break;
        case 2:
          ASSERT('$wire == 2');
          $len = Protobuf::read_varint($fp, $limit);
          if ($len === false)
            throw new Exception('Protobuf::read_varint returned false');
          if ($len > 0)
            $tmp = fread($fp, $len);
          else
            $tmp = '';
          if ($tmp === false)
            throw new Exception("fread($len) returned false");
          $this->category_ = $tmp;
          $limit-=$len;
          break;
        case 3:
          ASSERT('$wire == 2');
          $len = Protobuf::read_varint($fp, $limit);
          if ($len === false)
            throw new Exception('Protobuf::read_varint returned false');
          if ($len > 0)
            $tmp = fread($fp, $len);
          else
            $tmp = '';
          if ($tmp === false)
            throw new Exception("fread($len) returned false");
          $this->topic_ = $tmp;
          $limit-=$len;
          break;
        case 4:
          ASSERT('$wire == 2');
          $len = Protobuf::read_varint($fp, $limit);
          if ($len === false)
            throw new Exception('Protobuf::read_varint returned false');
          if ($len > 0)
            $tmp = fread($fp, $len);
          else
            $tmp = '';
          if ($tmp === false)
            throw new Exception("fread($len) returned false");
          $this->source_ = $tmp;
          $limit-=$len;
          break;
        default:
          $this->_unknown[$field . '-' . Protobuf::get_wiretype($wire)][] = Protobuf::read_field($fp, $wire, $limit);
      }
    }
    if (!$this->validateRequired())
      throw new Exception('Required fields are missing');
  }

  function write($fp) {
    if (!$this->validateRequired())
      throw new Exception('Required fields are missing');
    if (!is_null($this->logs_))
      foreach($this->logs_ as $v) {
        fwrite($fp, "\x0a");
        Protobuf::write_varint($fp, $v->size()); // message
        $v->write($fp);
      }
    if (!is_null($this->category_)) {
      fwrite($fp, "\x12");
      Protobuf::write_varint($fp, strlen($this->category_));
      fwrite($fp, $this->category_);
    }
    if (!is_null($this->topic_)) {
      fwrite($fp, "\x1a");
      Protobuf::write_varint($fp, strlen($this->topic_));
      fwrite($fp, $this->topic_);
    }
    if (!is_null($this->source_)) {
      fwrite($fp, "\"");
      Protobuf::write_varint($fp, strlen($this->source_));
      fwrite($fp, $this->source_);
    }
  }

  public function size() {
    $size = 0;
    if (!is_null($this->logs_))
      foreach($this->logs_ as $v) {
        $l = $v->size();
        $size += 1 + Protobuf::size_varint($l) + $l;
      }
    if (!is_null($this->category_)) {
      $l = strlen($this->category_);
      $size += 1 + Protobuf::size_varint($l) + $l;
    }
    if (!is_null($this->topic_)) {
      $l = strlen($this->topic_);
      $size += 1 + Protobuf::size_varint($l) + $l;
    }
    if (!is_null($this->source_)) {
      $l = strlen($this->source_);
      $size += 1 + Protobuf::size_varint($l) + $l;
    }
    return $size;
  }

  public function validateRequired() {
    return true;
  }

  public function __toString() {
    return ''
         . Protobuf::toString('unknown', $this->_unknown)
         . Protobuf::toString('logs_', $this->logs_)
         . Protobuf::toString('category_', $this->category_)
         . Protobuf::toString('topic_', $this->topic_)
         . Protobuf::toString('source_', $this->source_);
  }

  // repeated .Log Logs = 1;

  private $logs_ = null;
  public function clearLogs() { $this->logs_ = null; }
  public function getLogsCount() { if ($this->logs_ === null ) return 0; else return count($this->logs_); }
  public function getLogs($index) { return $this->logs_[$index]; }
  public function getLogsArray() { if ($this->logs_ === null ) return array(); else return $this->logs_; }
  public function setLogs($index, $value) {$this->logs_[$index] = $value;	}
  public function addLogs($value) { $this->logs_[] = $value; }
  public function addAllLogs(array $values) { foreach($values as $value) {$this->logs_[] = $value;} }

  // optional string Category = 2;

  private $category_ = null;
  public function clearCategory() { $this->category_ = null; }
  public function hasCategory() { return $this->category_ !== null; }
  public function getCategory() { if($this->category_ === null) return ""; else return $this->category_; }
  public function setCategory($value) { $this->category_ = $value; }

  // optional string Topic = 3;

  private $topic_ = null;
  public function clearTopic() { $this->topic_ = null; }
  public function hasTopic() { return $this->topic_ !== null; }
  public function getTopic() { if($this->topic_ === null) return ""; else return $this->topic_; }
  public function setTopic($value) { $this->topic_ = $value; }

  // optional string Source = 4;

  private $source_ = null;
  public function clearSource() { $this->source_ = null; }
  public function hasSource() { return $this->source_ !== null; }
  public function getSource() { if($this->source_ === null) return ""; else return $this->source_; }
  public function setSource($value) { $this->source_ = $value; }

  // @@protoc_insertion_point(class_scope:LogGroup)
}

// message LogGroupList
class LogGroupList {
  private $_unknown;

  function __construct($in = NULL, &$limit = PHP_INT_MAX) {
    if($in !== NULL) {
      if (is_string($in)) {
        $fp = fopen('php://memory', 'r+b');
        fwrite($fp, $in);
        rewind($fp);
      } else if (is_resource($in)) {
        $fp = $in;
      } else {
        throw new Exception('Invalid in parameter');
      }
      $this->read($fp, $limit);
    }
  }

  function read($fp, &$limit = PHP_INT_MAX) {
    while(!feof($fp) && $limit > 0) {
      $tag = Protobuf::read_varint($fp, $limit);
      if ($tag === false) break;
      $wire  = $tag & 0x07;
      $field = $tag >> 3;
      //var_dump("LogGroupList: Found $field type " . Protobuf::get_wiretype($wire) . " $limit bytes left");
      switch($field) {
        case 1:
          ASSERT('$wire == 2');
          $len = Protobuf::read_varint($fp, $limit);
          if ($len === false)
            throw new Exception('Protobuf::read_varint returned false');
          $limit-=$len;
          $this->logGroupList_[] = new LogGroup($fp, $len);
          ASSERT('$len == 0');
          break;
        default:
          $this->_unknown[$field . '-' . Protobuf::get_wiretype($wire)][] = Protobuf::read_field($fp, $wire, $limit);
      }
    }
    if (!$this->validateRequired())
      throw new Exception('Required fields are missing');
  }

  function write($fp) {
    if (!$this->validateRequired())
      throw new Exception('Required fields are missing');
    if (!is_null($this->logGroupList_))
      foreach($this->logGroupList_ as $v) {
        fwrite($fp, "\x0a");
        Protobuf::write_varint($fp, $v->size()); // message
        $v->write($fp);
      }
  }

  public function size() {
    $size = 0;
    if (!is_null($this->logGroupList_))
      foreach($this->logGroupList_ as $v) {
        $l = $v->size();
        $size += 1 + Protobuf::size_varint($l) + $l;
      }
    return $size;
  }

  public function validateRequired() {
    return true;
  }

  public function __toString() {
    return ''
         . Protobuf::toString('unknown', $this->_unknown)
         . Protobuf::toString('logGroupList_', $this->logGroupList_);
  }

  // repeated .LogGroup logGroupList = 1;

  private $logGroupList_ = null;
  public function clearLogGroupList() { $this->logGroupList_ = null; }
  public function getLogGroupListCount() { if ($this->logGroupList_ === null ) return 0; else return count($this->logGroupList_); }
  public function getLogGroupList($index) { return $this->logGroupList_[$index]; }
  public function getLogGroupListArray() { if ($this->logGroupList_ === null ) return array(); else return $this->logGroupList_; }
  public function setLogGroupList($index, $value) {$this->logGroupList_[$index] = $value;	}
  public function addLogGroupList($value) { $this->logGroupList_[] = $value; }
  public function addAllLogGroupList(array $values) { foreach($values as $value) {$this->logGroupList_[] = $value;} }

  // @@protoc_insertion_point(class_scope:LogGroupList)
}

