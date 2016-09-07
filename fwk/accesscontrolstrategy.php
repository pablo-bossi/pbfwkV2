<?php
namespace fwk;

/**
* Abstract class defining mandatory methods for using the access control feature
* @author Pablo Bossi
*/
abstract class AccessControlStrategy
{
  /**
  * Method to check the access to an element provided the access control parameters
  * @param controlItem Value used to check the access, it can be a profile, a user Id, etc, whatever is used for control in your ACLs
  * @param controllerName Name of the controller in which the action is defined
  * @param action Action to be checked access to
  * @returns boolean indicating if the user is able to execute the action
  */
  abstract public function check($controlItem, $controllerName, $action);
}

?>