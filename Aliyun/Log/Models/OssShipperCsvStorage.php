<?php
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */

class Aliyun_Log_Models_OssShipperCsvStorage extends Aliyun_Log_Models_OssShipperStorage{
    private $columns;
    private $delimiter = ',';
    private $quote = '';
    private $lineFeed = '\n';
    private $nullIdentifier = '';
    private $header = false;

    /**
     * @return mixed
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param mixed $columns
     */
    public function setColumns($columns)
    {
        $this->columns = $columns;
    }

    /**
     * @return string
     */
    public function getDelimiter(): string
    {
        return $this->delimiter;
    }

    /**
     * @param string $delimiter
     */
    public function setDelimiter(string $delimiter)
    {
        $this->delimiter = $delimiter;
    }

    /**
     * @return string
     */
    public function getQuote(): string
    {
        return $this->quote;
    }

    /**
     * @param string $quote
     */
    public function setQuote(string $quote)
    {
        $this->quote = $quote;
    }

    /**
     * @return string
     */
    public function getNullIdentifier(): string
    {
        return $this->nullIdentifier;
    }

    /**
     * @param string $nullIdentifier
     */
    public function setNullIdentifier(string $nullIdentifier)
    {
        $this->nullIdentifier = $nullIdentifier;
    }

    /**
     * @return bool
     */
    public function isHeader(): bool
    {
        return $this->header;
    }

    /**
     * @param bool $header
     */
    public function setHeader(bool $header)
    {
        $this->header = $header;
    }

    /**
     * @return string
     */
    public function getLineFeed(): string
    {
        return $this->lineFeed;
    }

    /**
     * @param string $lineFeed
     */
    public function setLineFeed(string $lineFeed)
    {
        $this->lineFeed = $lineFeed;
    }

    public function to_json_object(){
        $detail =  array(
            'columns' => $this->columns,
            'delimiter' => $this->delimiter,
            'quote' => $this->quote,
            'lineFeed' => $this->lineFeed,
            'nullIdentifier' => $this->nullIdentifier,
            'header' => $this->header
        );
        return array(
            'detail' => $detail,
            'format' => 'csv'
        );
    }
}