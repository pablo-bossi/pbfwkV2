<?php

namespace fwk;

$_SERVER['DOCUMENT_ROOT']  = __DIR__.'/../..';

require __DIR__.'/../jsenqueuer.php';

use fwk as fwk;

class JsEnqueuerTest extends \PHPUnit_Framework_TestCase
{

  public function testPrivateConstructor() {
    $class = new \ReflectionClass('fwk\JsEnqueuer');
    $method = $class->getMethod('__construct');
    $isPrivate    = $method->isConstructor();

    $this->assertEquals(true, $isPrivate, 'JSEnqueuer constructors is not private');
  }

  public function testGetInstance() {
    $enqueuer = fwk\JsEnqueuer::getInstance();
    $className = get_class($enqueuer);
    
    $this->assertEquals('fwk\JsEnqueuer', $className, 'Invalid class created');

    $enqueuer2 = fwk\JsEnqueuer::getInstance();

    $this->assertEquals($enqueuer, $enqueuer2, 'Get instance returning different objects instead of the same class');
  }

  public function testEnqueue() {
  
    $scripts = array(
      array(1, '/js/jquery.js', array('encoding' => 'utf-8', 'custom' => 'customAttrib'), '<script type="text/javascript" src="/js/jquery.js" encoding="utf-8" custom="customAttrib" ></script>'),
      array(1, '/js/myjs.js', array(), '<script type="text/javascript" src="/js/myjs.js" ></script>'),
      array(2, 'var a = 10;', array('encoding' => 'utf-8', 'custom' => 'customAttrib'), '<script type="text/javascript" encoding="utf-8" custom="customAttrib" >var a = 10;</script>'),
      array(2, 'var b = 5;', array(), '<script type="text/javascript" >var b = 5;</script>'),
    );
  
    $enqueuer = fwk\JsEnqueuer::getInstance();
    $counter = 0;
    $expectedFlush = '';
    
    foreach ($scripts as $script) {
      $enqueuer->enqueue($script[0], $script[1], $script[2]);
      $enqueuedScripts = $this->_getProperty('fwk\JsEnqueuer', 'chunks')->getValue($enqueuer);

      $expectedFlush .= $script[3];
      $this->assertEquals($script[3], $enqueuedScripts[$counter]);
      $counter++;
    }
    
    ob_start();
    $enqueuer->flushAll();
    $content = ob_get_contents();
    ob_end_clean();
    
    $this->assertEquals($expectedFlush, $content);

    $enqueuedScripts = $this->_getProperty('fwk\JsEnqueuer', 'chunks')->getValue($enqueuer);
    $this->assertEquals(array(), $enqueuedScripts);

  }
  
  private function _getProperty($className, $propertyName) 
  {
    $class = new \ReflectionClass($className);
    $property = $class->getProperty($propertyName);
    $property->setAccessible(true);
    return $property;
  }
  
}