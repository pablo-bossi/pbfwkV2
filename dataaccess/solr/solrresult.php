<?php

namespace dataaccess\solr;

class SolrResult {

  protected $numFound;
  protected $docs;
  protected $facets;

  public function __get($key) {
    if (property_exists($this, $key)) {
      return $this->$key;
    } else {
      return null;
    }
  }
  
  public function __construct($jsonResults) {
    $results = json_decode($jsonResults);

    $this->numFound = $results->response->numFound;
    $this->docs = $results->response->docs;
    $facets = $results->facet_counts->facet_fields;

    $facetFields = get_object_vars($facets);

    foreach ($facetFields as $field => $facetCounts) {
      $list = array();
      for ($counter = 0; $counter < count($facetCounts); $counter += 2) {
        $facet = new \stdClass();
        $facet->value = $facetCounts[$counter];
        $facet->count = $facetCounts[($counter + 1)];
        $list[] = $facet;
      }
      $this->facets[$field] = $list;
    }
  }
}