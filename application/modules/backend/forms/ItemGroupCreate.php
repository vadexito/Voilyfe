<?php

/**
 * form for Item creation
 * 
 * Author : DM 
 */

class Backend_Form_ItemGroupCreate extends Backend_Form_GeneralizedItemCreateAbstract 
{
    public function init()
    {
        parent::init();

        //set general parameter of form
        $this->setMethod('post');
        
        $name = new Pepit_Form_Element_Text('name',array(
            'label' => 'Name Group Item (small letter only): '
        ));
        
        //define validator for uniqueness
        $generalizedItems = $this ->getEntityManager()->getRepository('ZC\Entity\GeneralizedItem')
                            ->findAll();
        $validUnique = new Pepit_Validate_NotInDoctrineArray($generalizedItems,'name');       
               
        
        $name           ->setRequired('true')
                        ->addFilters(array('StringTrim','StripTags',
                            'StringToLower','Alnum'))
                        ->addValidators(array('notempty',$validUnique));
        $this->addElement($name);
        
        $this->_initGeneralizedItemOptions();
        $this->_initItemsOptions();
        
        $items = $this->getElement('itemIds');
        $items->setRequired(true);
        
        $associationTypes = $this->getElement('associationType')->getMultioptions();
        unset($associationTypes['0']);
        $this->getElement('associationType')->setMultioptions($associationTypes);
        
        $formElementClass = 'Pepit_Form_Element_Select';
        $idFormElementClass = $this ->getEntityManager()->getRepository('ZC\Entity\FormElementClass')
                ->findOneByName($formElementClass)->id;

        $this->getElement('formElementClassId')->setMultioptions(array(
                $idFormElementClass => $formElementClass
        ));
       
        
        
        $identifierItemId = new Pepit_Form_Element_Select('identifierItemId',array(
            'label' => 'Identifier Item : '
        ));
        $identifierItemId ->setRequired('true');
        $identifierItemId = Pepit_Form_Element::initMultioptions(
                $identifierItemId,
                'ZC\Entity\Item',
                'id', 'name',$this ->getEntityManager()
        );
        $itemType = $this->getElement('itemType');
        $itemType->setMultioptions(array(
                    ZC\Entity\GeneralizedItem::GENERALIZED_ITEM_ITEM_GROUP => 'item group',
                    ));
        
        $this->addElement($identifierItemId);
        
        //create submit element        
        $submit = new Zend_Form_Element_Submit('submit_insert',array(
            'label' => 'action_create'
        ));
        $this->addElement($submit);
                
    }
}

