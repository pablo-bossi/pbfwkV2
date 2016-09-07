<?php

namespace dataaccess\solr;

class SolrFacet {

  protected $field;
  protected $minCount = null;
  protected $prefix = null;
  protected $sort = null;
  protected $limit = null;
  protected $offset = null;

  public function __construct($fieldName) {
    $this->field = $fieldName;
  }
  
  public function __set($key, $value) {
    if (property_exists($this, $key)) {
      $this->$key = $value;
    }
  }
  
  public function __toString() {
    $str = '&facet.field='.$this->field;
    
    if ($this->minCount !== null) {
      $str .= '&f.'.$this->field.'.facet.mincount='.$this->minCount;
    }
    if (! empty($this->prefix)) {
      $str .= '&f.'.$this->field.'.facet.prefix='.$this->minCount;
    }
    if (! empty($this->sort)) {
      $str .= '&f.'.$this->field.'.facet.sort='.$this->sort;
    }
    if ($this->limit !== null) {
      $str .= '&f.'.$this->field.'.facet.limit='.$this->limit;
    }
    if ($this->offset !== null) {
      $str .= '&f.'.$this->field.'.facet.offset='.$this->offset;
    }
    
    return $str;
  }
}