<?php

class Layout_View_Helper_ButtonHeader extends Zend_View_Helper_Abstract
{
    /**
     *
     * @param string $type ('back' or other)
     * @param type $position position is equal to right or left
     * @return type 
     */
    public function buttonHeader($type,$position)
    {
        switch($type)
        {
            case 'back' :
                return '<a data-theme="b" data-rel="back" data-icon="arrow-l" class="ui-btn-'
                    .$position
                    .'">'.ucfirst($this->view->translate('menu_back_to_previous_page'))
                    .'</a>';
            case NULL:
                return '';
            default :
                return $type;
                
        }
    }
}
