<?php

/**
 * define an element text for crete item form
 * 
 *  
 */

class Pepit_Form_Element_Tags extends Pepit_Form_Element_Xhtml
{
    /**
     * Default form view helper to use for rendering
     * @var string
     */
    public $helper = 'formTags';
    
    protected $_model = NULL;

    protected $_tagEntity = 'ZC\Entity\ItemRow';
    
    protected $_containerId = NULL;
    protected $_itemName;
    protected $_propertyAutocomplete;
    protected $_containerType; // category, itemgroup or item
    
    
    protected $_multiTag = false;
    
    public function init()
    {
        $this->setOptions(array(
            'class'     => 'form-element-tags',
            'horizontal'=> true,
        ));
        
        $this->_initForm();
        $this->setPropertyAutocomplete();
        
        parent::init();
        
        $this   
                ->setAttrib('placeholder',$this->getTranslator()->translate('msg_press_return_to_save'))
                ->_initAutocomplete($this->_tagEntity,$this->_propertyAutocomplete)
                ->setAttrib(
                    'class',
                    (($this->_containerType === 'itemGroup') ?
                        'item-group ' : 'simple-tags ')
                    .$this->getAttrib('class')
                );
    }
    
    
    public function mapElement()
    {
        $formValue = $this->getValue();
        
        if ($this->getMultiTag())
        {
            $tags = new Doctrine\Common\Collections\ArrayCollection();
        }
        if (!is_array($formValue))
        {
            throw new Pepit_Form_Exception('The element '.$this->getName().' to map should be an array');
        }
        foreach ($formValue as $tagElement)
        {
            if (!is_array($tagElement))
            {
                throw new Pepit_Form_Exception('The sub-element of the element '.$this->getName().'to map should be an array');
            }
            //if the element is new
            if (array_key_exists('new',$tagElement))
            {
                
                if ($this->_model->getForm()->isValid($tagElement['new']))
                {
                    $tag = $this->_model
                                ->getStorage()
                                ->find($this->_model->insert());
                }
                else
                {
                    $errors = array();
                    foreach ($this->_model->getForm()->getErrors() as $key => $item)
                    {
                        if ($item)
                        {
                            $errors[]= $key.' '.implode(',',$item)."    ";
                        }
                        
                    }
                    throw new Pepit_Form_Exception('Subform not valid with error : '.implode('/',$errors));
                }
            }
            //if the element is already in the database, we use its id
            else
            {
                $tag = $this->_model->getStorage()->find($tagElement['id']);
            }
            if (!$this->getMultiTag())
            {
                return $tag;
            }
            $tags->add($tag);
        }
        
        
        return $tags;
    }
    
    public function setPropertyAutocomplete($property = NULL)
    {
        if ($property)
        {
            $this->_propertyAutocomplete = $property;
        }
        else if (!$this->_propertyAutocomplete)
        {
            if ($this->_containerType === 'item')
            {
                $this->_propertyAutocomplete = 'value';
            }
            else if ($this->_containerType === 'itemGroup')
            {
                $this->_propertyAutocomplete = $this->_itemName.'_name';
            }
        }
        
        return $this;
    }
    
    protected function _initAutocomplete($entityName,$property)
    {
        $repository = $this->getEntityManager()
                                    ->getRepository($entityName);
        if ($this->_containerType === 'itemGroup')
        {
            $entities = $repository->findAll();
        }
        else
        {
            $entities = $repository->findItemRowsByItemId($this->_containerId);
        }
                                    
        array_walk($entities, function (&$value,$key,$prop){
            $value = array(
                'value' => $value->id,
                'label' => $value->$prop
            );
        },$property);
        
 
        $this->setAttrib(
                'data-autocomplete',
                Zend_Json::encode(array('data' => $entities))
        );
        
        return $this;
    }
    
    
    
    public function setTagEntity($tagEntity)
    {
        $this->_tagEntity = $tagEntity;
        
        return $this;
    }
    
    public function getTagEntity()
    {
        return $this->_tagEntity;
    }
    
    public function setModel($model)
    {
        $this->_model = $model;
        
        return $this;
    }
    
    public function getModel()
    {
        return $this->_model;
    }
    
    public function setContainerId($id)
    {
        $this->_containerId = $id;
        
        return $this;
    }
    
    public function getContainerId()
    {
        return $this->_containerId;
    }
    
     /**
     * parameter for choosing if one of several tags
     * 
     * @param boolean $multi
     * @return \Pepit_Form_Element_TagsItemGroup
     */
    public function setMultiTag($multi)
    {
        $this->_multiTag = $multi;
        
        return $this;
    }
    
    /**
     * 
     * @return boolean
     */
    public function getMultiTag()
    {
        return $this->_multiTag;
    }
    
    protected function _initForm()
    {
        if ($this->_model)
        {
            if (($this->_containerId) && ($this->_containerType))
            {
                $form = $this->_model->getForm('insert',array(
                    'containerId'   => $this->_containerId,
                    'containerType' => $this->_containerType,
                    'model'         => $this->getModel()
                ));
                $this->getModel()->BindToForm($form);
            }
            else
            {
                throw new Pepit_Form_Exception('Property '
                    .(($this->_containerId) ? '':'containerId').' '.(($this->_containerType) ? '':'containerId').'of "'.$this->getId().'" must first be defined');
            }
            
        }
        else
        {
            throw new Pepit_Form_Exception('A model for "'.$this->getId().'" must be first defined and bound to the form');
        }
        
        return $this;
    }
    
    
}
