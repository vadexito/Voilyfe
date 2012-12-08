<?php

/**
 * Class model for Events
 *
 * @author     DM
 */

class Events_Model_ItemRows extends Events_Model_Abstract_GeneralizedItemRowsAbstract
{
    
    protected $_formClasses = array(
        'insert' => 'itemRowCreate',
        'update' => 'itemRowUpdate',
        'delete' => 'itemRowDelete'
    );
    
     /**
     * entity name
     * @var string
     */
    protected $_storageName = 'ZC\Entity\ItemRow';
    
    
     public function createEntityFromForm()
    {
        //get container
        $item = $this->getEntityManager()
                         ->getRepository('ZC\Entity\Item')
                         ->find($this->getForm()->getValue('itemId'));
        
        
        //create new itemrow
        $classItemRow = 
                Backend_Model_Items::getRowContainerEntityName(
                    $item->name
            );
                        
        $itemRow = new $classItemRow();
        $itemRow->item = $item;
        $itemRow->creationDate = new \DateTime();
        $this->addMember($itemRow);
                               
        return $this->_saveEntityFromForm($itemRow);
    }
    
    
    public function _saveEntityFromForm($entity)
    {
        $entity->modificationDate = new \DateTime();
        $this->getForm()->removeElement('itemId');
        
        parent::_saveEntityFromForm($entity);
        return $entity;
    }
    
}

