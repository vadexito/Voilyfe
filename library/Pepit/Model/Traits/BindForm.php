<?php
/**
 * Model Trait
 *
 * @package    Mylife
 * @author     DM
 */
Trait Pepit_Model_Traits_BindForm
{
    
    /**
     * form objects associated to the model
     * @var array
     */
    protected $_forms = array();
    
    protected $_form;
    
    
    
    public function bindToForm($form)
    {
        $this->_form = $form;
        $form->setModel($this);
    }
    
    /**
     * Get a formular
     *
     * @mode string $mode Action for identifying the formular
     * @options 
     * @return Zend_Form
     */
    public function getForm($mode = NULL,$options=NULL,$module=NULL)
    {
        if ($this->_form === NULL)
        {
            
            // check if mode exists
            if (!isset($this->getFormClasses()[$mode]))
            {
                // if not throw exception
                throw new Pepit_Model_Exception('Unknow Formular');
            }

            // check if form object exists (pseudo cached)
            if (!isset($this->_forms[$mode])) 
            {
               $this->_forms[$mode] = self::loadForm(
                        $this->getFormClasses()[$mode],
                        $options,
                        $module
                ); 

            }

            $this->bindToForm($this->_forms[$mode]);
        }
        
        return $this->_form;
    }
    
    public function getFormClasses()
    {
        return $this->_formClasses;
    }
    
    abstract function loadForm($formMode,$options);
    
}
