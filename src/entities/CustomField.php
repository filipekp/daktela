<?php
  
  namespace filipekp\daktela\entities;
  
  use filipekp\daktela\models\FormFields;

  /**
   * Třída CustomField.
   *
   * @author    Pavel Filípek <pavel@filipek-czech.cz>
   * @copyright © 2020, Proclient s.r.o.
   * @created   22.09.2020
   */
  class CustomField extends Entity
  {
    /** @var Contact|string */
    public $contact;
    
    /** @var FormField|string */
    private $field;
    
    /** @var string **required** */
    public $position;
    
    /** @var array */
    public $value;
    
    /** @var string */
    public $reverseValue;
  
    public function setField($name) {
      $cfm = new FormFields();
      $cf = new FormField($cfm->read($name));
      
      $this->field = $cf;
    }
  
    public function getField() {
      return $this->field->getName();
    }
  
    public function getContact() {
      return $this->contact->getName();
    }
  }