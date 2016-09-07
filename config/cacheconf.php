<?php
/**
* Use this file to configure your cache server farms and setup caching families which will used a version number to be cleaned up altogether
*/

define("GLOBALS_SOURCE", 1);
define("APC_SOURCE", 2);
define("GLOBALS_APC", 3);
define("MEMCACHE_SOURCE", 4);
define("GLOBALS_MEMCACHE", 5);
define("APC_MEMCACHE", 6);
define("GLOBALS_APC_MEMCACHE", 7);


define("MEMCACHE_FARM_GENERAL", "general");
/*Configuration of memcache farms
Format:
$memcacheFarms = array(
  "FarmName1" => array("memcacheserver1name" => "memcacheserver1port", "memcacheserver2name" => "memcacheserver2port"),
  "FarmName2" => array("memcacheserver1name" => "memcacheserver1port", "memcacheserver2name" => "memcacheserver2port")
)
*/
global $memcacheFarms;
$memcacheFarms = array(
  "general" => array("127.0.0.1" => "11211")
);

global $cacheFamilies;
$cacheFamilies = array(
  "EXAMPLE_KEY_FAMILY"  => array (
                    "source" => GLOBALS_APC_MEMCACHE,
                    "ttl" => array(MEMCACHE_SOURCE => 0, APC_SOURCE => 0),
                    "farm" => MEMCACHE_FARM_GENERAL,
                    "flags" => array(MEMCACHE_SOURCE => MEMCACHE_COMPRESSED),
                    "version" => 1,
                ),
);

?>
