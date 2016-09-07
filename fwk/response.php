<?php

namespace fwk;

class Response {
   private $code = null;
   private $body;
   private $headers = array();
   private $reponseCookies = array();
   private $responseCodes = array(
      "200" => "OK",
      "301" => "Moved Permanently",
      "302" => "Found",
      "304" => "Not Modified",
      "400" => "Bad Request",
      "401" => "Unauthorized",
      "403" => "Forbidden",
      "404" => "Not Found",
      "500" => "Internal Server Error",
      "503" => "Service Unavailable",
   );

  /**
  * Method to set cookies on the response allowing to store user information
  * @param Mixed $cookie Array with parameters to set the cookie information on the response
  */
  public function setResponseCookie($name, $value, $expirationTime = 0, $path = '/', $domain = DEFAULT_COOKIES_DOMAIN, $secure = false, $httponly = false) {
    $this->reponseCookies[COOKIES_PREFIX.$name] = array(
      'value' => $value,
      'expires' => $expirationTime,
      'path' => $path,
      'domain' => $domain,
      'secure' => $secure,
      'httpOnly' => $httponly,
      );
  }

  /**
  * Sets the response code for the request. As the fwk is meant to work over Http it should be an http response code
  * @param String $code Http Response code
  */
   public function setResponseCode($code)
   {
      $this->code = strval($code);
   }
   
  /**
  * Sets the response body for the http message
  * @param String $body Body response
  */
   public function setBody($body) {
      $this->body = $body;
   }

  /**
  * Sets headers to be sent over htttp
  * @param String $name Header Name
  * @param String $value Value for the header
  */
   public function setHeader($name, $value) {
      $this->headers[] = $name.":".$value;
   }
   
  /**
  * Sends the response as an HTTP Message (Set requested headers and echoes the body)
  */
   public function render($bodyAppenders = array()) {
      if (empty($this->code)) {
        $this->code = "500";
        header($this->code." ".$this->responseCodes[$this->code]);
        return false;
      } else {
        $description = "";
        if (isset($this->responseCodes[$this->code])) {
          $description = $this->responseCodes[$this->code];
        }
        header('HTTP/1.0 '.$this->code." ".$description);
      }
      
      foreach ($this->headers as $header) {
        header($header);
      }
      foreach($this->reponseCookies as $key => $params) {
        setcookie($key, $params['value'], $params['expires'], $params['path'], $params['domain'], $params['secure'], $params['httpOnly']);
      }
      foreach ($bodyAppenders as $appender) {
        $this->body = $appender->render($this->body);
      }
      echo $this->body;
   }
}