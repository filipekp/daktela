<?php
  
  namespace filipekp\daktela\helpers;
  
  use PF\helpers\MyArray;

  /**
   * Třída Filter.
   *
   * @author    Pavel Filípek <pavel@filipek-czech.cz>
   * @copyright © 2020, Proclient s.r.o.
   * @created   21.09.2020
   */
  class Filter
  {
    /** equal to */
    const OPERATOR_EQ = 'eq';
    /** not equal to */
    const OPERATOR_NEQ = 'neq';
    /** number less than */
    const OPERATOR_LT = 'lt';
    /** number less than or equal to */
    const OPERATOR_LTE = 'lte';
    /** number greater than */
    const OPERATOR_GT = 'gt';
    /** number greater than or equal to */
    const OPERATOR_GTE = 'gte';
    /** number greater than or equal to */
    const OPERATOR_LIKE = 'like';
    /** string contains part of string */
    const OPERATOR_CONTAINS = 'contains';
    /** string contains part of string */
    const OPERATOR_STARTSWITH = 'startswith';
    /** string contains part of string */
    const OPERATOR_ENDSWITH = 'endswith ';
    /** string does not contains part of string */
    const OPERATOR_NOTLIKE = 'notlike ';
    /** string does not contains part of string */
    const OPERATOR_DOESNOTCONTAIN = 'doesnotcontain ';
    /** is exactly null */
    const OPERATOR_ISNULL = 'isnull ';
    /** is not exactly null */
    const OPERATOR_ISNOTNULL = 'isnotnull ';
    /** from array */
    const OPERATOR_IN = 'in ';
    /** not from array */
    const OPERATOR_NOTIN = 'notin ';
    
    const LOPERATOR_AND = 'and';
    const LOPERATOR_OR = 'or';
    public static $AVAIL_LOPERATORS = [
      self::LOPERATOR_AND, self::LOPERATOR_OR
    ];
    
    private $loperator = self::LOPERATOR_AND;
    private $filters = NULL;
  
    public function __construct() {}
  
    /**
     * Set logic operator in filter object.
     *
     * @param $operator
     *
     * @return $this
     * @throws \Exception
     */
    public function setOperator($operator) {
      if (!in_array($operator, self::$AVAIL_LOPERATORS)) {
        throw new \Exception('`' . $operator . '` is not available operator. Use operators from ' . __CLASS__ . '::$AVAIL_LOPERATORS');
      }
      
      $this->loperator = $operator;
      
      return $this;
    }
  
    /**
     * Add filter rule.
     *
     * @param string      $field
     * @param string      $operator
     * @param string      $value
     * @param string|null $type
     *
     * @return $this
     * @throws \Exception
     */
    public function addFilter(string $field, string $operator, string $value, string $type = NULL) {
      if (!defined(__CLASS__ . '::OPERATOR_' . strtoupper($operator))) {
        throw new \Exception('`' . $operator . '` is not available operator. Use operators from ' . __CLASS__ . '::OPERATOR_...');
      }
      
      $filter = [
        'field'    => $field,
        'operator' => $operator,
        'value'    => $value,
      ];
      
      if (!is_null($type)) {
        $filter['type'] = $type;
      }
      
      $this->filters[] = $filter;
      
      return $this;
    }
  
    /**
     * Add filter rule as subfilter.
     *
     * @param Filter $filter
     *
     * @return $this
     */
    public function addSubFilter(Filter $filter) {
      $this->filters[] = $filter;
      
      return $this;
    }
  
    /**
     * Return result filter.
     *
     * @param false $isSubFilter
     *
     * @return array|array[]|mixed|null
     */
    public function resultFilter($isSubFilter = FALSE) {
      if (count($this->filters) == 1) {
        $farr = MyArray::init($this->filters);
        return $farr->first();
      }
      
      array_walk($this->filters, function(&$filterItem) {
        if (is_a($filterItem, __CLASS__)) {
          $filterItem = $filterItem->resultFilter(TRUE);
        }
      });
      
      return [
        'logic' => $this->loperator,
        'filters' => $this->filters,
      ];
      
//      if (!$isSubFilter) {
//        $result = [
//          'filter' => $result,
//        ];
//      }
//
//      return $result;
    }
  
    /**
     * Create filter object.
     *
     * @return Filter
     */
    public static function create() {
      return new self();
    }
  }