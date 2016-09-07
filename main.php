<?php
/**
* This is the main page for the fwk, all dynamic requests goes through this page.
* The flow for the framework is:
*   1- Instantiatie Router class which checks the url pattern and returns the info to instantiate proper controller
*   2- Response object is created (The fwk works populating the Response object and always renders the response)
*   3- Instantiate the controller which receives the Response object as a parameters
*   4- Calls to initialize method for the controller (This method is used execute commands needed before any action)
*   5- Call the controller method which executes the requested action, this method always receives the request as a parameters
*   6- Call the finalize method for the controlle (This methos is used for executing commands after every action, for example closing all DB connections)
*   7- Render the response
* The flow is also in a try catch sentence so every exception can be trapped at this level
*/
session_start();

require "config/env.php";
require "constants/constants.php";
require "config/urlmanagerconf.php";
require "config/i18n.php";
require "fwk/autoloader.php";
require "fwk/i18n.php";
require "config/hostmanagerconf.php";

spl_autoload_register('fwk\Autoloader::Loader');

try {
  if (! empty($localeManager)) {
    $localizationManager = new $localeManager($defaultLocale);
  } else {
    $localizationManager = null;
  }

  \fwk\I18N::setup($validLocales, $defaultLocale, $translationDomains, $localizationManager);
  \fwk\I18N::set();
  \lib\UrlManager::setup($hostsConfig);

  $requestBody = json_decode(file_get_contents('php://input'));

  if ($requestBody !== null) {
    $vars = get_object_vars($requestBody);
    foreach ($vars as $varName => $varValue) {
      $_REQUEST[$varName] = $varValue;
    }
  }
  
  $router = new fwk\Router($_SERVER["REQUEST_URI"], $_SERVER['REQUEST_METHOD'], $urlPatterns, $_SERVER["DOCUMENT_ROOT"].'/controllers', $staticContentPaths, $_REQUEST);
  
  $file       = $router->controllerFile;
  $className  = $router->className;
  $method     = $router->action;
  $params     = $router->params;

  //Check Access
  if ((isset($_SESSION['isLoggedIn'])) && ($_SESSION['isLoggedIn'])) {
    $accessControl = new \fwk\AccessControl(new \lib\AccessChecker());
    $isAllowed = $accessControl->accessCheck($_SESSION['roles'], $className, $method);
  } else {
    $isAllowed = true;
  }
  
  $response = new fwk\Response();

  if ($isAllowed) {
    if (file_exists($file)) {
      include($file);
      //TODO config to read parameters required to construct or run each class (like dependency injection)
      $class = new $className($response);
    } else {
      $exception = new \fwk\exceptions\InvalidUrl();
      throw $exception;
    }
  } else {
    /* AJAX check  */
    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
      $params['output_format'] = 'json';
    } else {
      $params['output_format'] = 'html';
    }
    $class = new \controllers\main($response);
    $method = 'forbidden';
  }

  $result = $class->initialize();
  if ($result) {
    if (method_exists($class, $method)) {
      $class->$method($params);
    } else {
      $exception = new \fwk\exceptions\InvalidUrl();
      throw $exception;
    }
  }
  $class->finalize();
  
  echo $response->render(array(\fwk\CssEnqueuer::getInstance()));
  
} catch (\fwk\Exceptions\InvalidUrl $ex) {
  $response = new fwk\Response();
  $response->setResponseCode("404");
  $response->setHeader("Content-Type", "text/html; charset=utf-8");
  $response->setBody('Unable to find a matching API method');
  error_log($ex->getMessage().' at '.$ex->getFile().'('.$ex->getLine().')'.PHP_EOL.$ex->getTraceAsString());
  echo $response->render();
} catch (Exception $ex) {
  $response = new fwk\Response();
  $response->setResponseCode("200");
  $response->setHeader("Content-Type", "text/html; charset=utf-8");
  $response->setBody($ex->getMessage());
  error_log($ex->getMessage().' at '.$ex->getFile().'('.$ex->getLine().')'.PHP_EOL.$ex->getTraceAsString());
  echo $response->render();
}
