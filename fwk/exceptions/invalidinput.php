<?php
namespace fwk\exceptions;

/**
* Specific exception for invalid user input
* @author Pablo Bossi
*/
class InvalidInput extends \Exception
{
  /**
  * Constructor for the class
  * params: Check PHP Exception documentation for explanation on the constructor params
  * @returns InvalidInput object
  */
  public function __construct($message, $code = 0, Exception $previous = null) {
    parent::__construct($message, $code, $previous);
  }
}

?>