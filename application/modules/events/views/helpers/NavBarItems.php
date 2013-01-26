<?php

class Events_View_Helper_NavBarItems extends Zend_View_Helper_Abstract
{
    public function navBarItems(array $formElements,$activeItem,$nbItemPerNavBar = 5)
    {
        $content = $navBar = '';
        $i=0;
        
        foreach ($formElements as $formElement)
        {
            $active = null;
            $propertyName = $formElement->getAttrib('data-property-name');
            $item = preg_replace('#.*_(.*)$#','$1',$propertyName);
            
            //mark the item active
            if ( $propertyName === $activeItem)
            {
                $active = true;
            }
            
            //add an item in the navbar line
            $navBar = $navBar . $this->_renderButton($propertyName,$item,$active);
            
            //save line and add new line of items
            if (($i % $nbItemPerNavBar) === $nbItemPerNavBar-1)
            {
                $content = $content.$this->_renderNavBar($navBar)."\n";
                $navBar = '';
            }
            $i++;
        }
        if ($navBar)
        {
            $content = $content.$this->_renderNavBar($navBar)."\n";
        }
        
        return $content;
        
    }
    
    protected function _renderNavBar($content)
    {
        return '<div data-role="navbar" class="nav-glyphish">
                    <ul>'.$content."\n"
                    .'</ul>
                </div><!-- /navbar -->';
    }
    
    
    
    protected function _renderButton($property,$item,$active=NULL)
    {
        $class = 'button-item';
        if ($active)
        {
            $class .= ' ui-btn-active ui-state-persist ';
        }
        
        return '<li>
                    <a class="'. $class . '" data-transition="none" data-prefetch data-ajax="true" href="#'
                    . $property.'_page'.'" data-icon="'
                    . $this->view->getIconItem($property,$item).'"></a>
                </li>';
    }
    
    
}
