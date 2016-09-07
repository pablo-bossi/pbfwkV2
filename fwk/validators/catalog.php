<?php

namespace fwk\validators;

/**
* Validator class to check wether an input is a text with a lenght between a prefedined range
* @author Pablo Bossi
*/
class Catalog extends \fwk\validators\base {
  private $options;

  /**
  * Constructor for the class
  * @param mixed Array which contains the list of valid values to compare against the input
  * @returns Catalog validation object
  */
  public function __construct(Array $options) {
    $this->options = $options;
  }

  /**
  * Method to execute the validations
  * @param String field Name of the field to be validated
  * @param String value Value to be validated
  * @returns true on success
  *          Throws an \fwk\exceptions\InvalidInput exception in case the validation is not successfull
  */
  public function validate($field, $value) {
    if (! in_array($value, $this->options)) {
        throw new \fwk\exceptions\InvalidInput(sprintf(_('%s is not a valid value'), $field));
    }
    return true;
  }
}

?>