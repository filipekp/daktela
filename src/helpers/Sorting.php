<?php
  
  namespace filipekp\daktela\helpers;
  
  use PF\helpers\MyArray;

  /**
   * Třída Sorting.
   *
   * @author    Pavel Filípek <pavel@filipek-czech.cz>
   * @copyright © 2020, Proclient s.r.o.
   * @created   21.09.2020
   */
  class Sorting
  {
    const DIR_ASC  = 'asc';
    const DIR_DESC = 'desc';
    
    private $sortingRules = NULL;
  
    /**
     * Add sorting rule.
     *
     * @param string $field
     * @param string $dir
     *
     * @return $this
     * @throws \Exception
     */
    public function addRule(string $field, string $dir = self::DIR_ASC) {
      if (!defined(__CLASS__ . '::DIR_' . strtoupper($dir))) {
        throw new \Exception('Direction `' . $dir . '` is not exists. Use directions from constants ' . __CLASS__ . '::DIR_...');
      }
      
      $filter = [
        'field' => $field,
        'dir'   => $dir,
      ];
      
      
      $this->sortingRules[] = $filter;
      
      return $this;
    }
  
    /**
     * Return result sorting.
     *
     * @return array|array[]|mixed|null
     */
    public function resultSorting() {
      if (count($this->sortingRules) == 1) {
        $farr = MyArray::init($this->sortingRules);
        return $farr->first();
      }
      
      return $this->sortingRules;
    }
  
    /**
     * Create filter object.
     *
     * @return Sorting
     */
    public static function create() {
      return new self();
    }
  }