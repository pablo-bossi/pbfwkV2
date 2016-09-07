<?php

namespace dataaccess\solr;

class SolrQueryExpression {

  public $filters = array();
  public $childExpressions = array();
  protected static $validOperators = array('AND', 'OR');

  public function addFilter($filter, $operator) {
  
    if (in_array($operator, self::$validOperators)) {
      $operand = new \stdClass();
      $operand->filter = $filter;
      $operand->operator = $operator;
      $this->filters[] = $operand;
      return true;
    } else {
      return false;
    }
  }
  
  public function addSubExpression($solrExpression, $operator) {
    $subExpression = new \stdClass();
    $subExpression->operator = $operator;
    $subExpression->expression = $solrExpression;
    $this->childExpressions[] = $subExpression;

    return true;
  }
  
  public function __toString() {
    $isFirst = true;
    $str = '';
    foreach ($this->filters as $filter) {
      if (! $isFirst) {
        $str.='%20'.$filter->operator.'%20';
      }
      $str .= $filter->filter;
      $isFirst = false;
    }
    $counter = 0;
    foreach ($this->childExpressions as $expression) {
      if ((! empty($this->filters) || $counter > 0)) {
        $str .= '%20'.$expression->operator;
      }
      $str .= '%20('.$expression->expression.')';
      $counter++;
    }
    
    return $str;
  }
}