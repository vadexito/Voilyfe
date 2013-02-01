<?php
/**
 * form element for item
 *
 * @author DM
 */


class Events_Form_Elements_CuisineType extends Pepit_Form_Element_Select
{

    protected $_storage = "ZC\Entity\ItemRow";
    
    public function init()
    {
        $this->setOptions([
                "required" => false,
                "idDB" => 8
            ])
            ->setLabel('item_cuisineType')
            ->setStorageEntity('ZC\Entity\ItemRow')
            ->setTagIdProperty('value');
        parent::init();
    }


}

