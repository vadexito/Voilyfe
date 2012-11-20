<?php

/**
 * Validator to check than password equals to confirm password
 *
 * 
 * @author     DM
 */

class Pepit_Validate_ValidateEqualPass extends Zend_Validate_Abstract
{
    /**
     * Constante for error
     */
    const NOT_MATCH = 'notMatch';
    
    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_MATCH => "the values do not match"
    );

    /**
     * Comparison field
     *
     * @var string
     */
    protected $_compare;

    /**
     * Construction to prepare comparison field
     *
     * @param  string $compare
     * @return void
     */
    public function __construct($compare = 'compare')
    {
        $this->setCompareField($compare);
    }

    /**
     * return compared element
     *
     * @return string
     */
    public function getCompareField()
    {
        return $this->_compare;
    }

    /**
     * set compare element
     *
     * @param  string $compare
     */
    public function setCompareField($compare)
    {
        $this->_compare = $compare;
    }

    /**
     * compare given value with the one of the compare field
     *
     * @param  mixed $value : value to be checked
     * @param  array $context : content of the whole form
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        // prepare value
        $value = (string) $value;
        $this->_setValue($value);
        
        // check context
        if (is_array($context))
        {
            if (isset($context[$this->getCompareField()])
                && ($value == $context[$this->getCompareField()]))
            {
                return true;
            }
        } 
        elseif (is_string($context) && ($value == $context)) 
        {
            return true;
        }

        // the value are not equal therefore an error
        $this->_error(self::NOT_MATCH);
        return false;
    }
}