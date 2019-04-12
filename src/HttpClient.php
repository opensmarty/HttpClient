<?php
// +----------------------------------------------------------------------
// | shuguo HttpClient
// +----------------------------------------------------------------------
// | Copyright (c) 2018~2050 opensmarty All rights reserved.
// +----------------------------------------------------------------------
// | Website: https://www.cnblogs.com/opensmarty
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: opensmarty <opensmarty@gmail.com>
// +----------------------------------------------------------------------
// | DateTime: 2019/3/27 20:51
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | HttpClient基础类
// +----------------------------------------------------------------------

namespace Guzzle;

use GuzzleHttp\Client;

/**
 * Class HttpClient
 */
class HttpClient
{
    /**
     * @var string $baseUrl
     * @desc 基础路由
     */
    protected $baseUrl;

    /**
     * @var string $baseApi
     * @desc 基础路由
     */
    protected $baseApi;

    /**
     * @var Object $httpClient
     * @desc httpClient对象
     */
    private $httpClient;

    /**
     * @var array $options
     * @desc 请求参数（包含header，body， query, form_data等）
     */
    private $options;

    /**
     * HttpClient constructor.
     * @param null $baseUrl
     * @param bool $https
     */
    public function __construct($baseUrl = null, $https = false)
    {
        $this->setbaseUrl($baseUrl, $https);
        $this->options = [];
    }

    /**
     * @title 设置请求基础路径
     * @param  string $baseUrl
     * @param bool    $https
     */
    public function setbaseUrl($baseUrl, $https = false)
    {
        $baseUrl = !is_null($baseUrl) ? $baseUrl : PocCmsConfig::SGSLM_API_HOST;
        if (false !== strpos($baseUrl, 'http')) {
            $this->baseUrl = $baseUrl;
        } else {
            $protocol      = $https ? 'https://' : 'http://';
            $this->baseUrl = $protocol . $baseUrl;
        }
    }

    /**
     * @title 获取请求路径
     * @return string
     */
    public function getbaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @title 设置跟api路径
     * @param string|null $baseApi
     */
    public function setBaseApi($baseApi = null)
    {
        if (is_null($baseApi)) {
            $baseApi = PocCmsConfig::SGSLM_API_PATH;
        }

        $this->baseApi = $baseApi;
    }

    /**
     * @title 获取跟api路径
     * return string|null baseApi
     */
    public function getBaseApi()
    {
        return $this->baseApi;
    }

    /**
     * @param string|null $baseApi 基础路由
     * @param string|null $bearer
     * @return $this
     */
    public function getHttpClient($baseApi = null, $bearer = null)
    {
        // 实例化httpClient对象
        if (!$this->httpClient) {
            $this->httpClient = new Client([
                'base_uri' => $this->baseUrl,
                'timeout'  => 2.0
            ]);
        }

        // 初始化基础api路由
        $this->setBaseApi($baseApi);

        // 配置默认header
        if (is_null($bearer)) {
            $this->withBearer($bearer);
        }

        return $this;
    }

    public function withAuth($user, $password)
    {
        $this->options['auth'] = [$user, $password];

        return $this;
    }

    public function withJson(array $data)
    {
        $this->options['json'] = $data;

        return $this;
    }

    public function withQuery(array $data)
    {
        $this->options['query'] = $data;

        return $this;
    }

    public function withForm(array $data)
    {
        $this->options['form_params'] = $data;

        return $this;
    }

    public function withMultipart(array $data)
    {
        $this->options['multipart'] = $data;

        return $this;
    }

    public function withBearer($bearer = null, $json = true)
    {
        $this->initHeaders();

        if (is_null($bearer)) {
            $bearer = PocCmsConfig::SGSLM_API_TOKEN;
        }

        if ($json) {
            $this->options['headers']['Content-Type'] = "application/json";
        }

        $this->options['headers']['Authorization'] = "Bearer $bearer";

        return $this;
    }

    public function withHeaders($headers)
    {
        $this->options['headers'] = array_merge_recursive($this->options['headers'], $headers);

        return $this;
    }

    /**
     * init the headers for the request.
     */
    private function initHeaders()
    {
        if (!array_key_exists('headers', $this->options)) {
            $this->options['headers'] = [];
        }
    }

    /**
     * @param string $url           url path
     * @param bool   $async         flag to set up this request as asynchronous
     * @param null   $thenCallback  onSuccess callback if $async is true
     * @param null   $catchCallback onError callback if $async is true
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException on synchronous requests
     */
    public function get($url, $async = false, $thenCallback = null, $catchCallback = null)
    {
        return $this->request('GET', $url, $async, $thenCallback, $catchCallback);
    }

    /**
     * @param string $url           url path
     * @param bool   $async         flag to set up this request as asynchronous
     * @param null   $thenCallback  onSuccess callback if $async is true
     * @param null   $catchCallback onError callback if $async is true
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException on synchronous requests
     * @throws \Exception calling this method without set any data
     */
    public function post($url, $async = false, $thenCallback = null, $catchCallback = null)
    {
        return $this->request('POST', $url, $async, $thenCallback, $catchCallback);
    }

    /**
     * @param string $url           url path
     * @param bool   $async         flag to set up this request as asynchronous
     * @param null   $thenCallback  onSuccess callback if $async is true
     * @param null   $catchCallback onError callback if $async is true
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException on synchronous requests
     * @throws \Exception calling this method without set any data
     */
    public function put($url, $async = false, $thenCallback = null, $catchCallback = null)
    {
        return $this->request('PUT', $url, $async, $thenCallback, $catchCallback);
    }


