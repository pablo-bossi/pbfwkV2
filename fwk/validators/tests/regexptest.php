<?php
namespace fwk\validators;

require_once(__DIR__.'/../base.php');
require_once(__DIR__.'/../regexp.php');
require_once(__DIR__.'/../../exceptions/invalidinput.php');

$_SERVER['DOCUMENT_ROOT']  = __DIR__.'/../../..';

class ValidatorRegexpTest extends \PHPUnit_Framework_TestCase
{
  /**
  * @dataProvider valuesProvider
  */
  public function test_validate($value, $pattern, $type, $isValid) {
    $validator = new Regexp($type, $pattern);
    
    if ($isValid) {
      $this->assertTrue($validator->validate('Regexp', $value));
    } else {
      $this->setExpectedException('\fwk\exceptions\InvalidInput');
      $validator->validate('Regexp', $value);
    }
  }
  
  public function valuesProvider() {
    return array(
      array('foo123bar', '/[0-9]+/', 0, true),
      array('foo123bar', '/[A-Za-z]+/', 0, true),
      array('123foo456', '/^[0-9]+[a-z]*[0-9]+$/', 0, true),
      array('foobar', '/[0-9]+/', 1, true),
      array('123456789', '/[A-Za-z]+/', 1, true),
      array('foo123bar', '/[0-9]+/', 1, false),
      array('foo123bar', '/[A-Za-z]+/', 1, false),
      array('123foo456', '/^[0-9]+[a-z]*[0-9]+$/', 1, false),
      array('foobar', '/[0-9]+/', 0, false),
      array('123456789', '/[A-Za-z]+/', 0, false),
    );
  }
}