<?php
  
  namespace filipekp\daktela\models;

  use filipekp\daktela\entities\Entity;

  /**
   * Třída CustomFields.
   *
   * @author    Pavel Filípek <pavel@filipek-czech.cz>
   * @copyright © 2020, Proclient s.r.o.
   * @created   23.09.2020
   */
  class FormFields extends DefaultModel
  {
    protected $modelName = 'formFields';
  
    public function create() { }
  
    public function update(Entity $object) { }
  }