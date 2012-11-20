<?php

/**
 * Validator to verify the username is not already taken
 * 
 * @author     DM
 */
class Pepit_Validate_MaxElementInStorage extends Zend_Validate_Abstract
{
    /**
     * Constant for error
     */
    
    const LIMIT_EXCEEDED = "limitExceeded";
    
     /**
     * @var array
     */
    
    protected $_messageTemplates = array(
        self::LIMIT_EXCEEDED => " You exceed the limit of different items authorized"
    );
    
    /**
     * Maximum number of element possible
     *
     * @var mixed
     */
    protected $_max;
    
    /**
     * Storage containing the elements
     *
     * @var mixed
     */
    protected $_modelStorage;
    
    

    /**
     * Sets validator options
     *
     * @param  mixed|Zend_Config $max
     * @param  Pepit_Model_Abstract model storage 
     * @return void
     */
    public function __construct($max,Pepit_Model_Abstract $modelStorage)
    {
        //initialize max
        if ($max instanceof Zend_Config) 
        {
            $max = $max->toArray();
        }

        if (is_array($max))
        {
            if (array_key_exists('max', $max))
            {
                $max = $max['max'];
            } 
            else
            {
                throw new Zend_Validate_Exception("Missing option 'max'");
            }
        }

        $this->setMax($max);
        
        // initialize storage
        $this->_modelStorage = $modelStorage;
    }

    /**
     * Returns the min option
     *
     * @return mixed
     */
    public function getMax()
    {
        return $this->_max;
    }

    /**
     * Sets the max option
     *
     * @param  mixed $max
     * @return Pepit_Validate_MaxElementInStorage
     */
    public function setMax($max)
    {
        $this->_max = $max;
        return $this;
    }
    
    
    /**
     * check that with the new element the limit number in storage is not exceeded
     *
     * @param  mixed $value
     * @param  array $context
     * @return boolean
     */
    
    public function isValid($name,$context=NULL) 
    {
        // get number of element already in the storage for user
        $numberElement = count($this->_modelStorage
                                        ->fetchEntriesByUser()->toArray());
        
        if ($numberElement < $this->getMax())
        {
            return true;
        }
        
        //if it exceeds limit, show error
        $this->_error(self::LIMIT_EXCEEDED);
        return false;
    }
}

