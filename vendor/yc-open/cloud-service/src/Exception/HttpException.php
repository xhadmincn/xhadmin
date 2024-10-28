<?php
namespace YcOpen\CloudService\Exception;
use Exception;
use YcOpen\CloudService\Response\ResponseCode;

class HttpException extends Exception
{
    protected $response;
    public function __construct($response,$code=ResponseCode::FAIL)
    {
        $this->response=$response;
        parent::__construct('服务器错误', $code);
    }
    public function getData()
    {
        return $this->response;
    }
}