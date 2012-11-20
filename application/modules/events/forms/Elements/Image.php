<?php
/**
 * form element for item
 *
 * @author DM
 */


class Events_Form_Elements_Image extends Pepit_Form_Element_Image
{

    protected $_id = null;
    
    protected $_storage = 'ZC\Entity\Image';
    
    public function init()
    {
        $this ->setAttrib('class','item_common')
              ->setAttrib('data-item-name','image')
              ->setAttrib('data-property-name','image')
              ->setLabel('item_image')
              ->setRequired(false)
              ->setAttrib(Pepit_Form_Element::DATA_ITEM_ATTRIB,'image');
        
        parent::init();
    }
    
    


}

