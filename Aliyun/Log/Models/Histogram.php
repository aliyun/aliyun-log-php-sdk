<?php
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */

/**
 * The class used to present the result of log histogram status. For every log
 * histogram, it contains : from/to time range, hit log count and query
 * completed status.
 *
 * @author log service dev
 */
class Aliyun_Log_Models_Histogram {
    
    /**
     * @var integer the begin time
     */
    private $from;
    
    /**
     * @var integer the end time
     */
    private $to;
   
    /**
     * @var integer log count of histogram that query hits 
     */
    private $count;
    
    /**
     * @var string histogram query status(Complete or InComplete)
     */
    private $progress;
    
    /**
     * Aliyun_Log_Models_Histogram constructor
     *
     * @param integer $from
     *            the begin time
     * @param integer $to
     *            the end time
     * @param integer $count
     *            log count of histogram that query hits 
     * @param string $progress
     *            histogram query status(Complete or InComplete)
     */
    public function __construct($from, $to, $count, $progress) {
        $this->from = $from;
        $this->to = $to;
        $this->count = $count;
        $this->progress = $progress;
    }
    
    /**
     * Get begin time
     *
     * @return integer the begin time
     */
    public function getFrom() {
        return $this->from;
    }
    
    /**
     * Get the end time
     *
     * @return integer the end time
     */
    public function getTo() {
        return $this->to;
    }
    
    /**
     * Get log count of histogram that query hits
     *
     * @return integer log count of histogram that query hits
     */
    public function getCount() {
        return $this->count;
    }
    
    /**
     * Check if the histogram is completed
     *
     * @return bool true if this histogram is completed
     */
    public function isCompleted() {
        return $this->progress == 'Complete';
    }
}
