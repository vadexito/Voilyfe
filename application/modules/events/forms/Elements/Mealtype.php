<?php
/**
 * form element for item
 *
 * @author DM
 */


class Events_Form_Elements_Mealtype extends Pepit_Form_Element_Select
{

    protected $_storage = "ZC\Entity\ItemRow";
    
    public function init()
    {
        $this->setOptions(array(
        "required" => false,
        "idDB" => 7,
        ))  ->setLabel('item_mealtype')
            ->setStorageEntity('ZC\Entity\ItemRow')
            ->setTagIdProperty('value');
        
        parent::init();
    }


}

