<?php

class Application_View_Helper_Popups extends Zend_View_Helper_HtmlElement
{
    public function popups()
    {
        if (!property_exists($this->view,'popups'))
        {
            return '';
        }
        $popups = $this->view->popups;
        $html = '';
        if (is_array($popups) & ($popups))
        {
            foreach ($popups as $popup)
            {
                if (!key_exists('attribs',$popup) && is_array($popup['attribs']))
                {
                    throw new Pepit_View_Exception('Popup should have attributes array');
                }
                if (!key_exists('id',$popup['attribs']))
                {
                    throw new Pepit_View_Exception('Popup should have an id');
                }

                $attribs['data-rel'] = 'popup';
                $html .= '<div'. $this->_htmlAttribs($attribs).'>'
                        . $popup['content']
                        . '</div>';
            }
        }
        
        
        return $html;
        
    }


        
} 
