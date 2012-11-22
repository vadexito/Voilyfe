<?php

class Application_View_Helper_MobilePage extends Zend_View_Helper_Abstract
{
    public function mobilePage()
    {
        return $this;
    }
    
    public function renderHeader($buttonLeft,$buttonRight,$title)
    {
        $header = '<div data-role="header" data-position="fixed" data-id="header" >'
        .$buttonLeft
        .'<h1>'.$title.'</h1>'."\n"
        .$buttonRight
        .'</div><!-- /header -->'."\n";
        
        return $header;
    }
} 
