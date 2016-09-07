<?php
namespace lib;

/**
* This class models the strategy used for detailed control access to the different parts of the site
* @author Pablo Bossi
*/
class AccessChecker extends \fwk\AccessControlStrategy
{
  public function accessCheck($controlItem, $controllerName, $action) {
    return true;
  }
}
