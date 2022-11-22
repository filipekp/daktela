<?php
  
  namespace filipekp\daktela\entities;
  
  use PF\helpers\MyString;
  use Tracy\Debugger;

  /**
   * Třída Entity.
   *
   * @author    Pavel Filípek <pavel@filipek-czech.cz>
   * @copyright © 2020, Proclient s.r.o.
   * @created   22.09.2020
   */
  class Entity
  {
    private $notTransformPropertiesName = [];
    protected $requiredProperties = [];
    
    public function __construct($data = []) {
      if ($data && is_array($data)) {
        foreach ($data as $property => $value) {
          $classProperty = lcfirst(MyString::camelize($property));
          $methodName = 'set' . ucfirst($classProperty);
          if (method_exists($this, $methodName)) {
            $this->{$methodName}($value);
          } elseif (property_exists($this, $classProperty)) {
            $this->{$classProperty} = $value;
          }
        }
      }
    }
  
    /**
     * @param $property
     *
     * @return Entity
     */
    protected function setPropertyAsRequired($property) {
      $this->requiredProperties[$property] = 'required';
    
      return $this;
    }
  
    public function checkRequiredForNewEntity() {
      $notFilled = [];
    
      foreach ($this->requiredProperties as $requiredProperty => $_) {
        if (is_null($this->{$requiredProperty})) {
          $notFilled[] = $requiredProperty;
        }
      }
    
      if ($notFilled) {
        throw new \InvalidArgumentException(vsprintf('Properties `%s` are required!', [implode('`, `', $notFilled)]));
      }
    }
  
    private function getAsArrayInternal(array $props) {
      $props = array_filter($props, function($val, $key) {
        return !is_null($val) &&
          (is_int($key) || !is_int($key) && !in_array($key, ['registry', 'language', 'requiredProperties', 'salesChannels']));
      }, ARRAY_FILTER_USE_BOTH);
    
      $props = array_combine(
        array_map(function($key) {
          if (in_array($key, $this->notTransformPropertiesName)) {
            return $key;
          }
          return MyString::decamelize($key);
        }, array_keys($props)),
        array_map(function($item) {
          if (is_subclass_of($item, __CLASS__)) {
            return $item->getAsArray();
          } elseif (is_a($item, '\DateTime')) {
            return $item->format('Y-m-d H:i:s');
          } elseif (is_array($item)) {
            return $this->getAsArrayInternal($item);
          } elseif (!is_object($item)) {
            return $item;
          }
        }, $props)
      );
    
      return $props;
    }
  
    public function getAsArray() {
      $this->checkRequiredForNewEntity();
      
      $r = new \ReflectionClass(get_called_class());
      $props = $r->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PRIVATE | \ReflectionProperty::IS_PROTECTED);
      $props = array_combine(
        array_map(function($prop) {
          /** @var $prop \ReflectionProperty */
          return $prop->name;
        }, $props),
        array_map(function($prop) {
          /** @var $prop \ReflectionProperty */
          $prop->setAccessible(TRUE);
          
          if (method_exists($this, 'get' . ucfirst($prop->name))) {
            $val = $this->{'get' . ucfirst($prop->name)}();
          } else {
            $val = $prop->getValue($this);
          }
          
          return $val;
        }, $props)
      );
    
      return $this->getAsArrayInternal((array)$props);
    }
  
    protected function setPropertyAsNoTransformName($property) {
      $this->notTransformPropertiesName[] = $property;
    }
  
    public function __toString() {
      return json_encode($this->getAsArray(), JSON_PRETTY_PRINT);
    }
  }