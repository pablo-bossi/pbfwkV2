<?php
namespace fwk\validators;

require_once(__DIR__.'/../base.php');
require_once(__DIR__.'/../email.php');
require_once(__DIR__.'/../../exceptions/invalidinput.php');
$_SERVER['DOCUMENT_ROOT']  = __DIR__.'/../../..';

class ValidatorEmailTest extends \PHPUnit_Framework_TestCase
{
  /**
  * @dataProvider validEmailsProvider
  */
  public function test_validateValidEmails($email) {
    $validator = new Email();
    $this->assertTrue($validator->validate('email', $email));
  }

  /**
  * @dataProvider invalidEmailsProvider
  * @expectedException \fwk\exceptions\InvalidInput
  */
  public function test_validateInvalidEmails($email) {
    $validator = new Email();
    $validator->validate('email', $email);
  }
  
  public function validEmailsProvider()
  {
    return array(
      array('mail@mailcatch.com'),
      array('dummy@gmail.com'),
      array('dummy@yahoo.com.ar'),
    );
  }

  public function invalidEmailsProvider()
  {
    return array(
      array('!!<<>dummy@gmail.com'),
      array('dummyatgmail.com'),
      array('a@gmail'),
    );
  }
  
}