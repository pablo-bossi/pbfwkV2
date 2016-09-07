<?php

namespace dataaccess\solr;

class SolrQuery {
  protected $limit = 50;
  protected $offset = 0;
  protected $sorts = array();
  protected $facetCollection = null;
  protected $expressions = array();
  protected $filterQueries = array();
  protected static $validOperators = array('AND', 'OR', 'AND NOT', 'OR NOT');

  public function __construct($fl, $offset, $limit) {
    $this->fl = implode(',', $fl);
    $this->offset = $offset;
    $this->limit = $limit;
  }
  
  public function addExpression($expression, $operator) {
    if (in_array($operator, self::$validOperators)) {
      $operand = new \stdClass();
      $operand->expression = $expression;
      $operand->operator = $operator;
      $this->expressions[] = $operand;
      return true;
    } else {
      return false;
    }
  }
  
  public function setFacets($facetCollection) {
    $this->facetCollection = $facetCollection;
    return true;
  }
  
  public function addFilterQueries($expression) {
    $this->filterQueries[] = $expression;
    return true;
  }
  
  public function addSortCriteria($sort) {
    $this->sorts[] = $sort;
  }
  
  public function __toString() {
    $str = '';
    $isFirst = true;
    foreach ($this->expressions as $expression) {
      if (! $isFirst) {
        $str .= '%20'.$expression->operator.'%20';
      } else {
        $str .= 'q=';
      }
      $str .= '('.$expression->expression.')';
      $isFirst = false;
    }
    if ($this->facetCollection != null) {
      $str .= '&'.$this->facetCollection;
    }
    if (! empty($this->filterQueries)) {
      foreach ($this->filterQueries as $expression) {
        $str .= '&fq='.$expression;
      }
    }
    if (! empty($this->sorts)) {
      $str .= '&sort=';
      $str .= implode(',', $this->sorts);
    }
    
    $str .= '&fl='.$this->fl.'&start='.$this->offset.'&rows='.$this->limit.'&wt=json';
    
    return $str;
  }

}