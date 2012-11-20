<?php

/**
 * Validator to verify an element is not in an array
 * 
 * @author     DM
 */
class Pepit_Validate_NotInDoctrineArray extends Zend_Validate_Abstract
{
    /**
     * Constant for error
     */
    
    const NOT_UNIQUE = "notUnique";
    
     /**
     * @var array
     */
    
    protected $_messageTemplates = array(
        self::NOT_UNIQUE => "'%value%' alread exists"
    );
    
    
    /**
     * Array of entities containing the value to test
     *
     * @var mixed
     */
    protected $_array;
    
    /**
     * Field to test
     *
     * @var string
     */
    protected $_field;
    
    
    
    /**
     * initialize array
     *
     * @param  array
     * @return void
     */
    public function __construct($array,$field)
    {
        $this->_array = $array;
        $this->_field = $field;
    }

    
    /**
     * compare the given value with the values in the array
     *
     * @param  mixed $value
     * @param  array $context
     * @return boolean
     */
    
    public function isValid($name,$context=NULL) 
    {
        // prepare value to compare
        $name = (string)$name;
        
        //alows using value for error writing with %value%
        $this->_setValue($name);
        
        $field = $this->_field;
        //check if user found
        foreach ($this->_array as $row)
        {
        
            if($row->$field === $name)
            {
                //if there is a user found, transmit error
                $this->_error(self::NOT_UNIQUE);
                return FALSE;
            }
        }
        return true;
    }
}

