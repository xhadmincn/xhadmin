<?php
namespace YcOpen\CloudService\Exception;
use Exception;
class HttpResponseException extends Exception
{
    protected $data;
    public function __construct($data)
    {
        $this->data=json_decode($data);
        if ($this->data==null) {
            $this->data=new \stdClass();
            $this->data->msg=$data;
            $this->data->code=0;
        }
        $message=$this->data->msg;
        $code=$this->data->code;
        parent::__construct($message, $code);
    }
    public function getData()
    {
        return $this->data;
    }
}