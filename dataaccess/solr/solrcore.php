<?php

namespace dataaccess\solr;

include_once($_SERVER["DOCUMENT_ROOT"]."/config/solr.php");

class SolrCore {

  protected $host;
  protected $port;
  protected $path;
  protected $request;
  
  public function __construct($host, $port, $corePath) {
    $this->host = $host;
    $this->port = $port;
    $this->path = $corePath;
    $this->request = new \HttpRequest();
  }
  
  public function query($solrQuery) {
    $url = $this->host.':'.$this->port.$this->path.'/select';
    
    $results = $this->_doRequest($url, $solrQuery);
    
    return new \dataaccess\solr\SolrResult($results);
  }
  
  protected function _doRequest($url, $query, $options = array()) {
      //If the url fits on a get, we use get, so is cacheable
      if (strlen($query) < 3000) {
        $this->request->setMethod(HTTP_METH_POST);
        $this->request->setRawPostData((string) $query);
      } else {
        $this->request->setMethod(HTTP_METH_GET);
        $this->request->setQueryData((string) $query);
      }

      if (! empty($options)) {
        $this->request->setOptions($options);
      }
      //$this->request->setHeaders($headers);
      $this->request->setUrl($url);
      
      try {
        //Send the request
        $response = $this->request->send();
        //echo $this->request->getRawRequestMessage();
        //On successfull response, return the content of the response, otherwise, null
        if ($this->request->getResponseCode() == 200) {
          return $this->request->getResponseBody();
        } else {
          return null;
        }
      } catch (HttpException $ex) {
        error_log("Error requesting Solr: ". $url. PHP_EOL . "Description: ".$ex->getMessage());
        return null;
      }
  }
}