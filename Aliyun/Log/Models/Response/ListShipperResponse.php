<?php
namespace Aliyun\Log\Models\Response;

class ListShipperResponse extends \Aliyun\Log\Models\Response\Response {
    private $count;
    private $total;
    private $shippers;

    /**
     * @return mixed
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param mixed $count
     */
    public function setCount($count)
    {
        $this->count = $count;
    }

    /**
     * @return mixed
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param mixed $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * @return mixed
     */
    public function getShippers()
    {
        return $this->shippers;
    }

    /**
     * @param mixed $shippers
     */
    public function setShippers($shippers)
    {
        $this->shippers = $shippers;
    }


    /**
     * Aliyun_Log_Models_ListShipperResponse constructor
     *
     * @param array $resp
     *            GetLogs HTTP response body
     * @param array $header
     *            GetLogs HTTP response header
     */
    public function __construct($resp, $header) {
        parent::__construct ( $header );
        $this->count = $resp['count'];
        $this->total = $resp['total'];
        $this->shippers = $resp['shipper'];
    }
}
