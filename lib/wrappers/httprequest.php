<?php

class HttpRequest {
  protected $client;
  protected $request;
  protected $response = null;
  
  public function __construct() {
    $this->client = new \Http\Client();
    $this->request = new \Http\Client\Request();
  }
  
  public function setUrl($url) {
    $this->request->setRequestUrl($url);
  }
  
  public function setMethod($method) {
    switch ($method) {
      case HTTP_METH_GET:
        $httpMethod = 'GET';
        break;
      case HTTP_METH_POST:
        $httpMethod = 'POST';
        break;
      case HTTP_METH_PUT:
        $httpMethod = 'PUT';
        break;
      case HTTP_METH_DELETE:
        $httpMethod = 'DELETE';
        break;
    }
    
    $this->request->setRequestMethod($httpMethod);
  }
  
  public function setOptions($options) {
    $this->request->setOptions($options);
  }

  public function setHeaders($headers) {
    $this->request->addHeaders($headers);
  }
  
  public function addHeaders($headers) {
    $this->request->addHeaders($headers);
  }

  public function setContentType($contentType) {
    $this->request->setContentType($contentType);
  }
  
  public function addQueryData($params) {
    $this->request->addQuery($params);
  }

  public function setQueryData($params) {
    $this->request->setQuery($params);
  }
  
  public function setRawPostData($params) {
    $this->setBody($params);
  }
  
  public function setPostFields($params) {
    $body = new \Http\Message\Body();
    $body->append(new \Http\QueryString($params));
    $this->request->setBody($body);
  }

  public function setBody($body) {
    $stream = fopen('data://text/plain,'.$body,'r');  
    $body = new \Http\Message\Body($stream);
    $this->request->setBody($body);
  }
  
  public function addPutData($putData) {
    $stream = fopen('data://text/plain,'.$body,'r');  
    $body = new \Http\Message\Body($stream);
    $this->request->setBody($body);
  }

  public function setPutData($params) {
    $body = new \Http\Message\Body();
    $body->append(new \Http\QueryString($params));
    $this->request->setBody($body);
  }
  
  public function send() {
  //error_log($this->request);
  //die();
    $this->client->enqueue($this->request)->send();
    $this->response = $this->client->getResponse($this->request);
  }
  
  public function getResponseCode() {
    if ($this->response !== null) {
      return $this->response->getResponseCode();
    }
    return null;
  }
  
  public function getResponseBody() {
    if ($this->response !== null) {
      return (string) $this->response->getBody();
    }
    return null;
  }
}