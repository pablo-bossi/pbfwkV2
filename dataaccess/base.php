<?php
namespace dataaccess;
use fwk\dataaccess as Fwk;

require $_SERVER['DOCUMENT_ROOT']."/config/dacachemapping.php";

class Base
{
  /*important Note, when adding automatic cache to the DA's after that keep in mind Update/Insert/Delete methods should call dropCache*/
  public static function __callStatic($name, $arguments) {
    global $daToCacheMappings;

    $className = get_called_class();
    if (isset($daToCacheMappings[$className][$name])) {
      $keyFamily = $daToCacheMappings[$className][$name];
      $keyParams = new Fwk\cacheKeyParameters(false);
      foreach ($arguments as $arg) {
        $keyParams->addParam($arg);
      }
      
      $cacheContent = Fwk\cache::get($keyFamily, $keyParams);

      if ($cacheContent != null) {
        return $cacheContent;
      } else {
        $result = call_user_func_array(array($className, $name), $arguments);
        $cached = Fwk\cache::set($keyFamily, $keyParams, $result);
        return $result;
      }
    } else {
      $result = call_user_func_array(array($className, $name), $arguments);
      return $result;
    }
  }
  
  /*Important note, at the moment this only works if the arguments for caching are always the same
   which is a good approximation as we only cache queries by ID */
  protected static function dropCache($class, $arguments) {
    global $daToCacheMappings;
    
    if (isset($daToCacheMappings[$class])) {
      foreach ($daToCacheMappings[$class] as $method => $keyFamily) {
        $keyParams = new Fwk\cacheKeyParameters(false);
        foreach ($arguments as $arg) {
          $keyParams->addParam($arg);
        }
        Fwk\cache::del($keyFamily, $keyParams);
      }
    }
  }

  public static function startTransaction()
  {
      $dbConnection = Fwk\dbConnProvider::getConnection("SITE_WRITE");
      $value = $dbConnection->startTransaction();

      return $value;
  }

  public static function commitTransaction()
  {
      $dbConnection = Fwk\dbConnProvider::getConnection("SITE_WRITE");
      $value = $dbConnection->commit();
      
      return $value;
  }
  
  public static function rollbackTransaction()
  {
      $dbConnection = Fwk\dbConnProvider::getConnection("SITE_WRITE");
      $value = $dbConnection->rollback();
      
      return $value;
  }
  
}