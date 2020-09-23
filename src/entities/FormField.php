<?php
  
  namespace filipekp\daktela\entities;
  
  /**
   * Třída FormField.
   *
   * @author    Pavel Filípek <pavel@filipek-czech.cz>
   * @copyright © 2020, Proclient s.r.o.
   * @created   22.09.2020
   */
  class FormField extends Entity
  {
    const TYPE_ADDRESS        = 'ADDRESS';
    const TYPE_CHECKBOX       = 'CHECKBOX';
    const TYPE_DATE           = 'DATE';
    const TYPE_DATETIME       = 'DATETIME';
    const TYPE_EMAIL          = 'EMAIL';
    const TYPE_PHONE          = 'PHONE';
    const TYPE_RADIO          = 'RADIO';
    const TYPE_SELECTBOX      = 'SELECTBOX';
    const TYPE_MULTISELECTBOX = 'MULTISELECTBOX';
    const TYPE_TEXT           = 'TEXT';
    const TYPE_TEXTAREA       = 'TEXTAREA';
    const TYPE_URL            = 'URL';
  
    /** @var string unique identifier */
    protected $name;
  
    /** @var string **required** */
    public $title;
    
    /** @var string */
    public $description;
    
    /** @var string */
    private $type;
    
    /** @var boolean */
    public $multiple;
    
    /** @var string */
    public $pattern;
    
    /** @var string */
    public $options;
    
    /** @var FormOptions[]|string */
    public $items;
    
    /** @var boolean */
    public $deleted;
  
    public function setType($type) {
      if (!defined(__CLASS__ . '::TYPE_' . strtoupper($type))) {
        throw new \Exception('`' . $type . '` is not correct type. Use type from ' . __CLASS__ . '::TYPE_... .');
      }
      
      $this->type = $type;
      
      return $this;
    }
    
    public function getName() {
      return $this->name;
    }
  }