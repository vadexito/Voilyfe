<?php

/**
 * Validator to verify the userName is not already taken
 * 
 * @author     DM
 */
class Pepit_Validate_UserUniqueName extends Zend_Validate_Abstract
{
    protected $em;
    
    
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
    
    public function __construct($em)
    {
        $this->em = $em;
    }

        
    /**
     * compare the given name with the database
     *
     * @param  mixed $value
     * @param  array $context
     * @return boolean
     */
    
    public function isValid($userName,$context=NULL) 
    {
        // prepare value to compare
        $userName = (string)$userName;
        
        //alows using value for error writing with %value%
        $this->_setValue($userName);
        
        // try finding user with input name
        $row = $this->em
                ->getRepository('ZC\Entity\Member')
                ->findOneByUserName($userName);
        
        //check if user found
        if (!$row)
        {
            return true;
        }
        
        //if there is a user found, transmit error
        $this->_error(self::NOT_UNIQUE);
        return false;
    }
}

