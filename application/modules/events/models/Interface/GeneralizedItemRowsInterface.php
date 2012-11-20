<?php

/**
 * interface for generalizeditem rows
 *
 * @author     DM
 */

interface Events_Model_Interface_GeneralizedItemRowsInterface
{
    
    /**
     * provides the form element in a formular used to choose the element
     *  
     */
    public function getFormElement(ZC\Entity\GeneralizedItem $generalizedItem,$containerName);
    
    /**
     * provides the name of the property corresponding to a given item in the container
     */
    static public function getPropertyName($containerName,$itemName);
    
}

