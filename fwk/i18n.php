<?php
namespace fwk;

/**
* This class is used to internationalize applications.
* @author Pablo Bossi
*/
class I18N
{
  private static $activeLocale = null;
  private static $defaultLocale = null;
  private static $txDomain = null;
  private static $enabledLocales = null;
  private static $localePaths = null;
  private static $i18NConfigurator = null;
  
  /**
  * Method to setup the localization manager
  * @param Mixed activeLocales array with the list of supported locales with the folder were the details are stored
  * @param String defaultLocale defaultLocale in case no specific locale is set
  * @param String translationDomains gettext domain for translations
  * @param Object Object which handles custom locale detection, should implement a method named detect without parameters
  */
  public static function setUp($activeLocales, $defaultLocale, $translationDomains, $i18NConfigurator = null) {
    self::$enabledLocales = $activeLocales;
    self::$defaultLocale = $defaultLocale;
    self::$txDomain = $translationDomains;
    self::$i18NConfigurator = $i18NConfigurator;
  }

  /**
  * Method to switch th application to use a particular locale
  * @param String languageISO ISO-CODE for the language to set
  * @param String countryISO ISO-CODE for the particular country variation
  * @returns True on success
  */
  public static function set($languageISO = null, $countryISO = null) {
    //Get Language Tag to set the locale
    $languageTag = self::detectLocale($languageISO, $countryISO);
    //Check that selected locale is active
    if (! isset(self::$enabledLocales[$languageTag])) {
      error_log('Locale '.$languageTag.' is not configured for the app, check your system configuration. Defaulting to '.self::$defaultLocale);
      $usedLocale = self::$defaultLocale;
    } else {
      $usedLocale = $languageTag;
    }

    //If last asked locale was another one, I reset the locale
    if ($usedLocale != self::$activeLocale) {
      self::switchLocale($usedLocale);
      self::$activeLocale = $usedLocale;
    }
    return true;
  }

  /**
  * Private :: execute the commands to setup the locale
  * @param String languageTag string identifying the locale to be set
  */
  private static function switchLocale($languageTag) {
    //Initialize gettext
    putenv('LANG='.$languageTag.'.UTF8'); 
    putenv('LANGUAGE='.$languageTag.'.UTF8'); 
    bind_textdomain_codeset(self::$txDomain, 'UTF8'); 
    bindtextdomain(self::$txDomain, self::$enabledLocales[$languageTag]);
    setlocale(LC_ALL, $languageTag.'.UTF8'); 
    textdomain(self::$txDomain);
  }
  
  /**
  * Private:: based on the parameters set the proper locale string
  * @param String languageISO ISO-CODE for the language to set
  * @param String countryISO ISO-CODE for the particular country variation
  * @returns Proper localestring
  */
  private static function detectLocale($languageISO, $countryISO) {
    if (empty($languageISO)) {
      if (! empty(self::$i18NConfigurator)) {
        $languageTag = self::$i18NConfigurator->detect();
      } else {
        $languageTag = self::$defaultLocale;
      }
    } else {
      //Create the Locale string
      $languageTag = $languageISO;
      if (! empty($countryISO)) {
        $languageTag .= '_'.$countryISO;
      }
    }
    return $languageTag;
  }
}
