<?php

/**
 * form for Item creation
 * 
 * Author : DM 
 */

class Backend_Form_ItemCreate extends Backend_Form_GeneralizedItemCreateAbstract 
{
    public function init()
    {
        parent::init();
        
        //set general parameter of form
        $this->setMethod('post');
        
        $name = new Pepit_Form_Element_Text('name',array(
            'label' => 'Name (small letter only): '
        ));
        
        //define validator for uniqueness
        $generalizedItems = $this ->_em->getRepository('ZC\Entity\GeneralizedItem')
                            ->findAll();
        $validUnique = new Pepit_Validate_NotInDoctrineArray($generalizedItems,'name');       
        $name           ->setRequired('true')
                        ->setAttrib('data-property-name','name')
                        ->addFilters(array('StringTrim','StripTags',
                            'StringToLower','Alnum'))
                        ->addValidators(array('notempty',$validUnique));
        $this->addElement($name);
        
        $this->_initGeneralizedItemOptions();
        $this->_initSingleItemOptions();
        
        $itemType = $this->getElement('itemType');
        $itemType ->setMultioptions(array(
                    ZC\Entity\GeneralizedItem::GENERALIZED_ITEM_SINGLE_ITEM => 'single item',
                    ));
        
        //create submit element        
        $submit = new Pepit_Form_Element_Submit('submit_insert');
        $submit->setLabel('action_create');
        $this->addElement($submit);
                
    }
    
    
    
}

