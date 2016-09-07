<?php
namespace lib;

/**
* This class is used to manage the urls for linking static files such as images, css or urls.
* This url manager has been included in order to:
* - Improve Http caching by ensuring the same resource will always have the same urls
* - Improve static files version management by allowing an indirection to add version numbers to the url of the files
* - Allow automatic host distribution for static files, allowing parallel browser download
* @author Pablo Bossi
*/
class UrlManager
{
  private static $serversConfig;
  private static $withMultiHost = array('css', 'image', 'js');
  private static $withVersioning = array('css', 'image', 'js');

  public static function setup($serversConfig) {
    self::$serversConfig = $serversConfig;
  }

  public static function getLink($resource, $resourceType, $host = null, $linkGenerator = null) {

    if (! empty($linkGenerator)) {
      return $linkGenerator->getLink($resource, self::$serversConfig[$resourceType]);
    }
    $serverMask = self::$serversConfig[$resourceType]['mask'];

    if (! empty($host)) {
      $server = $host;
    } else {
      if (in_array($resourceType, self::$withMultiHost)) {
        $serversCount = self::$serversConfig[$resourceType]['count'];
        $serverNum = ((crc32($resource)%4)+1);
        $server = sprintf($serverMask, $serverNum);
      } else {
        $server = $serverMask;
      }
    }
    
    if (in_array($resourceType, self::$withVersioning)) {
      $fileExtenstion = substr($resource, strrpos($resource, '.'));
      if (self::$serversConfig[$resourceType]['version'] != '') {
        $version = self::$serversConfig[$resourceType]['version'];
        $resource = str_replace($fileExtenstion, '-'.$version.$fileExtenstion, $resource);
      }
    }
    $uri = $server.'/'.$resource;
    return $uri;
  }
}
