<?php
namespace fwk\validators;

require_once(__DIR__.'/../base.php');
require_once(__DIR__.'/../number.php');
require_once(__DIR__.'/../numberrange.php');
require_once(__DIR__.'/../../exceptions/invalidinput.php');
$_SERVER['DOCUMENT_ROOT']  = __DIR__.'/../../..';

class ValidatorNumberRangeTest extends \PHPUnit_Framework_TestCase
{
  /**
  * @dataProvider valuesProvider
  */
  public function test_validate($value, $params, $isValid) {
    $validator = new NumberRange($params);
    
    if ($isValid) {
      $this->assertTrue($validator->validate('NumberRange', $value));
    } else {
      $this->setExpectedException('\fwk\exceptions\InvalidInput');
      $validator->validate('NumberRange', $value);
    }
  }
  
  public function valuesProvider() {
    return array(
      array('100', array('min' => 5, 'max' => 200), true),
      array('1', array('max' => 200), true),
      array('50', array('min' => 10), true),
      array('10', array('min' => 20), false),
      array('10', array('max' => 8), false),
      array('10', array('min' => 15, 'max' => 30), false),
      array('50', array('min' => 15, 'max' => 30), false),
    );
  }
}