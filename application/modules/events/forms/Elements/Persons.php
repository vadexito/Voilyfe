<?php
/**
 * form element for item
 *
 * @author DM
 */


class Events_Form_Elements_Persons extends Events_Form_Elements_Abstract_Tags
{

    protected $_containerId = 18;
    protected $_containerType = 'item';
    
    
    public function init()
    {
        
        
        $this   ->setAttrib('class','item_common')
                ->setAttrib('data-item-name','persons')
                ->setAttrib('data-property-name','persons')
                ->setLabel('item_persons')
                ->setRequired(false)
                ->setAttrib('placeholder',$this->getTranslator()->translate('msg_press_return_to_save'))
                ->setTagEntity('ZC\Entity\\'.ucfirst($this->_containerType).'Row')
                ->setModel(Pepit_Model_Doctrine2::loadModel($this->_containerType.'Rows'))
                ->setMultiTag(true);
        
        parent::init();
        
        
        
    }
}

