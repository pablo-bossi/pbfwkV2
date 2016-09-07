<?php
/**
* Use this file to configure localization parameters
*/

/**
* By default the fwk uses gettext as I18N fwk, this variable is used to define the name of the po file were translations are stored
*/
$translationDomains = "messages";

/**
* Default local in case none is set by the user
*/
$defaultLocale = "en";

/**
* Name for a user class for custom locale detection.
*/
$localeManager = "\lib\LocaleManager";

/**
* List of supported locales with the folder were the locale details is stored
**/
$validLocales = array(
  "en" => __DIR__."/../locales",
  "es_AR" => __DIR__."/../locales",
);