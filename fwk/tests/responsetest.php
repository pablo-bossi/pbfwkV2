<?php
namespace fwk;

$_SERVER['DOCUMENT_ROOT']  = __DIR__.'/../..';

require __DIR__.'/../response.php';

/**
* Overriding php function to make the test work in console
*/
function header($header) 
{
  ResponseTest::$headers[] = $header;
}

function headers_list() 
{
  return ResponseTest::$headers;
}

function header_remove()
{
  ResponseTest::$headers = array();
}

class ResponseTest extends \PHPUnit_Framework_TestCase
{
  public static $headers = array();

  /**
  * @dataProvider responseProvider
  */
  public function testRender($code, $body, $headers) 
  {
    $response = new Response();
    $response->setResponseCode($code);
    $response->setBody($body);
    foreach ($headers as $key => $value) {
      $response->setHeader($key, $value);
    }
    
    ob_start();
    $response->render();
    $headersList = headers_list();
    header_remove();
    $content = ob_get_contents();
    ob_end_clean();

    $statusCodeMsgs = $this->_getProperty('fwk\Response', 'responseCodes')->getValue($response);
    foreach ($headers as $key => $value) {
      $this->assertContains($key.":".$value, $headersList);
    }
    if (isset($statusCodeMsgs[$code])) {
      $this->assertContains("HTTP/1.0 ".$code." ".$statusCodeMsgs[$code], $headersList);
    }
    $this->assertEquals((count($headers) + 1), count($headersList));
    $this->assertEquals($body, $content);
  }
  
  private function _getProperty($className, $propertyName) 
  {
    $class = new \ReflectionClass($className);
    $property = $class->getProperty($propertyName);
    $property->setAccessible(true);
    return $property;
  }
  
  public function responseProvider() 
  {
    return array(
      array('200', 'Hello World', array('Content-Type' => 'text/html; charset=utf-8', 'Date' => gmdate('Y-m-d h:i:s'))),
      array('200', json_encode('Hello World'), array('Content-Type' => 'application/json; charset=utf-8', 'Date' => gmdate('Y-m-d h:i:s'))),
      array('500', '<p>Internal Server Error</p>', array('Content-Type' => 'text/html; charset=utf-8', 'Date' => gmdate('Y-m-d h:i:s'))),
      array('', '', array()),
    );
  }

}