<?php

/**
 * define an element text for crete item form
 * 
 *  
 */

class Pepit_Form_Element_Tags extends Pepit_Form_Element_Xhtml
{
    use Pepit_Form_Element_Trait_KeywordsVisual;
    
    /**
     * Default form view helper to use for rendering
     * @var string
     */
    public $helper = 'formTags';
    
    protected $_model = NULL;

    protected $_tagEntity = 'ZC\Entity\ItemRow';
    
    protected $_containerId = NULL;
    protected $_itemName;
    protected $_containerType; // category, itemgroup or item
    
    protected $_multiTag = false;
    
    public function init()
    {
        $this->setOptions(array(
            'horizontal'=> true,
        ));
        
        $this->addClass('form-element-tags');
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
                throw new Pepit_Form_Exception('The sub-element of the element '.$this->getName().' to map should be an array');
            }
            //if the element is new
            if (array_key_exists('new',$tagElement))
            {
                $model = clone $this->_model;
                $model->bindToForm(clone $this->_model->getForm());
                
                if ($model->getForm()->isValid($tagElement['new']))
                {
                    $tag = $this->getEntityManager()->getRepository($this->_tagEntity)->find($model->insert());
                }
                else
                {
                    $errors = array();
                    foreach ($model->getForm()->getErrors() as $key => $item)
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
                $tag = $this->getEntityManager()->getRepository($this->_tagEntity)
                            ->find($tagElement['id']);
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
    
    

    /**
     * init property data-autcomplete for aucomplete operated by jquery
     * @return \Pepit_Form_Element_Tags
     */
    protected function _initAutocomplete()
    {
        $entities = $this->_getEntities();           
        array_walk($entities, function (&$value,$key,$prop){
            
            if (property_exists($value,$prop))
            {
                $value = array(
                    'value' => $value->id,
                    'label' => $value->$prop
                );
            }
            else
            {
                throw new Pepit_Form_Exception('The class '.get_class($value).' should have the property'.$prop);
            }
            
        },$this->getTagIdProperty());
        
 
        $this->setAttrib(
                'data-autocomplete',
                Zend_Json::encode(array('data' => $entities))
        );
        
        return $this;
    }
    
    protected function _getEntities()
    {
        $repository = $this->getEntityManager()
                                    ->getRepository($this->_tagEntity);
        $memberId = Zend_Auth::getInstance()->getIdentity()->id; 
        
        if ($this->_containerType === 'itemGroup' ||
            $this->_containerType === 'location' )
        {
            $entities = $repository->findAllByMemberId($memberId);
        }
        else
        {
            $entities = $repository->findAllByItemIdAndMemberId(
                    $this->_containerId,
                    $memberId
            );
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
    
    public function setContainerType($value)
    {
        $this->_containerType = $value;
        
        return $this;
    }
    
    public function getContainerType()
    {
        return $this->_containerType;
    }
    
    public function getItemName()
    {
        return $this->_itemName;
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
