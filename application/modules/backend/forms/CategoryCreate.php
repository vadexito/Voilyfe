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
        $categoriesDb = $this ->_em->getRepository('ZC\Entity\Category')
                            ->findAll();
        $validUnique = new Pepit_Validate_NotInDoctrineArray($categoriesDb,'name');       
        
        $nameCategory   ->setRequired('true')
                        ->addFilters(array('StringTrim','StripTags',
                            'StringToLower','Alnum'))
                        ->addValidators(array('notempty',$validUnique))
                        ->addDecorator('label');
        
        
        
        
        $categoriesRaw = new Pepit_Form_Element_MultiCheckbox('categoryIds',array(
            'label' => 'Generalized categories (meta and simple categories): '
        ));
        
        $categories = Pepit_Form_Element::initMultioptions(
                $categoriesRaw->addDecorator('label'),
                'ZC\Entity\Category',
                'id', 'name', $this->_em
        );
        
        
       
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

