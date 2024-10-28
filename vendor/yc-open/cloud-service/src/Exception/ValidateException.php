<?php
namespace YcOpen\CloudService\Exception;
use Exception;
use YcOpen\CloudService\Response\ResponseCode;

class ValidateException extends Exception
{
    protected $data;
    public function __construct($data,$code=ResponseCode::FAIL)
    {
        $data=json_decode($data);
        parent::__construct($data->message, $code);
    }
    /**
     * 获取字段名
     * @return string
     */
    public function getField()
    {
        return $this->data->field;
    }
}