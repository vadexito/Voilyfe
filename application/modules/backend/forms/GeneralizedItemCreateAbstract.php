<?php

/**
 * form for Item creation
 * 
 * Author : DM 
 */


    
abstract class Backend_Form_GeneralizedItemCreateAbstract extends Pepit_Form
{
    protected function _initGeneralizedItemOptions()
    {
        $associationType  = new Pepit_Form_Element_Select('associationType',array(
            'label' => 'Association Type Doctrine: '
        ));
        $associationType    ->setRequired('true')
                            ->setMultioptions(array(
            '0'                                                 => 'Single field item',
            \Doctrine\ORM\Mapping\ClassMetadata::ONE_TO_MANY    => 'One to many',
            \Doctrine\ORM\Mapping\ClassMetadata::MANY_TO_MANY   => 'Many to many',
            \Doctrine\ORM\Mapping\ClassMetadata::MANY_TO_ONE    => 'Many to one',
            \Doctrine\ORM\Mapping\ClassMetadata::ONE_TO_ONE     => 'One to one',
                        ));
        
        
        
        $formElementClass = new Pepit_Form_Element_Select('formElementClassId',array(
            'label' => 'Form Element Class: '
        ));        
        $formElementClass   ->setRequired('true');
        $formElementClass = Pepit_Form_Element::initMultioptions(
                $formElementClass,
                'ZC\Entity\FormElementClass',
                'id', 'name', $this ->getEntityManager()
        );
        
        
        
        $formElements = $this ->getEntityManager()->getRepository('ZC\Entity\FormElementClass')
                            ->findAll();
        foreach ($formElements as $formElement)
        {
            $formElementClass->addMultiOptions(
                array($formElement->id => $formElement->name)
            );
        }
                        
        
        $formRequired = new Pepit_Form_Element_Checkbox('formRequired',array(
            'label' => 'Required (form element)'
        ));
        
        $itemType = new Pepit_Form_Element_Select('itemType',array(
            'label' => 'Item Type : '
        ));        
        $itemType   ->setRequired('true')
                    ->setMultioptions(array(
                    ZC\Entity\GeneralizedItem::GENERALIZED_ITEM_ITEM_GROUP => 'item group',
                    ZC\Entity\GeneralizedItem::GENERALIZED_ITEM_SINGLE_ITEM => 'single item',
                    ));
        
        $this->addDisplayGroup(array(
            $formElementClass,
            $formRequired,
            $associationType,
            $itemType,
        ),'Generalized_Item_Options');
        $this ->getDisplayGroup('Generalized_Item_Options');
    }
    
    
    
    
    
    protected function _initSingleItemOptions()
    {
        $typeSQL = new Pepit_Form_Element_Select('typeSQL',array(
            'label' => 'Type SQL (for single parameter not association): '
        ));
        $typeSQL        ->setRequired(false)
                        ->setMultioptions(array(
                            'string'                => 'string',
                            'integer'               => 'integer',
                            'float'                 => 'float',
                            'datetime'              => 'datetime',
                        ));
        
        $sizeSQL = new Pepit_Form_Element_Text('sizeSQL',array(
            'label' =>'Size SQL : '
        ));
        $sizeSQL  ->addFilters(array('StringTrim','StripTags'))
                  ->addValidators(array('Int'));
                        
        $nullableSQL = new Pepit_Form_Element_Checkbox('nullableSQL',array(
            'label' => 'Nullable SQL: '
        ));
        $nullableSQL->setRequired(false);
        
        
        $formLabel = new Pepit_Form_Element_Text('formLabel',array(
            'label' => 'Label: '
        ));
        $formLabel      ->setRequired('true')
                        ->addFilters(array('StringTrim'));
        
        
        //optional additionnal options for item
        $formMultioptions = new Pepit_Form_Element_Text('formMultioptions',array(
            'label' => 'Multioptions(if needed), to be separated by commas: '
        ));
        $formMultioptions   ->addFilters(array('StringTrim'));
        
        
        $formFilters = new Pepit_Form_Element_MultiCheckbox('formFilterIds',array(
            'label' => 'Filters: '
        ));
        $formFilters = Pepit_Form_Element::initMultioptions(
                $formFilters,
                'ZC\Entity\FormFilter',
                'id', 'name', $this ->getEntityManager()
        );
                        
        
        $formValidators = new Pepit_Form_Element_MultiCheckbox('formValidatorIds',array(
            'label' => 'Validators:'
        ));
        $formValidators = Pepit_Form_Element::initMultioptions(
                $formValidators,
                'ZC\Entity\FormValidator',
                'id', 'name', $this ->getEntityManager()
        );
                        
        $this->addDisplayGroup(array(
            $typeSQL->setAttrib('data-property-name','typeSQL'),
            $sizeSQL->setAttrib('data-property-name','sizeSQL'),
            $nullableSQL->setAttrib('data-property-name','nullableSQL'),
            $formLabel->setAttrib('data-property-name','formLabel'),
            $formMultioptions->setAttrib('data-property-name','formMultioptions'),
            $formFilters->setAttrib('data-property-name','formFilter'),
            $formValidators->setAttrib('data-property-name','formValidators'),
        ),'SingleFieldOptions');
        $this ->getDisplayGroup('SingleFieldOptions');
              
    }
    
    protected function _initItemsOptions()
    {
        $generalizedItems = (new Backend_Form_Elements_MultiCheckbox('itemIds',array(
            'label' => 'Items: ',
            'storageEntity'         => 'ZC\Entity\GeneralizedItem',
            'storageEntityProperty' => 'name',
        )))->setValue(array('date' => 'date'))
           ->setAttrib('data-property-name', 'items');

        
        $this->addDisplayGroup(array(
            $generalizedItems,
        ),'Category_Options');
        $this ->getDisplayGroup('Category_Options');
    }
    
}

