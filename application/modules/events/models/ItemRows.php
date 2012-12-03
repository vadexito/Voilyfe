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
        $formValues = $this->getForm()->getValues();

        //get container
        $item = $this->getEntityManager()
                         ->getRepository('ZC\Entity\Item')
                         ->find($formValues['itemId']);
        unset($formValues['itemId']);
        
        //create new itemrow
        $classItemRow = 
                Backend_Model_Items::getRowContainerEntityName(
                    $item->name
            );
                        
        $itemRow = new $classItemRow();
        $itemRow->item = $item;
        
        $itemRow->creationDate = new \DateTime();
        
        //get member and associate with item
        $itemRow->member = $this->getEntityManager()
                              ->getRepository('ZC\Entity\Member')
                              ->find(
                                    Zend_Auth::getInstance()->getIdentity()->id
                                );
                               
        return $this->saveEntityFromForm($formValues,$itemRow);
    }
    
    public function updateEntityFromForm($generalizedItemId)
    {
        $formValues = $this->getForm()->getValues();  
        
        $generalizedItem = $this->getStorage()->find($generalizedItemId);
        return $this->saveEntityFromForm($formValues,$generalizedItem);
    }
    
   public function saveEntityFromForm($formValues,$itemRow)
    {
        $propertyName = $this->getPropertyName('','');
        $itemRow->modificationDate = new \DateTime();
        $itemRow->$propertyName = $formValues[$propertyName];
        
        return $itemRow;
    }
    
    static public function getPropertyName($containerName,$itemName)
    {
        return 'value';
    }
    
    
}

