<?php
namespace fwk;

$_SERVER['DOCUMENT_ROOT']  = __DIR__.'/../..';

require __DIR__.'/../i18n.php';

use fwk as fwk;

class I18NTest extends \PHPUnit_Framework_TestCase
{
    private $validLocales;
    private $defaultLocale;
    private $translationDomains;
    
    public function setUp() {
      $this->validLocales = array(
        "en" => __DIR__."/../../locales/",
        "es_AR" => __DIR__."/../../locales/",
      );
      
      $this->defaultLocale = 'en';
      $this->translationDomains = 'messages';
    }
    
    public function testSetLocale() {
      \fwk\I18N::setup($this->validLocales, $this->defaultLocale, $this->translationDomains);
      //Sets to the default locale
      \fwk\I18N::set();
    
      $msg = _('Hello World');
      $this->assertEquals('Hello World', $msg, 'Error validating default locale');
      
      //Set to an invalid locale, should default to english
      \fwk\I18N::set('pt');
      $msg = _('Hello World');
      $this->assertEquals('Hello World', $msg, 'Error validating after switching to unexisting locale');

      //Sets to specific default
      \fwk\I18N::set('es', 'AR');
      $msg = _('Hello World');
      $this->assertEquals('Hola Mundo', $msg, 'Error validating after switching to specific locale');
    }
    
    public function testExternalManager() {
      $manager = new Manager();
      \fwk\I18N::setup($this->validLocales, $this->defaultLocale, $this->translationDomains, $manager);
      \fwk\I18N::set();

      $msg = _('Hello World');
      $this->assertEquals('Hola Mundo', $msg, 'Error validating after switching to specific locale');
    }
}

class Manager {
  public function detect() {
    return 'es_AR';
  }
}