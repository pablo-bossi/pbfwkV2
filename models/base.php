<?php

namespace Models;

abstract class Base {
  protected $loaded = false;
  protected $dirty = false;
  //Format: dbName => attributeName
  protected $fieldsMapping;
  //Format: attributeName => array (
  //                              array('type' => 'validationtype', params => array()),
  //                              array('type' => 'validationClass2'),
  //                            )
  protected $validationRules;
  private $validatorsMapping = array('int' => 'integer');

  public function __construct() {
    $this->_setFieldsMapping();
    $this->_setValidationRules();
  }

  protected function _setFieldsMapping() {
    return true;
  }
  
  protected function _setValidationRules() {
    return true;
  }
  
  public function fillData($data) {
    $haveMappings = (is_array($this->fieldsMapping) && ! empty($this->fieldsMapping));
    $keys = array_keys($data);
    
    foreach ($keys as $key) {
      $lkey = strtolower($key);
      if ($data[$key] !== null) {
        if ($haveMappings && isset($this->fieldsMapping[$lkey])) {
          $this->__set($this->fieldsMapping[$lkey], $data[$key]);
        } else {
          $this->__set($lkey, $data[$key]);
        }
      }
    }
    $this->loaded = true;
  }
  
  public function save() {
    //if dirty update, if not loaded insert (Can pass the values based on the mapping)
  }

  public function __get($key) {
    if (! isset($this->$key)) {
      return null;
    }
    return $this->$key;
  }
  
  public function __set($key, $value) {
    if ($key != 'loaded') {
      //TODO: Validate you can't set attributes which are not in the list.
      if (is_array($this->validationRules) && isset($this->validationRules[$key])) {
        $validation = new \fwk\validators\Validate($key, $value);
        $iCounter = 0;
        //Aborts in case some of the validations fails
        while (($iCounter < count($this->validationRules[$key])) && ($validation->isValid())) {
          //Build validator class name
          $validatorType = strtolower($this->validationRules[$key][$iCounter]['type']);
          $className = (isset($this->validatorsMapping[$validatorType])?$this->validatorsMapping[$validatorType]:$validatorType);
          $class = '\fwk\validators\\'.ucfirst(strtolower($className));
          $hasParameters = isset($this->validationRules[$key][$iCounter]['params']);
          if ($hasParameters) {
            $params = $this->validationRules[$key][$iCounter]['params'];
            $reflect  = new \ReflectionClass($class);
            $instance = $reflect->newInstanceArgs($params);
            $validation->validate($instance);
          } else {
            $validation->validate(new $class);
          }
          $iCounter++;
        }
        if ($validation->isValid()) {
          //Only update in case there was a change, this way save will only impact DB if there are real changes
          if ($this->$key !== $value) {
            $this->$key = $value;
            $this->dirty = true;
          }
        } else {
          throw new \fwk\exceptions\InvalidInput(sprintf(_('Invalid value (%s) for property %s'), $value, $key));
        }
      } else {
        if ($this->$key !== $value) {
          $this->$key = $value;
          $this->dirty = true;
        }
      }
    }
  }
  
  protected function _validate() {
    //Override this method for specific validation before saving (Example, checking duplicate Ids)
    return true;
  }
}