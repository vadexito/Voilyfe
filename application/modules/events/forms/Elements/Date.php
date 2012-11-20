<?php
/**
 * form element for item
 *
 * @author DM
 */


class Events_Form_Elements_Date extends Pepit_Form_Element_Date
{

    protected $_em = null;
    
    
    public function init()
    {
        parent::init();
        
        $today = new Zend_Date();
        $this   ->setAttrib('class','item_common')
                ->setAttrib('data-item-name','date')
                ->setAttrib('data-property-name','date')
                ->setLabel('item_date')
                ->setValue($today->toString(Pepit_Date::MYSQL_DATE))
                ->setRequired(true)
                ->setAttrib(Pepit_Form_Element::DATA_ITEM_ATTRIB,'date');
                
        
    }
    
    
    
    public function getEntityManager()
    {
        if ($this->_em)
        {
            return Zend_Registry::get('entitymanager');
        }
        return $this->_em;
        
    }
}

