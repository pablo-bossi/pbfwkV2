<?php

namespace dataaccess\solr\filters;

class SolrFilterEquals extends SolrFilter {

  public function __toString() {
    if ($this->value != '*') {
      $hasWildCards = (strpos($this->value, '*') !== false);
      if ($hasWildCards) {
        return $this->field.':'.$this->value;
      } else {
        return $this->field.':"'.$this->value.'"';
      }
    } else {
      return $this->field.':*';
    }
  }
}