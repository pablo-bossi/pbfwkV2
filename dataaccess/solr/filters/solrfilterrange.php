<?php

namespace dataaccess\solr\filters;

class SolrFilterRange extends SolrFilter {
  protected $from;
  protected $to;

  public function __construct($field, $from, $to) {
    $this->field = $field;
    $this->from = $from;
    $this->to = $to;
  }

  public function __toString() {
    return $this->field.':['.$this->from.'%20TO%20'.$this->to.']';
  }
}