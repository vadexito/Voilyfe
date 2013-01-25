<?php

class Events_View_Helper_SubPageIndex extends Pepit_View_Helper_Abstract
{
    
    protected $_category;    
    protected $_categoryName = NULL;
    
    protected $_buttonRight = NULL;
    protected $_buttonLeft = NULL;
    protected $_active = '';
    protected $_content = '';
    protected $_footer = NULL;
    protected $_title = NULL;
    protected $_noWrap = NULL;
    protected $_popups = NULL;
    
    protected $_possibleOptions = ['active','content','footer','title',
            'buttonRight','buttonLeft','noWrap','popups'];
    
    public function subPageIndex($category,$id = NULL,$options = [])
    {
        
         
        if (!is_object($category))
        {
            throw new Pepit_View_Exception('A entity category has to be provided for subpageindex viewer');
        }
        $this->_category = $category;
        
        if (!$id)
        {
            throw new Pepit_View_Exception('An id  has to be provided for subpageindex viewer');
        }
        
        $this->_resetOptions();
        $this->_loadOptions($options);
        $this->_loadDefaultOptions();
        
        return $this->view->partial(
            'partial/_mobilePage.phtml', 
            [
               'id'         => $id,
               'active'     => $this->_active,
               'content'    => $this->_content,
               'title'      => $this->_title,
               'buttonLeft' => $this->_buttonLeft,
               'buttonRight'=> $this->_buttonRight,
               'footer'     => $this->_footer ,
               'noWrap'     => $this->_noWrap, 
               'popups'     => $this->_popups 
            ]
        );
    }
    
    protected function _resetOptions()
    {
        $this->_buttonRight = NULL;
        $this->_buttonLeft = NULL;
        $this->_active = '';
        $this->_content = '';
        $this->_footer = NULL;
        $this->_title = NULL;
        $this->_noWrap = NULL;
        $this->_popups = NULL;
    }
    
    protected function _loadDefaultOptions()
    {
        if (!$this->_footer)
        {
            $this->_footer = '_footerCategories.mobile';
        }
        
        $plusButtonOptions = ['position' => 'right','type' => 2];
        if ($this->_category->name !=='all')
        {
            $this->_categoryName = 'category_'.$this->_category->name;
            $hrefButtonAdd = $this->view->url(array('action' => 'create','containerId' => $this->_category->id),'event');
            $attribs = [
                'href'      => $hrefButtonAdd
            ];
        }
        else
        {
            $this->_categoryName = 'menu_events';
            $hrefButtonAdd = '#list_singleCategories';
            $attribs = [
                'href'      => $hrefButtonAdd,
                'data-rel'  => 'popup',
            ];
        }
        
        if ($this->_buttonRight === NULL)
        {
            
            
            $this->_buttonRight = $this->view->htmlMobileButtonNavBar(
                    $plusButtonOptions,
                    $attribs
            );
           
        }
        
        if (!$this->_buttonLeft)
        {
            $this->_buttonLeft = $this->view->htmlMobileButtonNavBar(['position' => 'left'],[
                    'href' => '#list_allCategories'],
                    $this->view->translate('menu_short_categories')
            );
        }
        
        if ($this->_title === NULL)
        {
            $this->_title = ucfirst($this->view->translate($this->_categoryName));
            
        }
        
        
    }
}
