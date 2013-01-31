<?php
/**
 * Helper to generate a "location" element
 * 
 * @author  DM
 * @package    Pepit_View
 * @subpackage Helper
 * 
 */



class Pepit_View_Helper_FormLocation extends Zend_View_Helper_FormElement
{
    /**
     * Generates a 'location' element.
     *
     * @access public
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
    
    public function formLocation($name, $value = null, $attribs = null)
    {
        $html = '';
        
        $helperTags = new Pepit_View_Helper_FormTags();
        $helperTags->setView($this->view);
        
        $helper = new Zend_View_Helper_FormText();
        $helper->setView($this->view);
        
        
        $address = $latitude = $longitude = '';
        
        if (is_array($value))
        {
            $address = (isset($value['address']) ? $value['address']: '');
            $latitude = (isset($value['latitude']) ? $value['latitude']: '');
            $longitude = (isset($value['longitude']) ? $value['longitude']: '');
        }
        
        $html .= $helperTags->formTags($name.'[address]',$address,$attribs);
        $html .= $helper->formText($name.'[latitude]',$latitude,array('id' => 'input_latitude'));
        $html .= $helper->formText($name.'[longitude]',$longitude,array('id' => 'input_longitude'));

        return $html;
    }
}
