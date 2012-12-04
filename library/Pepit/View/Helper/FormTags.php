<?php
/**
 * Helper to generate a "location" element
 * 
 * @author  DM
 * @package    Pepit_View
 * @subpackage Helper
 * 
 */



class Pepit_View_Helper_FormTags extends Zend_View_Helper_FormElement
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
    
    public function formTags($name, $value = null, $attribs = null)
    {
        $html = '';
        
        $helper = new Zend_View_Helper_FormText();
        $helper->setView($this->view);
        
        $html .= $helper->formText($name.'[visible]','',$attribs)."\n";
         
        return $html;
    }
}
