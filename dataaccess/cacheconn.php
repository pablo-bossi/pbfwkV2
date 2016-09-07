<?php
namespace fwk\dataaccess;

include_once($_SERVER["DOCUMENT_ROOT"]."/config/cacheconf.php");
//TODO: TEST CACHE GET MULTI
class cache
{
  private static function getCacheSources($keyFamily)
  { 
    global $cacheFamilies;
    return $cacheFamilies[$keyFamily]["source"];
  }
  
  private static function getFamilyVersion($keyFamily) {
    global $cacheFamilies;
    return $cacheFamilies[$keyFamily]["version"];
  }

  public static function get($keyFamily, $Parameters)
  {
    $keyFamily = strtoupper($keyFamily);
    $cacheSource = self::getCacheSources($keyFamily);
    $keyParams = $Parameters->build(self::getFamilyVersion($keyFamily));

    $value = null;
    $useGlobals = false;
    $useAPC = false;

    if (($cacheSource & GLOBALS_SOURCE) == GLOBALS_SOURCE)
    {
      $useGlobals = true;
      $value = globalsCache::get($keyFamily, $keyParams);
    }
    if (($value == null) && (($cacheSource & APC_SOURCE) == APC_SOURCE))
    {
      $useAPC = true;
      $value = APCCache::get($keyFamily, $keyParams);
      if ($useGlobals && $value != null)
        globalsCache::set($keyFamily, $keyParams, $value);
    }

    if (($value == null) && (($cacheSource & MEMCACHE_SOURCE) == MEMCACHE_SOURCE))
    {
      $value = memcacheCache::get($keyFamily, $keyParams);
      if ($useGlobals && $value != null)
        globalsCache::set($keyFamily, $keyParams, $value);
      if ($useAPC && $value != null)
        APCCache::set($keyFamily, $keyParams, $value);
    }
    return $value;
  }

  public static function getMulti($keysFamilies, $Parameters)
  {
    if (! is_array($keysFamilies) || ! is_array($Parameters))
    {
      throw new Exception("Invalid cache call");
      return null;
    }
      
    if (count($keysFamilies) != count($Parameters))
    {
      throw new Exception("Invalid cache call");
      return null;
    }

    $values = array();
    $toMemcache = array();

    $counter = 0;
    foreach($keysFamilies as $keyFamily)
    {
      $keyFamily = strtoupper($keyFamily);
      $cacheSource = self::getCacheSources($keyFamily);
      $keyParams = $Parameters[$counter]->build(self::getFamilyVersion($keyFamily));
      $useGlobals = false;
      $useAPC = false;
      $value = null;

      if (($cacheSource & GLOBALS_SOURCE) == GLOBALS_SOURCE)
      {
        $useGlobals = true;
        $value = globalsCache::get($keyFamily, $keyParams);
      }
      if (($value == null) && (($cacheSource & APC_SOURCE) == APC_SOURCE))
      {
        $useAPC = true;
        $value = APCCache::get($keyFamily, $keyParams);
        if ($useGlobals && $value != null)
          globalsCache::set($keyFamily, $keyParams, $value);
      }

      if (($value == null) && (($cacheSource & MEMCACHE_SOURCE) == MEMCACHE_SOURCE))
      {
        $toMemcache["keyFamilies"][] = $keyFamily;
        $toMemcache["keyParameters"][] = $keyParams;
        $toMemcache["useGlobals"][] = $useGlobals;
        $toMemcache["useAPC"][] = $useAPC;
        $toMemcache["Order"][] = $counter;
        $values[] = null;
      } 
      elseif ($value != null) 
      {
        $values[] = $value;
      }
      $counter++;
    }

    if (! empty($toMemcache))
    {
      //Get from memcache all values i couldn't get from APC or Globals
      $memcacheValues = memcacheCache::getMulti($toMemcache["keyFamilies"], $toMemcache["keyParameters"]);
      for ($counter = 0; $counter < count($toMemcache["keyFamilies"]); $counter++)
      {
        $values[$toMemcache["Order"][$counter]] = $memcacheValues[$counter];
        if ($toMemcache["useAPC"][$counter])
          APCCache::set($toMemcache["keyFamilies"][$counter], $toMemcache["keyParameters"][$counter], $memcacheValues[$counter]);
        if ($toMemcache["useGlobals"][$counter])
          globalsCache::set($toMemcache["keyFamilies"][$counter], $toMemcache["keyParameters"][$counter], $memcacheValues[$counter]);
      }
    }

    return $values;
  }

  public static function set($keyFamily, $Parameters, $value)
  {
    $keyFamily = strtoupper($keyFamily);
    $cacheSource = self::getCacheSources($keyFamily);
    $keyParams = $Parameters->build(self::getFamilyVersion($keyFamily));
    if (($cacheSource & GLOBALS_SOURCE) == GLOBALS_SOURCE)
        globalsCache::set($keyFamily, $keyParams, $value);
    if (($cacheSource & APC_SOURCE) == APC_SOURCE)
        APCCache::set($keyFamily, $keyParams, $value);
    if (($cacheSource & MEMCACHE_SOURCE) == MEMCACHE_SOURCE)
    {
        memcacheCache::set($keyFamily, $keyParams, $value);
    }

    return;
  }

  public static function del($keyFamily, $Parameters)
  {
    $keyFamily = strtoupper($keyFamily);
    $cacheSource = self::getCacheSources($keyFamily);
    $keyParams = $Parameters->build(self::getFamilyVersion($keyFamily));

    if (($cacheSource & GLOBALS_SOURCE) == GLOBALS_SOURCE)
        globalsCache::del($keyFamily, $keyParams);
    if (($cacheSource & APC_SOURCE) == APC_SOURCE)
        APCCache::del($keyFamily, $keyParams);
    if (($cacheSource & MEMCACHE_SOURCE) == MEMCACHE_SOURCE)
        memcacheCache::del($keyFamily, $keyParams);

    return;
  }
}

