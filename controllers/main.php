<?php
namespace controllers;

use fwk as Fwk;
use models as Models;

class main extends Base {

  public function index($params) {
    $this->response->setResponseCode("200");
    $this->response->setHeader("Content-Type", "text/html; charset=utf-8");
    $this->response->setBody('<html><body><p>Hello World!!</p></body></html>');
  }

  public function hello($params) {

    $view = new Fwk\View('helloworld/index');
    $view->setMasterView('public');
    
    $view->name = $params['name'];
    
    $this->response->setResponseCode("200");
    $this->response->setHeader("Content-Type", "text/html; charset=utf-8");
    $this->response->setBody($view->render());
  }
}