<?php
/**
 * form element for item
 *
 * @author DM
 */


class Events_Form_Elements_Tags extends Pepit_Form_Element_Tags
{

    protected $_containerId = 19;
    protected $_containerType = 'item';
    
    
    
    public function init()
    {
        $this   ->setAttrib('class','item_common')
                ->setAttrib('data-item-name','tags')
                ->setAttrib('data-property-name','tags')
                ->setLabel('item_tags')
                ->setRequired(false)
                ->setTagEntity('ZC\Entity\\'.ucfirst($this->_containerType).'Row')
                ->setModel(Pepit_Model_Doctrine2::loadModel($this->_containerType.'Rows'))
                ->setMultiTag(true);
        
        parent::init();
        
        
    }
    
    


}

