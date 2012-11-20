<?php

/**
 * form for correspondance setting bewteen google places and categories
 * 
 * Author : DM 
 */

class Backend_Form_GooglePlaces extends Pepit_Form
{
    
    protected $_config;
    
    public function init()
    {
        parent::init();
        $this->setMethod('post');
        
        $this->_config = new Zend_Config_Xml(
                APPLICATION_PATH.'/modules/backend/Data/GoogleAPI/GoogleMaps/Type2Category.xml'
        );
        
        $categories = Zend_Registry::get('entitymanager')->getRepository('ZC\Entity\Category')
                            ->findAll();
        
        foreach ($this->_config->googleTypes->googleType as $type)
        {
            $categoryCorrespondance = new Pepit_Form_Element_Select($type,array(
                'label' => 'type: '.$type,
                'multiOptions' => array(null => 'Please choose a category')
            ));
            foreach ($categories as $category)
            {
                //only single category
                if ($category->items->count()>0)
                {
                     $categoryCorrespondance->addMultiOption($category->id,$category->name);
                }
               
            }
            $categoryCorrespondance->addDecorator('label');
            
            $this->addElement($categoryCorrespondance);
        }
        
        
        $submit = new Pepit_Form_Element_Submit('submit',array(
            'label' => 'Update'
        ));
        
        $this->addElement($submit);
        
    }
}

