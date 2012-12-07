<?php

/**
 * form for Event creation
 * 
 * @author DM 
 */

abstract class Events_Form_Abstract_GeneralizedItemRowCreate extends Pepit_Form
{
    /**
     * 
     * @var string $_containerId
     */
    protected $_containerId;
    
    protected $_containerType; // category or item group
    
    protected $_container;
    
    public function init()
    {
        parent::init();   
        $this->setMethod('post');
        
        $container = $this->_initContainer();
        $this->setAttrib('data-container-name',$container->name);

         // add each element of the form
        foreach ($container->items as $generalizedItem)
        {
            $this->addItemFormElement($generalizedItem,$container);
        }
        
        //create submit element        
        $submit = new Pepit_Form_Element_Submit('submit_insert',array(
            'horizontal' => true
        ));
        $submit->setLabel('action_save');
        
        //add element submit
        $this->addElement($submit);
    }
    
    /**
     *
     * return one or two form elements
     * @param ZC\Entity\GeneralizedItem $generalizedItem
     * @param type $containerName
     * @return array of form element 
     */
    public function addItemFormElement(ZC\Entity\GeneralizedItem $generalizedItem,
                                                    $container)
    {
               
        $propertyName = $this->_model->getPropertyName(
            $container->name,
            $generalizedItem->name
        );
        $formElementName = Backend_Model_Items::getFormItemName(
                    $propertyName,
                    $generalizedItem->associationType
        );
        
        $class = Backend_Model_Items::getFormElementClassName(
                $generalizedItem->name
            );
        $label = 'item_'.$generalizedItem->name;
        
        $formElement = new $class($formElementName);
        $formElement->setHorizontal(true)
                    ->setAttrib('data-property-name',$propertyName)
                    ->setAttrib('data-item-type',$generalizedItem->itemType)
                    ->setAttrib('data-category-name',$container->name)
                    ->setAttrib('data-item-name',$generalizedItem->name)
                    ->setAttrib('data-containerId',$container->id)
                    ->setLabel($label);
        
        if ($this->siteIsMobile())
        {
            $formElement->removeDecorator('label');
        }
        
        $this->addElement($formElement);
    }
    
    protected function _initContainer()
    {
        // the parameter is initialized through constructor (with an array) and setter
        $containerId = $this->_containerId;
        
        //get container entity
        $this->_container = $this->_model->getEntityManager()
                            ->getRepository('ZC\Entity\\'.ucfirst(
                                $this->_containerType
                        ))
                        ->find($containerId);
        
        $containerIdElement = new Zend_Form_Element_Hidden(
            $this->_containerType.'Id',
            array('value'     => $this->_containerId)
        ); 
        $containerIdElement ->addValidator('Int')
                            ->setRequired()
                            ->setAttrib('data-property-name',$this->_containerType.'Id');
        $this->addElement($containerIdElement);
        
        return $this->_container;
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
    
    
    
    public function setContainerType($containerType)
    {
        $this->_containerType = $containerType;
    }


    public function getContainerId()
    {
        return $this->_containerId;
    }
    
    
}


