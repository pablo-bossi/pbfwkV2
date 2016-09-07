<?php
namespace fwk\validators;

require_once(__DIR__.'/../validate.php');
require_once(__DIR__.'/../../exceptions/invalidinput.php');

$_SERVER['DOCUMENT_ROOT']  = __DIR__.'/../../..';

class ValidatorValidateTest extends \PHPUnit_Framework_TestCase
{
  public function test_validate() {
    $validator = new Validate('dummy', 'dummyValue');
    
    $stub = $this->getMock('GenericValidator', array('validate'));
    $stub->expects($this->any())
             ->method('validate')
             ->with('dummy', 'dummyValue')
             ->will($this->returnValue(true));

    $validator->validate($stub);
    $this->assertTrue($validator->isValid());

    $errorMsg = 'dummy is invalid';
    $stub = $this->getMock('GenericValidator', array('validate'));
    $stub->expects($this->any())
             ->method('validate')
             ->with('dummy', 'dummyValue')
             ->will($this->throwException(new \fwk\exceptions\InvalidInput($errorMsg)));

    $validator->validate($stub);
    $this->assertTrue(!$validator->isValid(), 'Validate is still valid even after detecting an error');
    $this->assertEquals("- ".$errorMsg."\n", $validator->getErrors());

  }
}