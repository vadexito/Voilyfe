<?php

/**
 *
 * init model
 * 
 * @package Mylife
 * @author DM 
 */

class Backend_Model_GoogleAPI
{
    /**
     *
     * @var Doctrine\ORM\EntityManager $_em
     */
    protected $_em;
    
    protected $_storagePath;
    
    protected $_storageForWriting;
    
    protected $_storageForReading;
    
    public function __construct()
    {
        $this->_em = Zend_Registry::get('entitymanager');
        
        $this->_storagePath = 
            APPLICATION_PATH.'/modules/backend/Data/'
            .'GoogleAPI/GoogleMaps/Type2Category.xml';
        
        $this->_storageForWriting = new Zend_Config_Xml(
            $this->_storagePath,
            null,
            array('skipExtends'        => true,
                'allowModifications' => true)
        );
        
        $this->_storageForReading = new Zend_Config_Xml(
            $this->_storagePath
        );
    }
    
    public function getEntityManager()
    {
        return $this->_em;
    }
    
    public function initGoogleTypes()
    {
        $config = $this->_storageForWriting;
        // add googletypes
        $config->googleTypes->googleType = $this->_getGoogleTypes();
        
        // Write the config file
        $writer = new Zend_Config_Writer_Xml(array(
            'config'   => $config,
            'filename' => $this->_storagePath
        ));
        $writer->write();
        Backend_Model_Init::addEncodingToXmlFile($this->_storagePath,'UTF-8');
    }
    
    protected function _getGoogleTypes()
    {
        $types = array();
        
        $client = new Zend_Http_Client();
        $client->setUri('https://developers.google.com/places/documentation/supported_types?hl=fr');
        $page = new Zend_Dom_Query($client->request()->getBody());
        $tables = $page->query('table.columns');
        
        foreach ($tables as $table)
        { 
            foreach ($table->childNodes as $node)
            {
                $types[] = $node->nodeValue;
            }
            //we take only the first table
            break;
        }
        
        return $types;
    }
    
    public function createCorrespondances($tableCorrespondances)
    {
        $correspondances = array();
        foreach ($tableCorrespondances as $googleType => $categoryId)
        {
            $correspondances[] = array(
                'googleType' => $googleType,
                'categoryId' => $categoryId
            );
        }
        
        $config = $this->_storageForWriting;
        
        // add googletypes
        $config->correspondances->correspondance = $correspondances;
        
        // Write the config file
        $writer = new Zend_Config_Writer_Xml(array(
            'config'   => $config,
            'filename' => $this->_storagePath
        ));
        $writer->write();
        Backend_Model_Init::addEncodingToXmlFile($this->_storagePath,'UTF-8');
    }
    
    public function getCategoryIdFromType($googleType)
    {
        $correspondances = $this->_storageForReading
                                ->correspondances->correspondance
                                ->toArray();
        
        $categoryId = NULL;
        foreach ($correspondances as $correspondance)
        {
            if ($correspondance['googleType'] == $googleType)
            {
                $categoryId = $correspondance['categoryId'];
                break;
            }
        }
        
        return $categoryId;
    }
}