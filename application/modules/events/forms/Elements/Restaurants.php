<?php
/**
 * form element for item
 *
 * @author DM
 */


class Events_Form_Elements_Restaurants extends Events_Form_Elements_Abstract_Tags
{

    protected $_containerId = 10;
    protected $_containerType = 'itemGroup';
    protected $_itemName = 'restaurants';
    
    
    public function init()
    {
        $this   ->setRequired(false)
                ->setAttrib($this->_containerType.'Id',$this->_containerId)
                ->setTagEntity('ZC\Entity\ItemGroupRows\\'.ucfirst($this->_itemName).'ItemGroupRows')
                ->setMultiTag(false)
                ->setModel(Pepit_Model_Doctrine2::loadModel($this->_containerType.'Rows'));
        
        parent::init();
        
    }


}

