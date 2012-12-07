<?php

/*
* @author DM
* 
*/

class Pepit_Form extends Zend_Form
{
    use Pepit_Doctrine_Trait;
    
    protected $_model;
    protected $_siteIsMobile;
    
    public function init()
    {
        $this->addPrefixPath(
            'Pepit_Form_Decorator',
            'Pepit/Form/Decorator',
            'decorator'
        );
        
        $this->setDecorators(array(
            'FormElements',
            'Form'
        ));
        
        
        if ($this->siteIsMobile())
        {
            $this->addAttribs(['data-role' => 'form','data-ajax' => 'false']);
        
            $this->setDecorators(array(
            'FormElements',
            array('HtmlTag',array(
                'tag' => 'ul',
                'data-role' => 'listview',
                'data-inset' => 'true'
            )),
            'Form',
            ));
        }
    }
    
    
    public function siteIsMobile()
    {
        if ($this->_siteIsMobile === NULL)
        {
            $session = new Zend_Session_Namespace('mylife_device_info');
            $this->_siteIsMobile = ($session->deviceType ===
                    Application_Controller_Plugin_MobileInit::DEVICE_TYPE_MOBILE);
        }
        return $this->_siteIsMobile;
        
    }
    
    public function bindToModel($model)
    {
        $model->setForm($this);
        $this->setModel($model);
    }
    
    public function setModel($model)
    {
        $this->_model = $model;
    }
    
    public function getModel()
    {
        return $this->model;
    }

}
