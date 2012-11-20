<?php

/**
 * Class model for ItemGrouprows
 *
 * @author     DM
 */

class Events_Model_ItemGroupRows extends Events_Model_Abstract_GeneralizedItemRowsAbstract
{
    
    protected $_formClasses = array(
        'insert' => 'itemGroupRowCreate',
        'update' => 'itemGroupRowUpdate',
        'delete' => 'itemGroupRowDelete'
    );
    
     /**
     * entity name
     * @var string
     */
    protected $_storageName = 'ZC\Entity\ItemGroupRow';
    
    
    public function createEntityFromForm()
    {
        $formValues = $this->getForm()->getValues();
        
        //get container
        $itemGroup = $this->getEntityManager()
                         ->getRepository('ZC\Entity\ItemGroup')
                         ->find($formValues['itemGroupId']);
        unset($formValues['itemGroupId']);
        
        //create new itemgrouprow
        $classItemGroupRow = 
                Backend_Model_ItemGroups::getRowContainerEntityName(
                    $itemGroup->name
            );
        $itemGroupRow = new $classItemGroupRow();
        $itemGroupRow->itemGroup = $itemGroup;
        
        $itemGroupRow->creationDate = new \DateTime();
        
        //get member and associate with itemgroup
        $itemGroupRow->member = $this->getEntityManager()
                              ->getRepository('ZC\Entity\Member')
                              ->find(
                                    Zend_Auth::getInstance()->getIdentity()->id
                                );
        
        return $this->saveEntityFromForm($formValues,$itemGroupRow);
    }
    
    public function updateEntityFromForm(Array $formValues,$generalizedItemId)
    {
        $generalizedItem = $this->getStorage()->find($generalizedItemId);
        
        return $this->saveEntityFromForm($formValues,$generalizedItem);
    }
    
    
    
}

