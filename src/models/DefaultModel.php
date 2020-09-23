<?php
  
  namespace filipekp\daktela\models;
  
  use filipekp\daktela\DaktelaConnector;
  use filipekp\daktela\entities\Entity;
  use filipekp\daktela\helpers\Filter;
  use filipekp\daktela\helpers\Sorting;
  use Tracy\Debugger;

  /**
   * Třída DefaulModel.
   *
   * @author    Pavel Filípek <pavel@filipek-czech.cz>
   * @copyright © 2020, Proclient s.r.o.
   * @created   21.09.2020
   */
  abstract class DefaultModel
  {
    /** @var DaktelaConnector */
    protected static $connector;
    
    /** @var string */
    protected $modelName;
  
    public static function setConnector(DaktelaConnector $connector) {
      self::$connector = $connector;
    }
    
    /**
     * Return collection of model.
     *
     * @param Filter|null  $filter
     * @param array        $fields
     * @param Sorting|null $sorting
     * @param int          $page
     * @param int          $countPerPage
     */
    public function fetch(Filter $filter = NULL, $fields = [], Sorting $sorting = NULL, $page = 1, $countPerPage = 100) {
      $params = [];
      
      $page = $page - 1;
      $params['take'] = $countPerPage;
      $params['skip'] = $page * $countPerPage;
      if (!is_null($filter)) { $params['filter'] = $filter->resultFilter(); }
      if (!is_null($sorting)) { $params['sort'] = $sorting->resultSorting(); }
      if ($fields) { $params['fields'] = $fields; }
      
      return self::$connector->execute($this->modelName, $params);
    }
  
    /**
     * Return all of collection from model.
     *
     * @param Filter|null  $filter
     * @param array        $fields
     * @param Sorting|null $sorting
     */
    public function fetchAll(Filter $filter = NULL, $fields = [], Sorting $sorting = NULL) {
      $returned = $total = 0;
      $page = 1;
      $allData = [];
      
      do {
        $response = $this->fetch($filter, $fields, $sorting, $page, 1000);
        if (self::$connector->getLastHttpStatus() == 200) {
          $data = $response->result->data;
          $allData = array_merge($allData, $data);
          
          $returned += count($data);
          
          if ($total == 0) {
            $total = $response->result->total;
          }
        }
        
        $page++;
      } while ($returned < $total);
      
      return $allData;
    }
  
    /**
     * Create one instance of model.
     *
     * @param Entity $object
     *
     * @return mixed
     * @throws \Exception
     */
    public function create(Entity $object) {
      return self::$connector->execute($this->modelName, [], $object->getAsArray(), DaktelaConnector::REQTYPE_POST);
    }
  
    /**
     * Update fields in one instance.
     *
     * @param Entity $object
     *
     * @return mixed
     * @throws \Exception
     */
    public function update(Entity $object) {
      return self::$connector->execute($this->modelName . '/' . $object->getName(), [], $object->getAsArray(), DaktelaConnector::REQTYPE_PUT);
    }
  
    /**
     * Return one instance.
     *
     * @param $name
     *
     * @return mixed
     * @throws \Exception
     */
    public function read($name) {
      return self::$connector->execute($this->modelName . '/' . $name);
    }
  
    /**
     * Delete one instance.
     *
     * @param $name
     *
     * @return bool
     * @throws \Exception
     */
    public function delete($name) {
      self::$connector->execute($this->modelName . '/' . $name, [], [], DaktelaConnector::REQTYPE_DELETE);
      if (self::$connector->getLastHttpStatus() == 204) {
        return TRUE;
      } elseif (self::$connector->getLastHttpStatus() == 404) {
        throw new \Error('Object `' . $this->modelName . '/' . $name . '` not found!', 404);
      }
      
      return FALSE;
    }
  }