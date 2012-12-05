<?php

class Pepit_View_Helper_HtmlMobileButtonNavBar extends Zend_View_Helper_HtmlElement
{
    
    protected $_position = 'right';
    
    const TYPE_BUTTON_BACK = 1;
    const TYPE_BUTTON_ADD_NEW_EVENT = 2;
    const TYPE_BUTTON_NONE = 3;


    public function htmlMobileButtonNavBar($options,$attribs = [],$content='')
    {
        if (array_key_exists('position',$options))
        {
            $this->_position = $options['position'];
        }
        
        $attribs = array_merge($attribs,[
            'data-theme'    => 'b',
            'data-ajax'     => 'false',
            'class'         => 'ui-btn-'.$this->getPosition(),
        ]);
        
        if (array_key_exists('type',$options))
        {
            switch($options['type'])
            {
                case self::TYPE_BUTTON_ADD_NEW_EVENT :
                    $attribs['data-iconpos'] = 'notext';
                    $attribs['data-icon'] = 'plus';
                    $content = '';
                    break;
                case self::TYPE_BUTTON_BACK :
                    $attribs['data-rel'] = 'back';
                    $attribs['data-icon'] = 'arrow-l';
                    break;
                    
                case self::TYPE_BUTTON_NONE :
                return ;
            }
        }
        
        $xhtml = '<a' . $this->_htmlAttribs($attribs) . '>' . self::EOL
                     . ($content ? $content . self::EOL : '')
                     . '</a>';

        return $xhtml;
    }
    
    public function getPosition()
    {
        return $this->_position;
    }
    
}

   