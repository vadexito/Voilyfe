<?php
/**
 * form element for item
 *
 * @author DM
 */


class Events_Form_Elements_Typeswimming extends Pepit_Form_Element_Select
{
    protected $_storage = "ZC\Entity\ItemRow";
    
    public function init()
    {
        $this->setOptions(array(
        "required" => false,
        "idDB" => 17,
        ))
          ->setLabel('item_typeswimming')
          ->setStorageEntity('ZC\Entity\ItemRow')
          ->setTagIdProperty('value');
        
        parent::init();
    }


}

