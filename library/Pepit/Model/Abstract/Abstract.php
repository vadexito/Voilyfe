<?php
/**
 * Abstract model class 
 *
 * @package    Mylife
 * @author     DM
 */
abstract class Pepit_Model_Abstract_Abstract 
{
     /**
     * @var Zend_Loader_PluginLoader
     */
    protected static $_pluginLoader;
    
    /**
     * Object for storing table
     * 
     */
    protected $_storage = null;
    
    /**
     * Table name
     * @var string
     */
    protected $_storageName = null;
    
    /**
     * load formular
     *
     * @param string $name name of the formular
     * @param array $options
     * @return Zend_Form
     */
    
    static public function loadForm($name,$options=NULL,$module=NULL)
    {
       if ($module === NULL)
       {
           $module = Zend_Controller_Front::getInstance()->getRequest()
                                                         ->getModuleName();
       }
       
       $className  = ucfirst($module).'_Form_' . ucfirst((string) $name);
        
       if ($options)
       {
           return new $className($options);
       }
       return new $className;
    }
    
    
    /**
     * get Table
     *
     * @return Storage
     */
    public function getStorage()
    {
        // initialize storate if not already done
        if (null === $this->_storage)
        {
            $this->_storage = static::loadStorage($this->_storageName);
        }
        
        // return storage (table)
        return $this->_storage;
    }
    
     /**
     * load model
     *
     * @param string $name Name of model class or asso array containing name and type
     * @return Pepit_Model_Abstract
     */
    static public function loadModel($name,array $options = NULL,$module = NULL)
    {
        if ($module === NULL)
       {
           $module = Zend_Controller_Front::getInstance()->getRequest()
                                                         ->getModuleName();
       }
        $className  = ucfirst($module) . '_Model_' . ucfirst((string) $name);
        if ($options)
        {
            return new $className($options);
        }
        return new $className();
        
    }
    
}

