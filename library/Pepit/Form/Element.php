<?php

class Pepit_Form_Element
{
    
    const DATA_ITEM_ATTRIB = 'data-property-name';
    
    static protected $_decoratorsDefault = array(
            'ErrorClass',
            'ViewHelper',
            'Errors',
            array('Description',array('tag' => 'p', 'class' => 'description')),
        );
   
    static protected $_decoratorsDefaultHorizontal = array(
            'ErrorClass',
            'ViewHelper',
            'Errors',
            array('Description',array('tag' => 'p', 'class' => 'description')),
            array(array('control'=> 'HtmlTag'),array('tag' => 'div','class' => 'controls')),
            array('label',array('class' => 'control-label')),
            array(array('controlGroup'=> 'HtmlTag'),array('tag' => 'div','class' => 'control-group'))
        );
   
   
    static protected $_decoratorsMobile = array(
            'ErrorClass',
            'ViewHelper',
            'Errors',
            array('Description',array('tag' => 'p', 'class' => 'description')),
            array('Label',array('escape'=>false,'requiredSuffix' => '<sup class="required">*</sup>')),
            array('HtmlTag',array('tag' => 'li','data-role' => 'fieldcontain','class'=> 'ui-hide-label')),
        );
   
    static protected $_decoratorsMobileHorizontal = array(
            'ErrorClass',
            'ViewHelper',
            'Errors',
            array('Description',array('tag' => 'p', 'class' => 'description')),
            //array('Label',array('escape'=>false,'requiredSuffix' => '<sup class="required">*</sup>')),
            //array('HtmlTag',array('tag' => 'li','data-role' => 'fieldcontain')),
        );
    
    static protected $_htmlAttribs = array();
    
    static protected $_htmlAttribsMobile = array(
        'data-theme' => 'd'
    );
    
    /**
     *
     * @param Zend_Form_Element $formElement
     * @param string $entityName
     * @param string $field1
     * @param string $field2
     * @param type $em
     * @return $formElement or false if no repository exists 
     */
    static public function initMultioptions($formElement,$entityName,
                                            $field1,$field2,$em)
    {
        if ($em->getRepository($entityName))
        {
            $options = $em->getRepository($entityName)->findAll();
            foreach ($options as $option)
            {
                $formElement->addMultioption(
                    $option->$field1,
                    $formElement->getTranslator()->translate($option->$field2)
                );
            }
        }
        return $formElement;
    }
    
    static public function initFormElement($formElement)
    {
        self::initErrorHelper($formElement);
        self::initDecoratorPath($formElement);
        $formElement->setAttribs(self::getHtmlAttribs());
        self::initDecorators($formElement);
    }
    
    static public function getHtmlAttribs()
    {
        $session = new Zend_Session_Namespace('mylife_device_info');
        if ($session->deviceType ===
                Application_Controller_Plugin_MobileInit::DEVICE_TYPE_MOBILE)
        {
            return self::$_htmlAttribsMobile;
        }
        else
        {
            return self::$_htmlAttribs;
        }
    }
    
    static public function initDecorators($formElement)
    {
        if (method_exists($formElement,'getHorizontal') && $formElement->getHorizontal())
        {
            $formElement->setDecorators(self::getDecoratorsHorizontal());
        }
        else
        {
            $formElement ->setDecorators(self::getDecorators());
        }
        
    }
    
    
    static public function getDecoratorsHorizontal()
    {
        $session = new Zend_Session_Namespace('mylife_device_info');
        if ($session->deviceType ===
                Application_Controller_Plugin_MobileInit::DEVICE_TYPE_MOBILE)
        {
            return self::$_decoratorsMobileHorizontal;
        }
        else
        {
            return self::$_decoratorsDefaultHorizontal;
        }
    }
    
    static public function getDecorators()
    {
        $session = new Zend_Session_Namespace('mylife_device_info');
        if ($session->deviceType ===
                Application_Controller_Plugin_MobileInit::DEVICE_TYPE_MOBILE)
        {
            return self::$_decoratorsMobile;
        }
        else
        {
            return self::$_decoratorsDefault;
        }
    }
    
    static public function initDecoratorPath($formElement)
    {
        $formElement->addPrefixPath(
            'Pepit_Form_Decorator',
            'Pepit/Form/Decorator',
            'decorator'
        );
    }
    
    static public function initErrorHelper($formElement)
    {
        $errorHelper = $formElement->getView()->getHelper('formErrors');
        $errorHelper->setElementStart('<span class="help-inline">');
        $errorHelper->setElementEnd('</span>');  
    }
}
