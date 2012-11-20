<?php

class Events_View_Helper_GetIconItem extends Zend_View_Helper_Abstract
{
    protected $_namespace = 'mylife-';
    
    public function getIconItem($property,$item)
    {
        $path = Zend_Registry::get('config')->public
                                            ->images->icon->item->inline->path;
        $completePath = APPLICATION_PATH.'/../public/'.$path;
        
        //try with specific name
        if (file_exists(sprintf($completePath,$property)))
        {
            $icon = $property;
        }
        else if (file_exists(sprintf($completePath,$item)))
        {
            $icon = $item;
        }
        else
        {
            return '';
        }
        
        return $this->_namespace.$icon;
    }
      
}
