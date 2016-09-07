<?php

namespace fwk;

/**
* This class is in charge of loading the classes as long as are requested.
* In order to work properly class names should follow the standar which is [Folder1]_[Folder2]_..._[FolderN]_filename
* @author Pablo Bossi
*/
class Autoloader {

  /**
  * Includes the file holding the class to make it available
  * @param String ClassName (Including namespace)
  */
  public static function Loader($className) {
    $classParts = self::_getClassParts($className);

    $path = self::_getFilePath($classParts->className, $classParts->classNameSpace);
    if (file_exists($path)) {
      //If file exists I include it, otherwise skip for other registered Autoloaders to deal with
      include($path);
    }
  }
  
  /**
  * Gets a class with two attributes: Namespace and class name
  * @param String ClassName (Including namespace)
  * @returns Std Object with attributes for namespace and class name
  */
  private static function _getClassParts($className) {
    $classNameStart = strrpos($className, '\\');
    $classParts = new \stdClass();
    if ($classNameStart !== false) {
      $classParts->classNameSpace = substr($className, 0, $classNameStart);
      $classParts->className = substr($className, ($classNameStart + 1));
    } else {
      $classParts->classNameSpace = '';
      $classParts->className = $className;
    }
    
    return $classParts;
  }
  
  /**
  * Gets the filepath for a specific class
  * @param String ClassName (without namespace)
  * @returns the path to the file were the class is stored
  */
  private static function _getFilePath($classname, $namespace) {

    $path = self::_checkSpecialCases($classname);
    
    if (empty($path)) {
      $directories = explode("\\", $namespace);
      $path = strtolower(implode("/", $directories));
      $path .= '/'.strtolower($classname).".php";
      $path = $_SERVER["DOCUMENT_ROOT"]."/".$path;
    }
    
    return $path;
  }
  
  /**
  * Gets the filepath for specific clases which does not follow the naming standard
  * @param String ClassName (without namespace)
  * @returns the path to the file were the class is stored
  */
  private static function _checkSpecialCases($className) {
    if (($className == "cache") || ($className == "cacheKeyParameters")) {
      return $_SERVER["DOCUMENT_ROOT"].'/dataaccess/cacheconn.php';
    }
    if ($className == "dbConnProvider") {
      return $_SERVER["DOCUMENT_ROOT"].'/dataaccess/dbconn.php';
    }
    if ($className == "HttpRequest") {
      return $_SERVER["DOCUMENT_ROOT"].'/lib/wrappers/httprequest.php';
    }
    return "";
  }
}
