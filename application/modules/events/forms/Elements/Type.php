<?php
/**
 * form element for item
 *
 * @author DM
 */


class Events_Form_Elements_Type extends Pepit_Form_Element_Select
{

    protected $_storage = "ZC\Entity\ItemRow";
    
    public function init()
    {
        $this->setOptions(array(
        "required" => false,
        "idDB" => 21,
        "multioptionTarget" => 'ZC\Entity\ItemRow',
        "filters" => array('HtmlEntities','StringTrim',),
        ))
        ->setLabel('item_type')
        ->setStorageEntity('ZC\Entity\ItemRow');
        
        
        parent::init();
    }
}

