<?php
namespace YcOpen\CloudService;
class Validator
{
    protected $rules;
    protected $data;
    public function rules(array $rules)
    {
        $this->rules=$rules;
    }
    public function setData(Request $data)
    {
        $this->data=$data->toArray();
    }
    public function check()
    {
        foreach($this->rules as $key=>$value){
            if(!isset($this->data[$key])||empty($this->data[$key])){
                throw new Exception\ValidateException(json_encode(['message'=>"{$key}参数不能为空",'field'=>$key],JSON_UNESCAPED_UNICODE));
            }
        }
    }
}