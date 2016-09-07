<?php

namespace fwk\validators;

/**
* Validator class to check wether an input is a text with a lenght between a prefedined range
* @author Pablo Bossi
*/
class TextLength extends \fwk\validators\Base {
  private $min;
  private $max;

  /**
  * Constructor for the class
  * @param mixed Array which can contains min, max or both values to define the range, if one of the options is not present, then that part won't be validated
  * @returns TextLength object
  */
  public function __construct($params) {
    $this->min = (isset($params['min'])?$params['min']:null);
    $this->max = (isset($params['max'])?$params['max']:null);
  }

  /**
  * Method to execute the text length validation
  * @param String field Name of the field to be validated
  * @param String value Value to be validated
  * @returns true on success
  *          Throws an \fwk\exceptions\InvalidInput exception in case the validation is not successfull
  */
  public function validate($field, $value) {
    //If empty no validation happens (Required validator is for this purpose)
    if ((! empty($value)) && ($value != '')) {
      //If is a valid numeric number check that is inside proper ranges
      $error = '';
      if ((! empty($this->min)) && ($this->min > mb_strlen($value, 'UTF-8'))) {
        $error = sprintf(_('%s should be longer than %s'), $field, $this->min).PHP_EOL;
      }
      if ((! empty($this->max)) && ($this->max < mb_strlen($value, 'UTF-8'))) {
        $error = sprintf(_('%s should be shorter than %s'), $field, $this->max).PHP_EOL;
      }
      if ($error != '') {
        throw new \fwk\exceptions\InvalidInput($error);
      }
    }
    return true;
  }
}

?>