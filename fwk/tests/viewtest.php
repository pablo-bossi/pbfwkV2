<?php
namespace fwk;

$_SERVER['DOCUMENT_ROOT']  = __DIR__.'/../..';

require __DIR__.'/../view.php';

use fwk as fwk;

class ViewTest extends \PHPUnit_Framework_TestCase
{

  public function testSimpleRender() {
    fwk\View::setGlobal('site', 'Test Site');
    $view = new fwk\View('test/test1', array('fwkName' => 'PB Generic PHP Fwk'));
    $view->message = 'Hello World!';
    
    $content = $view->render();
    
    $expected = '<html><head><title>Test Site</title></head><body><h1>Hello World!</h1><p>Page generatd with PB Generic PHP Fwk</p></body></html>';
    
    $this->assertEquals($expected, $content, 'Resulting view does not match expected output');
    
    //Just for coverage, getting a property not set
    $this->assertEquals(null, $view->notSetted);
  }

  public function testSubmoduleRender() {
    fwk\View::setGlobal('site', 'Test Site');
    $view = new fwk\View('test/test1', array('fwkName' => 'PB Generic PHP Fwk'));
    $view->message = 'Hello World!';

    $content = $view->renderSubModule('test/testmodule/test2', array('extra' => 'Extra'));
    
    $expected = '<p>Site: Test Site</p><p>Message: Hello World!</p><p>Fwk: PB Generic PHP Fwk</p><p>Extra</p>';

    $this->assertEquals($expected, $content, 'Resulting submodule view does not match expected output');
  }

  public function testLayoutRender() {
    fwk\View::setGlobal('site', 'Test Site');
    $view = new fwk\View('test/testmodule/test2');
    $view->setMasterView('testmaster');
    $view->message = 'Hello World!';
    $view->extra = 'Extra';
    $view->fwkName = 'PB Generic PHP Fwk';

    $content = $view->render();
    
    $expected = '<html><head><title>Test Site</title></head><body><p>Site: Test Site</p><p>Message: Hello World!</p><p>Fwk: PB Generic PHP Fwk</p><p>Extra</p></body></html>';

    $this->assertEquals($expected, $content, 'Resulting view with layout does not match expected output');
  }
  
}