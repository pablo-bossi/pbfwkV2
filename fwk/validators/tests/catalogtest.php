<?php
namespace fwk\validators;

require_once(__DIR__.'/../base.php');
require_once(__DIR__.'/../catalog.php');
require_once(__DIR__.'/../../exceptions/invalidinput.php');
$_SERVER['DOCUMENT_ROOT']  = __DIR__.'/../../..';

class ValidatorCatalogTest extends \PHPUnit_Framework_TestCase
{
  private $auxClass1;
  private $auxClass2;
  private $auxClass3;
  private $classArray;

  /**
  * @dataProvider valuesProvider
  */
  public function test_validate($options, $value, $isValid) {
    $validator = new Catalog($options);
    
    if ($isValid) {
      $this->assertTrue($validator->validate('Catalog', $value));
    } else {
      $this->setExpectedException('\fwk\exceptions\InvalidInput');
      $validator->validate('Catalog', $value);
    }
  }
  
  public function valuesProvider() {
    $this->providerSetup();
    return array(
      array(array(1, 2, 3, 4, 5), 1, true),
      array(array(1, 2, 3, 4, 5), 3, true),
      array(array('a', 'b', 'c', 'd', 'e'), 'a', true),
      array(array(1, 2, 3, 4, 5), 6, false),
      array(array(1, 2, 3, 4, 5), 0, false),
      array(array('a', 'b', 'c', 'd', 'e'), 1, false),
      array($this->classArray, $this->auxClass1, true),
      array($this->classArray, $this->auxClass2, true),
      array($this->classArray, $this->auxClass3, false),
    );
  }
  
  public function providerSetup() {
    $this->classArray = array();

    $this->auxClass1 = new \stdClass();
    $this->auxClass1->value = 1;
    $this->classArray[] = $this->auxClass1;

    $this->auxClass2 = new \stdClass();
    $this->auxClass2->value = 2;
    $this->classArray[] = $this->auxClass2;

    $this->auxClass3 = new \stdClass();
    $this->auxClass3->value = 3;
  }

  
}