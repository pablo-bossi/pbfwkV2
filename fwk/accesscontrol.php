<?php
namespace fwk;

/**
* This class loads a config file and checks if the user has rights to execute a specific action
* @author Pablo Bossi
*/
class AccessControl
{
  protected $_accessChecker;

  /**
  * Cronstructor for the class
  * @param accessChecker $strategy object which allows to given a controller and an action, check for the access rights
  * @returns AccessControl Object
  */
  public function __construct($accessChecker) {
    $this->_accessChecker = $accessChecker;
  }

  /**
  * Method to check the access to an element provided the access control parameters
  * @param controlItem Value used to check the access, it can be a profile, a user Id, etc, whatever is used for control in your ACLs
  * @param controllerName Name of the controller in which the action is defined
  * @param action Action to be checked access to
  * @returns boolean indicating if the user is able to execute the action
  */
  public function accessCheck($controlItem, $controllerName, $action) {
    return $this->_accessChecker->check($controlItem, $controllerName, $action);
  }
}

?>