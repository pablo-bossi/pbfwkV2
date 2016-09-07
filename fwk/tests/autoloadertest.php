<?php
namespace fwk;

require_once(__DIR__.'/../autoloader.php');
$_SERVER['DOCUMENT_ROOT']  = __DIR__.'/../..';

class AutoloaderTest extends \PHPUnit_Framework_TestCase
{
  /**
  * @dataProvider classesProvider
  */
  public function test_getFilePath($className, $namespace, $file) {
    $method = $this->_getMethod('fwk\Autoloader', '_getFilePath');
    $path = $method->invoke(null, $className, $namespace);
    $this->assertEquals($file, $path);
  }

  /**
  * @dataProvider namespacesProvider
  */
  public function test_getClassParts($classNameWithNamespace, $className, $namespace) {
    $method = $this->_getMethod('fwk\Autoloader', '_getClassParts');
    $class = $method->invoke(null, $classNameWithNamespace);
    $this->assertEquals($className, $class->className);
    $this->assertEquals($namespace, $class->classNameSpace);
  }

  private function _getMethod($className, $method) {
    $class = new \ReflectionClass($className);
    $method = $class->getMethod($method);
    $method->setAccessible(true);
    return $method;
  }
  
  public function classesProvider()
  {
    return array(
      array('Router', 'Fwk', $_SERVER["DOCUMENT_ROOT"].'/fwk/router.php'),
      /*array('View', $_SERVER["DOCUMENT_ROOT"].'/fwk/view.php'),
      array('Controller', $_SERVER["DOCUMENT_ROOT"].'/fwk/controller.php'),
      array('JsEnqueuer', $_SERVER["DOCUMENT_ROOT"].'/fwk/jsenqueuer.php'),
      array('Response', $_SERVER["DOCUMENT_ROOT"].'/fwk/response.php'),
      array('cache', $_SERVER["DOCUMENT_ROOT"].'/dataaccess/cacheconn.php'),
      array('dbConnProvider', $_SERVER["DOCUMENT_ROOT"].'/dataaccess/dbconn.php'),
      array('Controllers\User', $_SERVER["DOCUMENT_ROOT"].'/controllers/user.php'),
      array('Controllers\user\customer', $_SERVER["DOCUMENT_ROOT"].'/controllers/user/customer.php'),
      array('Lib\User', $_SERVER["DOCUMENT_ROOT"].'/lib/user.php'),
      array('Lib\User\Customer', $_SERVER["DOCUMENT_ROOT"].'/lib/user/customer.php'),
      array('Models\User', $_SERVER["DOCUMENT_ROOT"].'/models/user.php'),*/
    );
  }
  
  public function namespacesProvider()
  {
    return array(
      array('Fwk\Router', 'Router', 'Fwk'),
      /*array('Fwk\View', 'View', 'Fwk'),
      array('Fwk\Controller', 'Controller', 'Fwk'),
      array('Fwk\JsEnqueuer', 'JsEnqueuer', 'Fwk'),
      array('Fwk\Response', 'Response', 'Fwk'),
      array('dataaccess\cache', 'cache', 'dataaccess'),
      array('dataaccess\dbConnProvider', 'dbConnProvider', 'dataaccess'),
      array('controllers\User', 'User', 'controllers'),
      array('controllers\User\Customer', 'controllers\User'),
      array('Lib\User', 'User', 'Lib'),
      array('Lib\User\Customer', 'Customer', 'Lib'),
      array('Models\User', 'User', 'Models'),*/
    );
  }
  
}