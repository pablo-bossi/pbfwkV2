<?php
/*this file defines mappings between Dataaccess methods and caching Config entries
The format is
array(
   ClassName (Including Namespace) => array (
         'methodName' => CachEntryName,
         ...
         ),
   ClassName ...
);
*/

global $daToCacheMappings;
$daToCacheMappings = array(
  'dataaccess\Sample' => array(
      'getById' => 'EXAMPLE_KEY_FAMILY',
   ),
);

?>
