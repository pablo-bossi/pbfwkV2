<?php
namespace fwk;

/**
* This class is a performance helper for HTML, for performance matters as long as possible js code should be sent to the bottom of the HTML
* This class allows to enqueue pieces of scripting in the templates to be rendered before the html closing tag
* @author Pablo Bossi
*/
class JsEnqueuer
{
  const JS_FILE = 1;
  const JS_CODE = 2;

  private static $instance = null;
  private $chunks = array();
  
  /**
  * The enqueuer is implemented as a singleton, this method allows the obtention of the instance storing the scripts
  * @returns JsEnqueuer Object
  */
  public static function getInstance() {
    if (self::$instance == null) {
      self::$instance = new JsEnqueuer();
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
  * Method to enqueue a script to be shown before the end of the document
  * @param Int JsEnqueuer::JS_FILE or JsEnqueuer::JS_CODE indicating wether the script is a file inclusion or a code snippet
  * @param String $content url of the file or code snippet to be enqueued
  * @param Array $attributes list of extra attributes to be added to the script tag (IE: 'encoding' => 'utf8')
  */
  public function enqueue($type, $content, $attributes = array())
  {
    $scriptAttrs = "";
    foreach ($attributes as $attrib => $value) {
      if (($attrib != "type") && ($attrib != "src")) {
        $scriptAttrs .= $attrib .'="'.$value.'" ';
      }
    }
    if ($type == self::JS_FILE) {
        $this->chunks[] = '<script type="text/javascript" src="'.$content.'" '.$scriptAttrs.'></script>';
    } elseif ($type == self::JS_CODE) {
        $this->chunks[] = '<script type="text/javascript" '.$scriptAttrs.'>'.$content.'</script>';
    }
  }
  
  /**
  * Method which prints all the enqueued scripts and empties the list of scripts to avoid double printing of the scripts
  */
  public function flushAll() {
    foreach ($this->chunks as $chunk) {
      echo $chunk;
    }
    $this->clean();
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
  }
}
