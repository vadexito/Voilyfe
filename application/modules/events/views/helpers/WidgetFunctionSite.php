<?php

class Events_View_Helper_WidgetFunctionSite extends Zend_View_Helper_HtmlElement
{
    protected $_class = NULL;
    
    use Pepit_Http_UserAgent_Trait;
    
    public function widgetFunctionSite($options)
    {
        extract($options);
        $this->setClass();
        
        return '<div data-content-theme="c" data-collapsed="false" data-role="collapsible" data-collapsed="true" data-theme="a" class="'.$this->getClass().'">'
               .'<h5 class="title" >'.$title.'</h5>'
               .'<img class="img-widget-help" src="'. $img .'"/>'
               .'<span class="btn-explanation">'.$visualFunction.'</span>'
               .'<p><h4 class="subtitle">'.$subtitle.'</h4>'
               .'<p>'.$content.'</p></p></div>';
    }
    
    public function getClass()
    {
        return $this->_class;
    }
    
    public function setClass()
    {
        if ($this->_class === NULL)
        {
            if ($this->siteIsMobile())
            {
                $this->_class = 'widget-function-site';
            }
        }
    }
}
