<?php

/**
 * define an element text for crete item form
 * 
 *  
 */

class Pepit_Form_Element_MultiCheckbox extends Pepit_Form_Element_Multi
{
    /**
     * Use formSelect view helper by default
     * @var string
     */
    public $helper = 'formMultiCheckbox';
    
    /**
     * MultiCheckbox is an array of values by default
     * @var bool
     */
    protected $_isArray = true;
    
    public function init()
    {
        parent::init();
        
        $nullFilter = new Zend_Filter_Null();
        $nullFilter->setType(Zend_Filter_Null::STRING);
        $this->addFilter($nullFilter);
    }
    
    
    /**
     * Load default decorators
     *
     * @return Zend_Form_Element_MultiCheckbox
     */
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return $this;
        }

        parent::loadDefaultDecorators();

        // Disable 'for' attribute
        if (false !== $decorator = $this->getDecorator('label')) {
            $decorator->setOption('disableFor', true);
        }

        return $this;
    }
}
