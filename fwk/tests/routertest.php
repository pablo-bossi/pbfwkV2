<?php
namespace fwk;

$_SERVER['DOCUMENT_ROOT']  = __DIR__.'/../..';

require __DIR__.'/../router.php';

use fwk as fwk;

class RouterTest extends \PHPUnit_Framework_TestCase
{
  public function setUp() {
    $this->urlPatters = array();
    $this->urlPatterns[] = array('pattern' => '/^\/$/', 'method' => 'GET', 'handlerFile' => __DIR__.'/../../controllers/main.php', 'handlerClass' => 'controllers\main', 'handlerMethod' => 'index');
    $this->urlPatterns[] = array('pattern' => '/^\/micasa\/$/', 'method' => 'GET', 'handlerFile' => __DIR__.'/../../controllers/homes.php', 'handlerClass' => 'controllers\Homes', 'handlerMethod' => 'buy');
    $this->urlPatterns[] = array('pattern' => '/\/la\/micasa\/fea/', 'method' => 'GET', 'handlerFile' => __DIR__.'/../../controllers/homes.php', 'handlerClass' => 'controllers\Homes', 'handlerMethod' => 'belleza', 'extraParams' => array('type' => 'fea'));
    $this->urlPatterns[] = array('pattern' => '/\/pais\/([a-z]*)\/viajar/', 'method' => 'GET', 'handlerFile' => __DIR__.'/../../controllers/countries.php', 'handlerClass' => 'controllers\Countries', 'handlerMethod' => 'go', 'extraParams' => array('param1' => 'viajar', 'country' => '$1'));
    $this->urlPatterns[] = array('pattern' => '/^\/pais\/$/', 'method' => 'POST', 'handlerFile' => __DIR__.'/../../controllers/countries.php', 'handlerClass' => 'controllers\Countries', 'handlerMethod' => 'add');
    $this->urlPatterns[] = array('pattern' => '/^\/pais\/([0-9]+)$/', 'method' => 'PUT', 'handlerFile' => __DIR__.'/../../controllers/countries.php', 'handlerClass' => 'controllers\Countries', 'handlerMethod' => 'update', 'extraParams' => array('id' => '$1'));

    $this->staticContentPaths = array();
    $this->staticContentPaths[] = '/templates/';
    $this->staticContentPaths[] = '/js/';
    $this->staticContentPaths[] = '/css/';    

    $this->request = array('user' => 1);
  }

  /**
  * @dataProvider uriProvider
  */
  public function testconstructor($uri, $requestMethod, $file, $class, $method, $params = array()) {
    $router = new fwk\Router($uri, $requestMethod, $this->urlPatterns, $_SERVER["DOCUMENT_ROOT"].'/controllers', $this->staticContentPaths, $this->request);
    
    $this->assertEquals($file, $router->controllerFile);
    $this->assertEquals($class, $router->className);
    $this->assertEquals($method, $router->action);
    $this->assertEquals($params, $router->params);
  }

  public function uriProvider()
  {
    $dir = realpath(__DIR__.'/../../');
    return array(
      array('/', 'GET', __DIR__.'/../../controllers/main.php', 'controllers\main', 'index', array('user' => 1)),
      array('/micasa/', 'GET', __DIR__.'/../../controllers/homes.php', 'controllers\Homes', 'buy', array('user' => 1)),
      array('/la/micasa/fea', 'GET', __DIR__.'/../../controllers/homes.php', 'controllers\Homes', 'belleza', array('user' => 1, 'type' => 'fea')),
      array('/pais/argentina/viajar', 'GET', __DIR__.'/../../controllers/countries.php', 'controllers\Countries', 'go', array('user' => 1, 'param1' => 'viajar', 'country' => 'argentina')),
      array('/user/', 'GET', __DIR__.'/../../controllers/user.php', 'controllers\user', 'index', array('user' => 1)),
      array('/user/list', 'GET', __DIR__.'/../../controllers/user.php', 'controllers\user', 'list', array('user' => 1)),
      array('/user/customer/list', 'GET', __DIR__.'/../../controllers/user/customer.php', 'controllers\user\customer', 'list', array('user' => 1)),
      array('/templates/test.php', 'GET', $dir.'/fwk/staticfilesrenderer.php', '\fwk\StaticFilesRenderer', 'render', array('user' => 1, 'viewFile' => $_SERVER["DOCUMENT_ROOT"].'/templates/test.php')),
      array('/css/test.css', 'GET', $dir.'/fwk/staticfilesrenderer.php', '\fwk\StaticFilesRenderer', 'render', array('user' => 1, 'viewFile' => $_SERVER["DOCUMENT_ROOT"].'/css/test.css')),
      array('/js/jquery.js', 'GET', $dir.'/fwk/staticfilesrenderer.php', '\fwk\StaticFilesRenderer', 'render', array('user' => 1, 'viewFile' => $_SERVER["DOCUMENT_ROOT"].'/js/jquery.js')),
      array('/pais/', 'POST', __DIR__.'/../../controllers/countries.php', 'controllers\Countries', 'add', array('user' => 1)),
      array('/pais/213', 'PUT', __DIR__.'/../../controllers/countries.php', 'controllers\Countries', 'update', array('user' => 1, 'id' => 213)),
    );
  }
}