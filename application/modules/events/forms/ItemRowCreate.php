<?php

/**
 * form for Event creation
 * 
 * Author : DM 
 */



class Events_Form_ItemRowCreate extends Pepit_Form
{
    
    protected $_containerId;
    
    protected $_model;
    
    public function init()
    {
        
        $this->setMethod('post');

        $textElement = new Pepit_Form_Element_Text('value');
        $textElement->setAttrib('data-property-name','value');
        $textElement->addDecorator('label',['placement'=>'prepend']);
                    
        
        //get container entity
        $container = $this->_model->getEntityManager()
                            ->getRepository('ZC\Entity\Item')
                            ->find($this->_containerId);
                    
        $textElement->setLabel('Value for item row of element '.$container->name.'');
        
        
        $containerId = new Zend_Form_Element_Hidden(
            'itemId',
            array('value'     => $this->_containerId)
        ); 
        $containerId->addValidator('Int')
                    ->setRequired(true);
        
        
        //create submit element        
        $submit = new Pepit_Form_Element_Submit('submit_insert');
        $submit->setLabel('action_save');
        
        $this->addElements(array(
            $textElement,
            $containerId,
            $submit
        ));
    }
    
    /**
     * Setter automaticaly loaded before init() in the array options
     * 
     * @param string $type 
     */
    
    public function setContainerId($containerId)
    {
        $this->_containerId = $containerId;
    }
    
    public function setModel($model)
    {
        $this->_model = $model;
    }
}