    /**
     * @param string $url           url path
     * @param bool   $async         flag to set up this request as asynchronous
     * @param null   $thenCallback  onSuccess callback if $async is true
     * @param null   $catchCallback onError callback if $async is true
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException on synchronous requests
     * @throws \Exception calling this method without set any data
     */
    public function delete($url, $async = false, $thenCallback = null, $catchCallback = null)
    {
        return $this->request('DELETE', $url, $async, $thenCallback, $catchCallback);
    }

    /**
     * Like get() but decode synchronous responses to json
     * @param string $url           url path
     * @param bool   $async         flag to set up this request as asynchronous
     * @param null   $thenCallback  onSuccess callback if $async is true
     * @param null   $catchCallback onError callback if $async is true
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException on synchronous requests
     */
    public function getJson($url, $async = false, $thenCallback = null, $catchCallback = null)
    {
        return $this->requestJson('GET', $url, $async, $thenCallback, $catchCallback);
    }

    /**
     * Like post() but decode synchronous responses to json
     * @param string $url           url path
     * @param bool   $async         flag to set up this request as asynchronous
     * @param null   $thenCallback  onSuccess callback if $async is true
     * @param null   $catchCallback onError callback if $async is true
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException on synchronous requests
     * @throws \Exception calling this method without set any data
     */
    public function postJson($url, $async = false, $thenCallback = null, $catchCallback = null)
    {
        $this->checkData();

        return $this->requestJson('POST', $url, $async, $thenCallback, $catchCallback);
    }

    /**
     * Like put() but decode synchronous responses to json
     * @param string $url           url path
     * @param bool   $async         flag to set up this request as asynchronous
     * @param null   $thenCallback  onSuccess callback if $async is true
     * @param null   $catchCallback onError callback if $async is true
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException on synchronous requests
     * @throws \Exception calling this method without set any data
     */
    public function putJson($url, $async = false, $thenCallback = null, $catchCallback = null)
    {
        $this->checkData();

        return $this->requestJson('PUT', $url, $async, $thenCallback, $catchCallback);
    }

    /**
     * Like delete() but decode synchronous responses to json
     * @param string $url           url path
     * @param bool   $async         flag to set up this request as asynchronous
     * @param null   $thenCallback  onSuccess callback if $async is true
     * @param null   $catchCallback onError callback if $async is true
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException on synchronous requests
     * @throws \Exception calling this method without set any data
     */
    public function deleteJson($url, $async = false, $thenCallback = null, $catchCallback = null)
    {
        $this->checkData();

        return $this->requestJson('DELETE', $url, $async, $thenCallback, $catchCallback);
    }

    /**
     * Like request() but decode synchronous responses to json
     * @param string $method        request $method
     * @param string $url           url path
     * @param bool   $async         flag to set up this request as asynchronous
     * @param null   $thenCallback  onSuccess callback if $async is true
     * @param null   $catchCallback onError callback if $async is true
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException on synchronous requests
     */
    public function requestJson($method, $url, $async = false, $thenCallback = null, $catchCallback = null)
    {
        if (!$async) {
            $response = $this->httpClient->request($method, $this->baseApi . $url, $this->options);
            if (200 !== $response->getStatusCode) {
                Log::write("MatterMost中心接口{$url}调用未返回信息", Log::ERR);
            }

            return json_decode($response->getBody(), true);
        } else {
            $this->httpClient->requestAsync($method, $this->baseApi . $url, $this->options)->then($thenCallback, $catchCallback);
        }
    }

    /**
     * @title Upload file.
     * @param  string $url
     * @param array   $files
     * @param array   $form
     * @param array   $query
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function upload($url, $files = [], $form = [], $query = [])
    {
        try {
            $multipart = [];
            foreach ($files as $name => $path) {
                $multipart[] = [
                    'name'     => $name,
                    'contents' => fopen($path, 'r'),
                ];
            }
            foreach ($form as $name => $contents) {
                $multipart[] = compact('name', 'contents');
            }

            return $this->withQuery($query)->withMultipart($multipart)->postJson($url);

        } catch (\Exception $ex) {
            Log::write("MatterMost中心接口{$url}调用异常:" . $ex->getMessage(), Log::ERR);

            return null;
        }

        return null;
    }

    /**
     * @param string $method        request method
     * @param string $url           url path
     * @param bool   $async         flag to set up this request as asynchronous
     * @param null   $thenCallback  onSuccess callback if $async is true
     * @param null   $catchCallback onError callback if $async is true
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException on synchronous requests
     */
    public function request($method, $url, $async = false, $thenCallback = null, $catchCallback = null)
    {
        try {
            if (!$async) {
                $response = $this->httpClient->request(strtoupper($method), $this->baseApi . $url, $this->options);
                if (200 !== $response->getStatusCode) {
                    Log::write("MatterMost中心接口{$url}调用未返回信息", Log::ERR);
                }

                return $response;
            } else {
                $this->httpClient->requestAsync(strtoupper($method), $this->baseApi . $url, $this->options)->then($thenCallback, $catchCallback);
            }
        } catch (\Exception $ex) {
            Log::write("MatterMost中心接口{$url}调用异常:" . $ex->getMessage(), Log::ERR);

            return null;
        }

        return null;
    }

    /**
     * @throws \Exception
     */
    private function checkData()
    {
        if (!array_key_exists('json', $this->options) && !array_key_exists('form_params', $this->options)) {
            throw new \Exception('Data not set with json or form, please call setJson() or setForm() first');
        }
        if (array_key_exists('json', $this->options) && array_key_exists('form_params', $this->options)) {
            throw new \Exception('Data set as json and form, please choose only one method to send data');
        }
    }
}