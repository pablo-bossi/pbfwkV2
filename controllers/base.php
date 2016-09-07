<?php
namespace controllers;

use fwk as Fwk;
use models as Models;

class Base extends Fwk\Controller {

  public function initialize() {
    return true;
  }
  
  public function finalize() {
  }
}