class APCCache
{
  private static function getKeyOptions($keyFamily)
  {
    global $cacheFamilies;
    $keyOptions = $cacheFamilies[$keyFamily];
    return $keyOptions["ttl"][APC_SOURCE];
  }

  public static function set($keyFamily, $keyParameters, $object)
  {
    $cacheTime = self::getKeyOptions($keyFamily);
    return apcu_add($keyFamily."_".$keyParameters, $object, $cacheTime);
  }

  public static function get($keyFamily, $keyParameters)
  {
    $object = apcu_fetch($keyFamily."_".$keyParameters, $result);
    if ($result)
      return $object;
    else
      return null;
  }

  public static function del($keyFamily, $keyParameters)
  {
    return apcu_delete($keyFamily."_".$keyParameters);
  }
}

class memcacheCache
{
  private static $cacheConnections = array();

  private static function getConnection($farmName)
  {
    global $memcacheFarms;
    if (! isset(self::$cacheConnections[$farmName]))
    {
      $memcacheConnection = new \Memcache;
      $farmServersConf = $memcacheFarms[$farmName];
      foreach ($farmServersConf as $host => $port)
      {
        $memcacheConnection->addServer($host, $port);
      }
      self::$cacheConnections[$farmName] = $memcacheConnection;
    }
    return self::$cacheConnections[$farmName];
  }

  private static function getKeyOptions($keyFamily)
  {
    global $cacheFamilies;
    $keyOptions = $cacheFamilies[$keyFamily];
    return array($keyOptions["farm"], $keyOptions["flags"][MEMCACHE_SOURCE], $keyOptions["ttl"][MEMCACHE_SOURCE]);
  }

  public static function get($keyFamily, $keyParameters)
  {
    list($farmName, $flags, $ttl) = self::getKeyOptions($keyFamily);
    $memcacheConnection = self::getConnection($farmName);
    return $memcacheConnection->get($keyFamily."_".$keyParameters);
  }

  public static function getMulti($keysFamilies, $keysParameters)
  {
    $cacheRequests = array();
    $results = array();
    //Divido los keys segun el farm al cual pertenecen
    for ($counter = 0; $counter < count($keysFamilies); $counter++)
    {
      list($farmName, $flags, $ttl) = self::getKeyOptions($keysFamilies[$counter]);
      $cacheRequests[$farmName]["Keys"][] = $keysFamilies[$counter]."_".$keysParameters[$counter];
      $cacheRequests[$farmName]["Order"][] = $counter;
      //Initialize all spaces of the resulting array
      $results[] = null;
    }
    //Ejecuto un get por casa farm
    foreach($cacheRequests as $farm => $keys)
    {
      $memcacheConnection = self::getConnection($farm);
      $cacheResults = $memcacheConnection->get($keys["Keys"]);
      $counter = 0;
      //Put the keys on the results array in the order they were asked
      foreach($keys["Order"] as $index)
      {
        if (isset($cacheResults[$keys["Keys"][$counter]])) {
          $results[$index] = $cacheResults[$keys["Keys"][$counter]];
        } else {
          $results[$index] = false;
        }
        $counter++;
      }
    }
    //TODO Reorder results when we query more than one farm
    return $results;
  }

  public static function set($keyFamily, $keyParameters, $object)
  {
    list($farmName, $flags, $ttl) = self::getKeyOptions($keyFamily);
    $memcacheConnection = self::getConnection($farmName);
    return $memcacheConnection->set($keyFamily."_".$keyParameters, $object, $flags, $ttl);
  }

  public static function del($keyFamily, $keyParameters)
  {
    list($farmName, $flags, $ttl) = self::getKeyOptions($keyFamily);
    $memcacheConnection = self::getConnection($farmName);
    return $memcacheConnection->delete($keyFamily."_".$keyParameters);
  }
}

class globalsCache
{
  private static $globals;

  public static function get($keyFamily, $keyParameters)
  {
    if (! isset(self::$globals[$keyFamily."_".$keyParameters]))
      return null;
    else
      return self::$globals[$keyFamily."_".$keyParameters];
  }

  public static function set($keyFamily, $keyParameters, $object)
  {
    self::$globals[$keyFamily."_".$keyParameters] = $object;
    return true;
  }

  public static function del($keyFamily, $keyParameters)
  {
    if (isset(self::$globals[$keyFamily."_".$keyParameters]))
    {
      unset(self::$globals[$keyFamily."_".$keyParameters]);
    }
    return true;
  }
}

class cacheKeyParameters
{
  protected $withNamedParams;
  protected $properties = array();

  public function __construct($withNamedParams = true) {
    $this->withNamedParams = $withNamedParams;
  }
  
  public function __set($paramName, $value)
  {
    if ($this->withNamedParams) {
      $this->properties[$paramName] = $value;
      return true;
    }
    return false;
  }

  public function addParam($value) {
    if (! $this->withNamedParams) {
      $this->properties[] = $value;
    }
  }
  
  public function build($version)
  {
    $keyParameters = '';
    foreach($this->properties as $key => $value)
    {
      if ($this->withNamedParams) {
        $keyParameters .= '_'.$key.'-'.$value;
      } else {
        $keyParameters .= '_'.$value;
      }
    }
    return $version.'--'.$keyParameters;
  }
}

?>