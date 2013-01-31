<?php
/**
 * form element for item
 *
 * @author DM
 */

class Events_Form_Elements_Location extends Events_Form_Elements_Abstract_Tags
{

    public $helper = 'formLocation';
    
    protected $_containerId = 15;
    protected $_containerType = 'location';
    protected $_itemName = 'location';
    
    
    public function init()
    {
        $this->setAttrib('class','item_common')
            ->setAttrib('data-item-name','location')
            ->setAttrib('data-property-name','location')
            ->setLabel('item_location')
            ->setRequired(false)
            ->setAttrib(Pepit_Form_Element::DATA_ITEM_ATTRIB,'location')
            ->addClass('event_item')
            ->setAttrib('itemId',$this->_containerId)
            ->setTagEntity('ZC\Entity\Location')
            ->setMultiTag(false)
            ->setTagIdProperty('address')
            ->setModel(Pepit_Model_Doctrine2::loadModel('itemRows'));
        
        parent::init();
        
    }
    
    
    public function mapElement($entity)
    {
        $property = $this->getAttrib('data-property-name');
        $formValue = $this->getValue();
        
        
        if (!is_array($formValue) && ($formValue !== NULL))
        {
            throw new Pepit_Form_Exception('The element '.$this->getName().' to map should be an array');
        }
        $tagElement = $formValue[0];
        if (!is_array($tagElement) && ($tagElement !== NULL))
        {
            throw new Pepit_Form_Exception('The sub element of '.$this->getName().' to map should be an array');
        }
        elseif ($tagElement !== NULL && !array_key_exists('new',$tagElement) && (!array_key_exists('id',$tagElement)))
        {
            throw new Pepit_Form_Exception('The sub element of '
                    .$this->getName()
                    .' to map should be an array with new or id as unique key');
        }
        
        if (array_key_exists('new',$tagElement))
        {
            $location = new $this->_tagEntity;
            $location->address = $tagElement['new']['value'];
            $location->latitude = $formValue['latitude'];
            $location->longitude = $formValue['longitude'];
            $location->member = $this->getEntityManager()
                                     ->getRepository('ZC\Entity\Member')
                                     ->find(Zend_Auth::getInstance()->getIdentity()->id);
            
            $this->getEntityManager()->persist($location);
            $tag = $location;
            
        }
        //if the element is already in the database, we use its id
        elseif (array_key_exists('id',$tagElement))
        {
            $tag = $this->getEntityManager()->getRepository($this->_tagEntity)
                        ->find($tagElement['id']);
        }
        
        $entity->$property = $tag;
    }
    
    public function populate($entity)
    {
        $populate = parent::populate($entity);
        
        if (property_exists($entity,'location'))
        {
            $populate['latitude'] = $entity->location->latitude;
            $populate['longitude'] = $entity->location->longitude;
        }
        return $populate;
    }
}

