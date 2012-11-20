<?php
/**
 * form element for item
 *
 * @author DM
 */


class Events_Form_Elements_Location extends Pepit_Form_Element_Location
{

    protected $_storage = 'ZC\Entity\Location';
    
    public function init()
    {
       $this->setAttrib('class','item_common')
            ->setAttrib('data-item-name','location')
            ->setAttrib('data-property-name','location')
            ->setLabel('item_location')
            ->setRequired(false)
            ->setAttrib(Pepit_Form_Element::DATA_ITEM_ATTRIB,'location');
        
        parent::init();
    }
}

