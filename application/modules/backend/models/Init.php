<?php

/**
 *
 * init model
 * 
 * @package Mylife
 * @author DM 
 */

class Backend_Model_Init
{
    /**
     *
     * @var Doctrine\ORM\EntityManager $_em
     */
    protected $_em;
    
    protected $_storage;
    
    public function __construct()
    {
        $this->_em = Zend_Registry::get('entitymanager');
        
        $this->_storage = new Zend_Config_Xml(
                APPLICATION_PATH.'/modules/backend/Data/Init/InitialData.xml',
                'init'
        );
    }
    
    public function getEntityManager()
    {
        return $this->_em;
    }
    
    public function initTables(Zend_Config $config = NULL)
    {
        if ($config === NULL)
        {
            $config = $this->_storage;
        }
        try
        {
            foreach ($config->toArray() as $table)
            {
                $property = $table['property'];
                $nameFindMethod = 'findOneBy'.ucfirst($property);
                $entityName = $table['entityName'];

                foreach ($table['type']as $value)
                {
                    //check if entry is already in the table
                    if (!($this->_em
                            ->getRepository($entityName)
                            ->$nameFindMethod($value['value'])))
                    {

                        $newEntity = new $entityName;
                        $newEntity->$property = $value['value'];
                        
                        $this->_em->persist($newEntity);
                    }
                }
            }
            $this->_em->flush();
        }
        catch(Exception $e)
        {
            throw new Pepit_Model_Exception('Impossible to init because of '
                    .$e->getMessage());
        }
    }
    
    public function fetchAllTables($config = NULL)
    {
        if ($config === NULL)
        {
            $config = $this->_storage;
        }
        return array_keys($config->toArray());
    }
    
    public function initTranslationXMLFiles()
    {
        $pathdir = APPLICATION_PATH.'/translations/_XMLForPoedit/';
        $pathFileCategories = $pathdir.'categories.xml';
        $config = new Zend_Config_Xml($pathFileCategories,
                              null,
                              array('skipExtends'        => true,
                                    'allowModifications' => true));
        
        
        // Modify a value
        $config->categories = array();
        $config->itemgroups = array();
        $config->items = array();
        
        $config->categories->translate = $this->_getElementsFromDataBase(new Backend_Model_Categories(),'category','name');
        $config->itemgroups->translate =  $this->_getElementsFromDataBase(new Backend_Model_ItemGroups(),'item','name');
        $config->items->translate =  $this->_getElementsFromDataBase(new Backend_Model_Items(),'item','name');
        
        // Write the config file
        $writer = new Zend_Config_Writer_Xml(array('config'   => $config,
                                                   'filename' => $pathFileCategories));
        $writer->write();
        $this->addEncodingToXmlFile($pathFileCategories,'UTF-8');
        
    }
    
    
    protected function _getElementsFromDataBase(
            Pepit_Model_Doctrine2 $model,$nameElementInDataBase,$property)
    {
        $elementsinDataBase = $model->getStorage()->findAll();
        $elementsForTranslation = array();
        foreach ($elementsinDataBase as $element)
        {
             $elementsForTranslation[] = $nameElementInDataBase.'_'.$element->$property;
        }
        return $elementsForTranslation;
    }

    static public function addEncodingToXmlFile($file,$encoding)
    {
        $code = file_get_contents($file);
        return file_put_contents(
                $file, 
                preg_replace(
                        '#<\?xml version="1\.0"\?>#',
                        '<?xml version="1.0" encoding="'.$encoding.'" ?>',
                        $code
                )
        );
    }
    
    
}