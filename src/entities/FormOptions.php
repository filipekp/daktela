<?php
  
  namespace filipekp\daktela\entities;
  
  /**
   * Třída FormOptions.
   *
   * @author    Pavel Filípek <pavel@filipek-czech.cz>
   * @copyright © 2020, Proclient s.r.o.
   * @created   22.09.2020
   */
  class FormOptions extends Entity
  {
    /** @var string unique identifier */
    protected $name;
  
    /** @var FormField|string */
    public $field;
  
    /** @var string **required** */
    public $title;
    
    /** @var string */
    public $description;
  
    /** @var string */
    public $value;
  }