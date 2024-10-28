<?php

namespace YcOpen\CloudService;

use YcOpen\CloudService\Response\ResponseCode;

class Cloud
{
    protected $request;
    protected $content;
    protected $data;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    public function __call($name, $arguments)
    {
        $this->request->$name(...$arguments);
        return $this;
    }
    /**
     * 发送请求
     * 
     * @return DataModel|string|bool
     */
    public function send()
    {
        $response = $this->request->Builder();
        if ($this->request->isDownFile()) {
            return $this->request->download();
        }
        $this->content = $response->getBody()->getContents();
        if ($this->request->isException()) {
            if ($response->getStatusCode() != 200) {
                throw new Exception\HttpException($this->content, $response->getStatusCode());
            }
            $this->data = json_decode($this->content, true);
            if (!isset($this->data['code'])) {
                throw new Exception\HttpResponseException($this->content);
            }
            if ($this->data['code'] === ResponseCode::LOGIN) {
                Request::Login()->outLogin();
                throw new Exception\HttpResponseException($this->content);
            } elseif ($this->data['code'] !== ResponseCode::SUCCESS) {
                throw new Exception\HttpResponseException($this->content);
            }
            return $this->request->setResponse($this->data['data']);
        }
        return $this->content;
    }
}
