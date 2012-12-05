<?php
/**
 * form element for item
 *
 * @author DM
 */


class Events_Form_Elements_Medicine extends Pepit_Form_Element_Select
{
    protected $_storage = "ZC\Entity\ItemRow";
    
    public function init()
    {
        $this->setOptions(array(
        "required" => true,
        "idDB" => 11,
        ))
           ->setLabel('item_medicine')     
           ->setStorageEntity('ZC\Entity\ItemRow');
        parent::init();
    }


}

