<?php
/**
 * 
 * @author  DM
 * @package    Pepit_View
 * @subpackage Helper
 * @version    $Id: FormText.php 24750 2012-05-05 01:24:21Z adamlundrigan $
 */



class Pepit_View_Helper_Abstract extends Zend_View_Helper_HtmlElement
{
    protected $_possibleOptions=[];
    
    protected function _loadOptions(array $options)
    {
        foreach ($this->_possibleOptions as $option)
        {
            $prop = '_'.$option;
            if (property_exists($this,$prop) && key_exists($option, $options))
            {
                $method = 'set'.ucfirst($option);
                if (method_exists($this,$method))
                {
                    $this->$method($options[$option]);
                }
                else
                {
                    $this->$prop = $options[$option];
                }
            }
        }
    }
    
    protected function _loadDefaultOptions(){}
    
}
