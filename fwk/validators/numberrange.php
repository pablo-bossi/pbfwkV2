<?php

namespace fwk\validators;

/**
* Validator class to check wether an input is a number within a defined range
* @author Pablo Bossi
*/
class NumberRange extends \fwk\validators\Base {
  private $min;
  private $max;

  /**
  * Constructor for the class
  * @param mixed Array which can contains min, max or both values to define the range, if one of the options is not present, then that part won't be validated
  * @returns NumberRange object
  */
  public function __construct($params) {
    $this->min = (isset($params['min'])?$params['min']:null);
    $this->max = (isset($params['max'])?$params['max']:null);
  }

  /**
  * Method to execute the number range validation
  * @param String field Name of the field to be validated
  * @param String value Value to be validated
  * @returns true on success
  *          Throws an \fwk\exceptions\InvalidInput exception in case the validation is not successfull
  */
  public function validate($field, $value) {
    //If empty no validation happens (Required validator is for this purpose)
    if (($value != null) && ($value != '')) {
      //Check value is numeric
      $numberValidation = new \fwk\validators\Number();
      if ($numberValidation->validate($field, $value)) {
        //If is a valid numeric number check that is inside proper ranges
        $error = '';
        if ((! empty($this->min)) && ($this->min > $value)) {
          $error = sprintf(_('%s should be higher than %s'), $field, $this->min).PHP_EOL;
        }
        if ((! empty($this->max)) && ($this->max < $value)) {
          $error = sprintf(_('%s should be lower than %s'), $field, $this->max).PHP_EOL;
        }
      }
      if ($error != '') {
        throw new \fwk\exceptions\InvalidInput($error);
      }
    }
    return true;
  }
}

?>