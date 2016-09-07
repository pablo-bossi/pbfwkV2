<?php
namespace fwk\validators;

require_once(__DIR__.'/../base.php');
require_once(__DIR__.'/../number.php');
require_once(__DIR__.'/../../exceptions/invalidinput.php');
$_SERVER['DOCUMENT_ROOT']  = __DIR__.'/../../..';

class ValidatorNumberTest extends \PHPUnit_Framework_TestCase
{
  /**
  * @dataProvider valuesProvider
  */
  public function test_validate($value, $isValid) {
    $validator = new Number();
    
    if ($isValid) {
      $this->assertTrue($validator->validate('Number', $value));
    } else {
      $this->setExpectedException('\fwk\exceptions\InvalidInput');
      $validator->validate('Number', $value);
    }
  }
  
  public function valuesProvider() {
    return array(
      array('100', true),
      array('123.12', true),
      array('foo', false),
      array('123foo', false),
    );
  }
}