<?php

/**
 * Validator to check than password equals to confirm password
 *
 * 
 * @author     DM
 */

class Pepit_Validate_NotAlreadyInForUser extends Zend_Validate_Abstract
{
    /**
     * Constante for error
     */
    const MULTIPLE_VALUE = 'already_in';
    
    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::MULTIPLE_VALUE => "The item to be added is already present"
    );
    
    /**
     *
     * @param integer $userId
     */
    
    protected $_userId;
    
    protected $_dbtable;

    public function __construct($dbtable)
    {
        $this->_dbtable = $dbtable;
        
        //get userId
        $user = Zend_Auth::getInstance()->getIdentity();
        $this->_userId = $user->id;
        
    }
    
    
    public function isValid($value, $context = null)
    {
        //get and adjust whole context from form
        $context['user_id'] = $this->_userId;
        unset($context['submit']);
        
        //initialize select for checking if the value is already in the table
        $select = $this->_dbtable->select();
        foreach($context as $key => $value)
        {
            $select->where(
                    $key.' = ?',
                    $value
            );
        }
        if ($this->_dbtable->fetchRow($select))
        {
            $this->_error(self::MULTIPLE_VALUE);
            return false;
        }
        return true;
    }
}