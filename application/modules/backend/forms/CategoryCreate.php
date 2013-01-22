<?php

/**
 * form for Event creation
 * 
 * Author : DM 
 */

class Backend_Form_CategoryCreate extends Backend_Form_GeneralizedItemCreateAbstract
{
    public function init()
    {
        parent::init();
        
        //set general parameter of form
        $this->setMethod('post');
        
        $nameCategory = new Pepit_Form_Element_Text('name',array(
            'label' => 'Name Category (small letter only): '
        ));
        
        //define validator for uniqueness
        $categoriesDb = $this ->getEntityManager()->getRepository('ZC\Entity\Category')
                            ->findAll();
        $validUnique = new Pepit_Validate_NotInDoctrineArray($categoriesDb,'name');       
        
        $nameCategory   ->setRequired('true')
                        ->addFilters(array('StringTrim','StripTags',
                            'StringToLower','Alnum'))
                        ->addValidators(array('notempty',$validUnique))
                        ->addDecorator('label')->setAttrib('data-property-name','name');
        
        
        $categories = (new Backend_Form_Elements_MultiCheckbox('categoryIds',array(
            'label'                 => 'Generalized categories (meta and simple categories): ',
            'storageEntity'         => 'ZC\Entity\Category',
            'storageEntityProperty' => 'name',
        )))->setAttrib('data-property-name', 'categories');
       
        //create submit element        
        $submit = new Pepit_Form_Element_Submit('submit_insert',array(
            'label' => 'action_create'
        ));
        
        $this->addElement($nameCategory);
        $this->_initItemsOptions();
        $this->addElement($categories);
        $this->addElement($submit);
        
        
    }
}

