<?php

class Access_View_Helper_Slide extends Zend_View_Helper_Abstract
{
    
    public function slide($src,$divClass,$message)
    {
       return $this->render($src,$divClass,$message);
    }
    
    public function render($src,$divClass,$message)
    {
        return "<div data-src=\"$src\">
            <div class=\"$divClass\">
                $message
            </div>
        </div>\n";
    }
    
}
