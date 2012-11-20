<?php

/*
* @author DM
* 
*/

class Pepit_Form extends Zend_Form
{
    use Pepit_Doctrine_Trait;
    
    protected $_model;
    
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
        
        
        $session = new Zend_Session_Namespace('mylife_device_info');
        if ($session->deviceType ===
                Application_Controller_Plugin_MobileInit::DEVICE_TYPE_MOBILE)
        {
            $this->addAttribs(array('data-ajax' => 'false'));
        
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
