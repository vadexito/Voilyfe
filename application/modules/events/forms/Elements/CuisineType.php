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
        $this->setOptions(array(
        "required" => false,
        "idDB" => 8,
        "filters" => array(),
        "validators" => array(),
        ))->setLabel('item_cuisineType');
        parent::init();
    }


}

