<?php
namespace fwk;

/**
* This class is a performance helper for HTML, for performance matters as long as possible js code should be sent to the bottom of the HTML
* This class allows to enqueue pieces of scripting in the templates to be rendered before the html closing tag
* @author Pablo Bossi
*/
class CssEnqueuer
{
  private static $instance = null;
  private $chunks = array();
  
  /**
  * The enqueuer is implemented as a singleton, this method allows the obtention of the instance storing the scripts
  * @returns JsEnqueuer Object
  */
  public static function getInstance() {
    if (self::$instance == null) {
      self::$instance = new CssEnqueuer();
    }
    return self::$instance;
  }
  
  /**
  * Creates a JSEnqueuer object
  * @returns JsEnqueuer object
  */
  private function __construct() {
    $this->chunks = array();
  }

  /**
  * Method to enqueue a css to be added on the Head of the document
  * @param String $Url of the css file to add to the html
  * @param Array $attributes list of extra attributes to be added to the script tag (IE: 'encoding' => 'utf8')
  */
  public function enqueue($cssUrl, $attributes = array())
  {
    $attrs = "";
    foreach ($attributes as $attrib => $value) {
      if (($attrib != "rel") && ($attrib != "src")) {
        $attrs .= $attrib .'="'.$value.'" ';
      }
    }
    $this->chunks[] = '<link href="'.$cssUrl.'" rel="stylesheet" type="text/css" '.$attrs.' />';
  }
  
  /**
  * Method which prints all the enqueued scripts and empties the list of scripts to avoid double printing of the scripts
  */
  public function render($content) {
    $output = '';
    foreach ($this->chunks as $chunk) {
      $output .= $chunk;
    }
    $this->clean();
    $content = str_replace('<!-- CSSENQUEUER //-->', $output, $content);
    return $content;
  }
  
  /**
  * Method which removes all the enqueued sripts
  */
  public function clean() {
    $done = false;
    while (! $done) {
      if (array_pop($this->chunks) === null) {
        $done = true;
      }
    }
    $this->chunks = array();
  }
}
