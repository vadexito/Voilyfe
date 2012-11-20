<?php

/**
 *
 * interface for containers
 * 
 * @package Mylife
 * @author DM 
 */
interface Backend_Model_Interface_Container 
{

    
    static public function getRowContainerEntityName($name);
    
    static public function getNameSpaceForContainer();
    
    static public function getParentClassContainer($NoInitialSlash=true);
    
    static public function getContainerForRowsPath($name);
    
    static public function getRowContainerShortEntityName($name);
    
    static public function getContainerRowModel();
    
    
}

