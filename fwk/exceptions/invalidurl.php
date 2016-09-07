<?php
namespace fwk\exceptions;

/**
* Specific exception for 404 error
* @author Pablo Bossi
*/
class InvalidUrl extends \Exception
{
  /**
  * Constructor for the class
  * params: Check PHP Exception documentation for explanation on the constructor params
  * @returns InvalidInput object
  */
  public function __construct(\Exception $previous = null) {
    parent::__construct(_('The page requested does not exist'), 0, $previous);
  }
}

?>