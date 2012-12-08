<?php
/**
 * 
 * @author  DM
 * @package    Pepit_View
 * @subpackage Helper
 * @version    $Id: FormText.php 24750 2012-05-05 01:24:21Z adamlundrigan $
 */



class Pepit_View_Helper_FormRangePlusUnit extends Zend_View_Helper_FormElement
{
    /**
     * Generates a 'range' element.
     *
     * @param string|array $name If a string, the element name.  If an
     * array, all other parameters are ignored, and the array elements
     * are used in place of added parameters.
     *
     * @param mixed $value The element value.
     *
     * @param array $attribs Attributes for the element tag.
     *
     * @return string The element XHTML.
     */
    public function formRangePlusUnit($name, $value = null, $attribs = null,
                                        $options = null)
    {
        $info = $this->_getInfo($name, $value, $attribs,$options);
        extract($info); // name,id,value,attribs,options
        
        $helperRange = new Pepit_View_Helper_FormRange();
        $helperRange->setView($this->view);
        $range = $helperRange->formRange($name,$value,$attribs);
        
        $helperSelect = new Zend_View_Helper_FormSelect();
        $helperSelect->setView($this->view);
        $unit = $helperSelect->formSelect($name,$value,$attribs,$options);
        
        return $range.$unit;
    }
}
