<?php

namespace fwk\validators;

/**
* Validator class to check wether an input is not empty
* @author Pablo Bossi
*/
Class Required extends \fwk\validators\Base {

  /**
  * Method to execute the not empty validation
  * @param String field Name of the field to be validated
  * @param String value Value to be validated
  * @returns true on success
  *          Throws an \fwk\exceptions\InvalidInput exception in case the validation is not successfull
  */
  public function validate($field, $value) {
    if (($value === null) || ($value === '')) {
      throw new \fwk\exceptions\InvalidInput(sprintf(_('%s is required'), $field));
    }
    return true;
  }
}

?>