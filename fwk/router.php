<?php
namespace fwk;

/**
* This class is in charge top map the requested uri into the handle controlller
* @author Pablo Bossi
*/
class Router { 
  private $controllerFile;
  private $className;
  private $action;
  private $params;
  private $language;
  private $country;

  /**
  * Parses the url to locate proper controller and method to handle the requested
  * The default url pattern looks like: [/folder1]...[/foldern]/controllerName/action?param1=name1&param2=name2
  * @param String $uri Requested URI
  * @param String $method Method used on the request (POST, PUT, GET)
  * @param String[] $customRules is an array with regular expresions to match against the uri in order to manage particular cases
  * @param String $controllersRoot base path were controller classes are stored
  * @param mixed[] asociative array with the request variables
  * @returns Router object
  */
  public function __construct($uri, $method, $customRules, $controllersRoot, $staticContentFolders, $params) {

    $done = $this->checkStaticContent($uri, $staticContentFolders, $params);
    if (! $done) {
      $done = $this->checkCustomRules($uri, $method, $customRules, $params);
    }
    if ($done) {
      return;
    }
    
    $urlParts = parse_url($uri);
    $url = $urlParts['path'];
  
    if (substr($url,0,1) == '/') {
      $url = substr($url, 1);
    }

    $path = explode('/', $url);

    $controllerPath = $controllersRoot;
    $iCounter = 0;
    $namespace = 'controllers\\';

    while ($iCounter < (count($path) - 2)) {
      $controllerPath .= '/'.$path[$iCounter];
      $namespace .= $path[$iCounter].'\\';
      $iCounter++;
    }
    $this->controllerFile = $controllerPath."/".$path[$iCounter].".php";
    $this->className = $namespace.$path[$iCounter];
    $this->action = $path[count($path) -1];
    if ($this->action == '') {
      $this->action = 'index';
    }
    $this->params = $params;
  }
  
  protected function checkStaticContent($uri, $staticContentFolders, $params) {
    $done = false;
    if (is_array($staticContentFolders) && ! empty($staticContentFolders)) {
      $counter = 0;
      while ($counter < count($staticContentFolders) && ! $done) {
        if (strpos($uri, $staticContentFolders[$counter]) !== false) {
          $done = true;
        }
        $counter++;
      }
      if ($done) {
        $this->controllerFile = __DIR__.'/staticfilesrenderer.php';
        $this->className = '\fwk\StaticFilesRenderer';
        $this->action = 'render';
        $params['viewFile'] = $_SERVER['DOCUMENT_ROOT'].$uri;
        $this->params = $params;
      }
    }
    return $done;
  }
  
  protected function checkCustomRules($uri, $method, $customRules, $params) {
    if (isset($_SERVER['QUERY_STRING'])) {
      $uri = urldecode(str_replace('?'.$_SERVER['QUERY_STRING'], '', $uri));
    }
    $done = false;
    if (is_array($customRules) && ! empty($customRules)) {
      $counter = 0;
      while (! $done && $counter < count($customRules)) {
        if (preg_match($customRules[$counter]['pattern'], $uri, $matches) > 0) {
          if (($customRules[$counter]['method'] == '*') || ($customRules[$counter]['method'] == $method)) {
            $this->controllerFile = $customRules[$counter]['handlerFile'];
            $this->className = $customRules[$counter]['handlerClass'];
            $this->action = $customRules[$counter]['handlerMethod'];
            $this->params = $params;
            if (isset($customRules[$counter]['extraParams']) && is_array($customRules[$counter]['extraParams']) && ! empty($customRules[$counter]['extraParams'])) { 
              foreach($customRules[$counter]['extraParams'] as $key => $value) {
                if (preg_match('/\$([0-9]*)/', $value, $submatch)) {
                  $this->params[$key] = $matches[$submatch[1]];
                } else {
                  $this->params[$key] = $value;
                }
              }
            }
            $done = true;
          }
        }
        $counter++;
      }
    }
    return $done;
  }
  
  /**
  * Generic getter to access properties for the class in the case particular rules are added for any attribute, this exceptions can be handled in here
  * @param String $key name of the attribute being queried
  * @returns mixed the value of the attribute or null if not exists
  */
  public function __get($key) {
    return $this->$key;
  }
}