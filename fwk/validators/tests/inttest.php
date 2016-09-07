<?php
namespace fwk\validators;

require_once(__DIR__.'/../base.php');
require_once(__DIR__.'/../int.php');
require_once(__DIR__.'/../../exceptions/invalidinput.php');
$_SERVER['DOCUMENT_ROOT']  = __DIR__.'/../../..';

class ValidatorIntTest extends \PHPUnit_Framework_TestCase
{
  /**
  * @dataProvider valuesProvider
  */
  public function test_validate($value, $isValid) {
    $validator = new Int();
    
    if ($isValid) {
      $this->assertTrue($validator->validate('Int', $value));
    } else {
      $this->setExpectedException('\fwk\exceptions\InvalidInput');
      $validator->validate('Int', $value);
    }
  }
  
  public function valuesProvider() {
    return array(
      array('100', true),
      array('foo', false),
      array('123aa', false),
      array('123.12', false),
    );
  }
}