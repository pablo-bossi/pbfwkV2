<?php
namespace fwk;

/**
* This class is in charge to render templates and manage templating hierarchies
* @author Pablo Bossi
*/
class View
{
  private $viewName = null;
  private $variables = null;
  private $masterPage = null;
  private static $generalVariables = array();

  /**
  * Creates a view object which will render the view with the specified name
  * @param String $viewname Name of the template to render (The template name always matches the file name were the template is stored)
  * @param mixed $variables shortcut to set variables to be used to render the template, way to use it is sending an array specifying attrName => attrValue
  * @returns View object
  */
  public function __construct($viewName, $variables = null)
  {
    $this->variables = $variables;
    $this->viewName = $viewName;
  }

  /**
  * In case the template should be render inside of a master page, then use this method to set the layout template
  * @param String $masterTemplate name of the master template located in /template/masters, the name should match the file name were the template is stored
  */
  public function setMasterView($masterTemplate) {
    $this->masterPage = $masterTemplate;
  }

  /**
  * The method renders the template, important to note that this doesn't flush the output, but returns a string with the result of rendering the template
  * @returns String representing the result of rendering the template
  */
  public function render()
  {
     //Move globals to the class
     foreach (self::$generalVariables as $key => $value) {
        $this->$key = $value;
     }

     ob_start();

     if ($this->masterPage != null) {
        $this->childModule = strtolower($this->viewName);
        include(__DIR__.'/../templates/masters/'.strtolower($this->masterPage).'.php');
        unset($this->childModule);
     } else {
        //Include view file
        include(__DIR__.'/../templates/'.strtolower($this->viewName).'.php');
     }
     $content = ob_get_contents(); 
     ob_end_clean();

     return $content;
  }

  /**
  * This method allows to render a submodule from a template, important to notice that a submodule inherites all the variables setted for it's parent view
  * @param String $moduleName name of the submodule, this name should match the file name where the submodule templates is stored
  * @param Mixed $params In case there are particular values to set for the submodule, this values can be set passing them as an array $attrName => $attrValue
  * @returns String representing the result of rendering the submodule
  */
  public function renderSubModule($moduleName, $params = null)
  {
     $moduleView = new View($moduleName, $this->variables);
     if ($params !== null) {
        foreach ($params as $key => $value) {
          $moduleView->$key = $value;
        }
     }
     $content = $moduleView->render();
     unset ($moduleView);

     return $content;
  }

  /**
  * Generic setter to add attribute to the class in order to be used for rendering the template
  * @param String $key name of the attribute being queried
  * @param Mixed $value value for the attribute. If the attribute was already set, then it will be replaced by this new value
  */
  public function __set($key, $value) {
    if (! is_array($this->variables)) {
      $this->variables = array();
    }
    
    $this->variables[$key] = $value;
  }
  
  /**
  * Generic getter to access properties for the class in the case particular rules are added for any attribute, this exceptions can be handled in here
  * @param String $key name of the attribute being queried
  * @returns mixed the value of the attribute or null if not exists
  */
  public function __get($key) {
    if (isset($this->variables[$key])) {
      return $this->variables[$key];
    } else {
      return null;
    }
  }
  
  /**
  * Used to set variables which will be required on every view and are available even before view creation
  * @param String $key name of the attribute being queried
  * @param Mixed $value value for the attribute. If the attribute was already set, then it will be replaced by this new value
  */
  public static function setGlobal($key, $value) {
    self::$generalVariables[$key] = $value;
  }
}
