<?php

namespace Models;

Class Example extends \Models\Base {
  protected $id;
  protected $username;
  protected $email;
  protected $active;
  protected $role;

  protected function _setFieldsMapping() {
    $this->fieldsMapping = array (
      'user_id' => 'id',
      'username' => 'username',
      'email_address' => 'email',
      'active' => 'active',
      'access_level' => 'role',
    );
    
    return true;
  }

  protected function _setValidationRules() {
    $this->validationRules = array(
      'id' => array(
        array('type' => 'required'),
        array('type' => 'int'),
      ),
      'username' => array(
        array('type' => 'required'),
        array('type' => 'textlength', 'params' => array('min' => 3, 'max' => 10)),
      ),
      'email' => array(
        array('type' => 'required'),
        array('type' => 'email'),
      ),
      'active' => array(
        array('type' => 'required'),
        array('type' => 'int'),
        array('type' => 'numberrange', 'params' => array('min' => 0, 'max' => 1)),
      ),
    );
  }
}