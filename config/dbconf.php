<?php
/**
* Use this file to configure DB connection parameters, the naming conventions for the constants is {Connection_Name}_Param
* For using a connection then when creating a db connection object just ask for the name of the requested connection
* @example SITE_WRITE_HOST is the hosts for a connection called SITE_WRITE
* @example Fwk\dbConnProvider::getConnection("SITE_WRITE") will return a SITE_WRITE connection
*/

DEFINE("SITE_WRITE_HOST", "localhost");
DEFINE("SITE_WRITE_USER", "root");
DEFINE("SITE_WRITE_PASS", "chiquita");
DEFINE("SITE_WRITE_DB", "gopr");

DEFINE("SITE_READ_HOST", "localhost");
DEFINE("SITE_READ_USER", "root");
DEFINE("SITE_READ_PASS", "chiquita");
DEFINE("SITE_READ_DB", "gopr");

DEFINE("SEARCH_SERVICE_WRITE_HOST", "localhost");
DEFINE("SEARCH_SERVICE_WRITE_USER", "root");
DEFINE("SEARCH_SERVICE_WRITE_PASS", "chiquita");
DEFINE("SEARCH_SERVICE_WRITE_DB", "GOPR_SEARCH");

DEFINE("SEARCH_SERVICE_READ_HOST", "localhost");
DEFINE("SEARCH_SERVICE_READ_USER", "root");
DEFINE("SEARCH_SERVICE_READ_PASS", "chiquita");
DEFINE("SEARCH_SERVICE_READ_DB", "GOPR_SEARCH");

?>
