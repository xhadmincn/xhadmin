<?php

namespace loong\ApiStore;

use GuzzleHttp\Client;
use loong\ApiStore\response\Code;

class Http
{
    public $token = '';
    public $baseUrl = 'https://api.yidevs.com/app/';
    protected $contents;
    public function __construct()
    {
        $baseUrl = yd_env('YD_CLOUD_SERVICE_BASE_URL');
        if ($baseUrl) {
            $this->baseUrl = $baseUrl;
        }
    }
    /**
     * 设置token
     *
     * @param string $token
     * @return \loong\ApiStore\Http
     */
    public function setToken(string $token)
    {
        $this->token = $token;
        return $this;
    }
    protected function getClient()
    {
        return new Client([
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Accept' => 'application/json',
            ],
            'base_uri' => $this->baseUrl,
        ]);
    }
    /**
     * 返回结果
     *
     * @return DataModel
     */
    public function response()
    {
        if (!$this->contents) {
            throw new \Exception('返回结果为空');
        }
        $ret = json_decode($this->contents, true);
        if (!$ret) {
            throw new \Exception('返回结果解析失败');
        }
        if ($ret['code'] != Code::SUCCESS) {
            throw new \Exception($ret['msg'], $ret['code']);
        }
        return new DataModel($ret['data']);
    }
    /**
     * 获取响应内容
     *
     * @return string
     */
    public function getContents()
    {
        return $this->contents;
    }
    /**
     * 发送get请求
     *
     * @param string $url
     * @param array $params
     * @return \loong\ApiStore\Http
     */
    public function get(string $url, $params = [])
    {
        $client = $this->getClient();
        $response = $client->get($url, [
            'query' => $params,
        ]);
        $this->contents = $response->getBody()->getContents();
        return $this;
    }
    /**
     * 发送post请求
     *
     * @param string $url
     * @param array $params
     * @return \loong\ApiStore\Http
     */
    public function post(string $url, $params = [])
    {
        $client = $this->getClient();
        $response = $client->post($url, [
            'form_params' => $params,
        ]);
        $this->contents = $response->getBody()->getContents();
        return $this;
    }
    /**
     * 上传文件
     *
     * @param string $url
     * @param string|array $file
     * @param array $params
     * @return \loong\ApiStore\Http
     */
    public function upload(string $url, string|array $file, $params = [])
    {
        $multipart = [];
        if (is_array($file)) {
            foreach ($file as $key => $value) {
                if (!file_exists($value)) {
                    throw new \Exception("{$value};文件不存在");
                }
                $multipart[] = [
                    'name' => 'files[]',
                    'contents' => fopen($value, 'r'),
                ];
            }
        } else {
            if (!file_exists($file)) {
                throw new \Exception("{$file};文件不存在");
            }
            $multipart[] = [
                'name' => 'file',
                'contents' => fopen($file, 'r'),
            ];
        }
        foreach ($params as $key => $value) {
            $multipart[] = [
                'name' => $key,
                'contents' => $value,
            ];
        }
        $client = $this->getClient();
        $response = $client->post($url, [
            'multipart' => $multipart,
        ]);
        $this->contents = $response->getBody()->getContents();
        return $this;
    }
}
