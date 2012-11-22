<?php

class Pepit_View_Helper_HtmlMobileButtonNavBar extends Zend_View_Helper_HtmlElement
{
    
    protected $_position = 'right';
    
    const TYPE_BUTTON_BACK = 1;
    const TYPE_BUTTON_ADD_NEW_EVENT = 2;


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
                    return '<a data-theme="b" data-iconpos="notext" data-icon="plus" class="ui-btn-'
                    .$this->getPosition()
                    .'" '. $this->_htmlAttribs($attribs).' ></a>';
                case self::TYPE_BUTTON_BACK :
                default :
                    return '<a data-theme="b" data-rel="back" data-icon="arrow-l" class="ui-btn-'
                    .$this->getPosition()
                    .'" '. $this->_htmlAttribs($attribs).' >'.ucfirst($this->view->translate('menu_back_to_previous_page'))
                    .'</a>';
            }
        }
        else
        {
            $xhtml = '<a' . $this->_htmlAttribs($attribs) . '>' . self::EOL
                     . ($content ? $content . self::EOL : '')
                     . '</a>';

            return $xhtml;
        }
    }
    
    public function getPosition()
    {
        return $this->_position;
    }
    
}

   