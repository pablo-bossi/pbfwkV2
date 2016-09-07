<?php

namespace lib;

/**
* This class is just an example on how to write a custom language manager
* @author Pablo Bossi
*/
class LocaleManager {
  private $defaultLocale;

  public function __construct($defaultLocale) {
    $this->defaultLocale = $defaultLocale;
  }
  
  public function detect() {
    if (isset($_REQUEST['language'])) {
      if (isset($_REQUEST['country'])) {
        $tag = $_REQUEST['language']."_".$_REQUEST['country'];
      } else {
        $tag = $_REQUEST['language'];
      }
      return $tag;
    } else {
      return $this->defaultLocale;
    }
  }
}