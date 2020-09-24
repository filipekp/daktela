<?php
  
  namespace filipekp\daktela\entities;
  
  use Tracy\Debugger;

  /**
   * Třída Contact.
   *
   * @author    Pavel Filípek <pavel@filipek-czech.cz>
   * @copyright © 2020, Proclient s.r.o.
   * @created   22.09.2020
   */
  class Contact extends Entity
  {
    /** @var string unique identifier */
    protected $name;
    
    /** @var string **required** */
    public $title;
    
    /** @var string */
    public $firstname;
    
    /** @var string **required** */
    public $lastname;
    
    /** @var string */
    public $account;
    
    /** @var string */
    public $user;
    
    /** @var string */
    public $description;
    
    /** @var string */
    protected $npsScore;
    
    /** @var \DateTime */
    protected $created;
    
    /** @var \DateTime */
    protected $edited;
    
    /** @var CustomField[]|string */
    protected $customFields;
  
    public function __construct($data = []) {
      $this->setPropertyAsRequired('name')
        ->setPropertyAsRequired('lastname');
      
      $this->setPropertyAsNoTransformName('customFields');
      
      parent::__construct($data);
    }
  
    public function setCustomFields($customFields) {
      $this->customFields = [];
      
      if ($customFields) {
        foreach ($customFields as $customFieldName => $values) {
          $customField = new CustomField();
          $customField->contact = $this;
          $customField->value = $values;
          $customField->setField($customFieldName);
          
          $this->customFields[] = $customField;
        }
      }
      
      return $this;
    }
  
    /**
     * @return array
     */
    public function getCustomFields() {
      if (!$this->customFields) {
        return NULL;
      }
      
      $returnArray = [];
      
      foreach ($this->customFields as $customField) {
        $returnArray[$customField->getField()] = $customField->value;
      }
      
      return $returnArray;
    }
  
    public function getName() {
      return $this->name;
    }
  
    /**
     * @param $identifier
     * @param $title
     * @param $lastname
     *
     * @return Contact
     */
    public static function create($identifier, $title, $lastname) {
      return new self([
        'name'     => $identifier,
        'title'    => $title,
        'lastname' => $lastname,
      ]);
    }
  }