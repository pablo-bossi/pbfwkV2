<?php

namespace dataaccess\solr\filters;

abstract class SolrFilter {

  protected $field;
  protected $value;
  
  public function __construct($field, $value) {
    $this->field = $field;
    $this->value = $value;
  }
  
  public abstract function __toString();
}