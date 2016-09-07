<?php

namespace dataaccess\solr;

class SolrFacetCollection {

  protected $minCount = null;
  protected $prefix = null;
  protected $sort = null;
  protected $limit = null;
  protected $offset = null;
  protected $facets = array();

  public function addFacet($facet) {
    $this->facets[] = $facet;
  }
  
  public function __set($key, $value) {
    if (property_exists($this, $key)) {
      $this->$key = $value;
    }
  }
  
  public function __toString() {

    if (! empty($this->facets)) {
      $str = 'facet=true';
      if ($this->minCount !== null) {
        $str .= '&facet.mincount='.$this->minCount;
      }
      if (! empty($this->prefix)) {
        $str .= '&facet.prefix='.$this->minCount;
      }
      if (! empty($this->sort)) {
        $str .= '&facet.sort='.$this->sort;
      }
      if ($this->limit !== null) {
        $str .= '&facet.limit='.$this->limit;
      }
      if ($this->offset !== null) {
        $str .= '&facet.offset='.$this->offset;
      }
    }
    
    foreach ($this->facets as $facet) {
      $str .= $facet;
    }
    
    return $str;
  }
}