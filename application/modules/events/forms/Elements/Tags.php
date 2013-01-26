<?php
/**
 * form element for item
 *
 * @author DM
 */


class Events_Form_Elements_Tags extends Events_Form_Elements_Abstract_Tags
{

    protected $_containerId = 19;
    protected $_containerType = 'item';
    
    
    
    public function init()
    {
        $this   ->addClass('item_common')
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

