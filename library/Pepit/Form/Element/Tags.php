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
    protected $_tagIdProperty = NULL;
    protected $_containerType; // category, itemgroup or item
    
    protected $_multiTag = false;
    
    public function init()
    {
        $this->setOptions(array(
            'class'     => 'form-element-tags',
            'horizontal'=> true,
        ));
        
        $this->_initForm();
        parent::init();
        
        $this   ->setAttrib('data-containerId', $this->_containerId)
                ->setAttrib('placeholder',$this->getTranslator()->translate('msg_press_return_to_save'))
                ->_initAutocomplete()
                ->setAttrib('data-multitag',$this->getMultiTag())
                ->setAttrib(
                    'class',
                    (($this->_containerType === 'itemGroup') ?
                        'item-group ' : 'simple-tags ')
                    .$this->getAttrib('class')
                );
    }
    
    
    public function mapElement($entity)
    {
        $property = $this->getAttrib('data-property-name');
        $formValue = $this->getValue();
        
        if ($this->getMultiTag())
        {
            $tags = new Doctrine\Common\Collections\ArrayCollection();
            if ($formValue === NULL)
            {
                $entity->$property = $tags;
                return true;
            }
        }
        else
        {
            if ($formValue === NULL)
            {
                $entity->$property = NULL;
                return true;
            }
        }
            
        if (!is_array($formValue) && ($formValue !== NULL))
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
                $entity->$property = $tag;
                return true;
            }
            $tags->add($tag);
        }
       
        $entity->$property = $tags;
        return true;
    }
    
    public function populate($entity)
    {
        $tags = parent::populate($entity);
        if ($tags)
        {
            $populatedArray = [];
            $property = $this->getTagIdProperty();
            $dataPopulate = [];

            if ($this->getMultiTag())
            {
                foreach ($tags as $tag)
                {

                    $populatedArray[] = ['id' => $tag->id ];
                    $dataPopulate[$tag->id] = $tag->$property;
                }
            }
            else
            {
                $populatedArray[] = ['id' => $tags->id ];
                $dataPopulate[$tags->id] = $tags->$property;
            }

            $this->setAttrib('data-populate',  Zend_Json::encode($dataPopulate));
            return $populatedArray;
        }
        
        return $tags;
        
    }
    
    public function setTagIdProperty($property = NULL)
    {
        if ($property)
        {
            $this->_tagIdProperty = $property;
        }
        else if (!$this->_tagIdProperty)
        {
            if ($this->_containerType === 'item')
            {
                $this->_tagIdProperty = 'value';
            }
            else if ($this->_containerType === 'itemGroup')
            {
                $this->_tagIdProperty = $this->_itemName.'_name';
            }
        }
        
        return $this;
    }
    
    public function getTagIdProperty()
    {
        if ($this->_tagIdProperty === NULL)
        {
            $this->setTagIdProperty();
        }
        return $this->_tagIdProperty;
    }


    protected function _initAutocomplete()
    {
        $entities = $this->_getEntities();                 
        array_walk($entities, function (&$value,$key,$prop){
            $value = array(
                'value' => $value->id,
                'label' => $value->$prop
            );
        },$this->getTagIdProperty());
        
 
        $this->setAttrib(
                'data-autocomplete',
                Zend_Json::encode(array('data' => $entities))
        );
        
        return $this;
    }
    
    
    public function dataChart($events)
    {
                
        $tags = [];
        $property = $this->getAttrib('data-property-name');
        
        $propertyTag = $this->_containerType === 'itemGroup' ? 
            $this->_itemName.'_name' : 'value';
        
        foreach ($events as $event)
        {
            if ($event->$property)
            {
                if (!method_exists($event->$property,'count'))
                {
                    $tags[] = $event->$property->$propertyTag;
                }
                else
                {
                    foreach ($event->$property as $tag)
                    {
                        $tags[] = $tag->$propertyTag;
                    }
                }
            }
            
        }
        $entities = array_count_values($tags);
        arsort($entities);
      
        return [
            'type'  =>'winner_list',
            'title' => ucfirst($this->getLabel()),
            'values'=> $entities
        ];
        
    }
    
    protected function _getEntities()
    {
        $repository = $this->getEntityManager()
                                    ->getRepository($this->_tagEntity);
        if ($this->_containerType === 'itemGroup')
        {
            $entities = $repository->findAll();
        }
        else
        {
            $entities = $repository->findItemRowsByItemId($this->_containerId);
        }
        
        return $entities;
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
