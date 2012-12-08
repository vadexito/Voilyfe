<?php

/**
 * define an element text for crete item form
 * 
 *  
 */


class Pepit_Form_Element_Submit extends Zend_Form_Element_Submit
{
    /**
     *
     * @var boolean true if the rendering should be horizontal
     */
    protected $_horizontal = false;
    
    protected $_decoratorDefault = array(
            array('ViewHelper'),
            array('Description',array('tag' => 'p','class' => 'description','escape' => false)),
    );
    
    protected $_decoratorMobile = array(
            array('ViewHelper'),
            array('Description',array('tag' => 'p','class' => 'description','escape' => false)),
            array('HtmlTag',array(
                'tag' => 'li',
                'class' => 'ui-body'
    )));
    
    protected $_decoratorDefaultHorizontal = array(
            array('ViewHelper'),
            array('Description',array('tag' => 'p','class' => 'description')),
            array(array('control'=> 'HtmlTag'),array('tag' => 'div','class' => 'controls')),
            array(array('controlGroup'=> 'HtmlTag'),array('tag' => 'div','class' => 'control-group'))
    );
    
    protected $_decoratorMobileHorizontal = array(
            array('ViewHelper'),
            array('Description',array('tag' => 'p','class' => 'description')),
            array('HtmlTag',array(
                'tag' => 'li',
                'class' => 'ui-body',
    )));
    
    
    public function init()
    {
        $this->setAttrib('class','btn btn-primary btn-large btn-block');
        $this->setAttrib('data-theme','b');
        $session = new Zend_Session_Namespace('mylife_device_info');
        
        if ($this->_horizontal)
        {
            if ($session->deviceType ===
                Application_Controller_Plugin_MobileInit::DEVICE_TYPE_MOBILE)
            {
                return $this ->setDecorators($this->_decoratorMobileHorizontal);
            }
            else 
            {
                return $this ->setDecorators($this->_decoratorDefaultHorizontal);
            }
        }
        else
        {
            if ($session->deviceType ===
                Application_Controller_Plugin_MobileInit::DEVICE_TYPE_MOBILE)
            {
                return $this ->setDecorators($this->_decoratorMobile);
            }
            else 
            {
                return $this ->setDecorators($this->_decoratorDefault);
            }
        }
    }
    
    public function setHorizontal($horizontal)
    {
        $this->_horizontal = $horizontal;
    }
}
