<?php

/**
 * filters DateTime to localized date using zend filter
 *  
 */


class Pepit_Filter_DateTimeToDateForm implements Zend_Filter_Interface
{
    /**
     *
     * @var Zend_Filter_NormalizedToDateForm
     */
    
    protected $_zendFilter;
    
    public function __construct($options = NULL)
    {
        if ($options === NULL)
        {
            $options = array(
                'date_format' => Zend_Date::DATE_SHORT
            );
        }
        $this->_zendFilter = new Zend_Filter_NormalizedToLocalized($options);
        
    }
    
    public function filter($value)
    {
        $normalizedDate = array(
            'day'=> $value->format('d'),
            'month'=> $value->format('m'),
            'year'=> $value->format('Y')
        );
        return $this->_zendFilter->filter($normalizedDate);
    }
}
