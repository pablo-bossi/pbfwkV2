<?php
namespace fwk\validators;

require_once(__DIR__.'/../base.php');
require_once(__DIR__.'/../textlength.php');
require_once(__DIR__.'/../../exceptions/invalidinput.php');

$_SERVER['DOCUMENT_ROOT']  = __DIR__.'/../../..';

class ValidatorTextLengthTest extends \PHPUnit_Framework_TestCase
{
  /**
  * @dataProvider valuesProvider
  */
  public function test_validate($value, $params, $isValid) {
    $validator = new TextLength($params);
    
    if ($isValid) {
      $this->assertTrue($validator->validate('TextLength', $value));
    } else {
      $this->setExpectedException('\fwk\exceptions\InvalidInput');
      $validator->validate('TextLength', $value);
    }
  }
  
  public function valuesProvider() {
    return array(
      array('Valid on full range', array('min' => 5, 'max' => 200), true),
      array('Valid on top limit', array('max' => 200), true),
      array('Valid on lower limit', array('min' => 5), true),
      array('Invalid on lower limit', array('min' => 50), false),
      array('Invalid on max limit', array('max' => 8), false),
      array('Invalid on min below lower limit on full range', array('min' => 100, 'max' => 200), false),
      array('Invalid on max above limit on full range', array('min' => 5, 'max' => 10), false),
    );
  }
}