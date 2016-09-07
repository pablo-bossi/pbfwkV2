<?php

namespace dataaccess\solr\filters;

class SolrFilterNot extends SolrFilter {

  public function __toString() {
     return '-'.$this->field.':'.$this->value;
  }
}