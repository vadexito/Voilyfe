<?php

class Events_View_Helper_SubPageIndex extends Zend_View_Helper_Abstract
{
    
    protected $_name = NULL;
    protected $_category;
    
    protected $_buttonRight = NULL;
    protected $_buttonLeft = NULL;
    protected $_active;
    protected $_content = '';
    protected $_footer = NULL;
    protected $_title = NULL;
    
    protected $_possibleOptions = ['active','content','footer','title',
            'buttonRight','buttonLeft'];
    
    public function subPageIndex($category,$id,$options)
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
               'footer'     => $this->_footer 
            ]
        );
    }
    
    protected function _loadOptions($options)
    {
        foreach ($this->_possibleOptions as $option)
        {
            if (key_exists($option, $options))
            {
                $method = 'set'.ucfirst($option);
                if (method_exists($this,$method))
                {
                    $this->$method($options[$option]);
                }
                else
                {
                    $prop = '_'.$option;
                    $this->$prop = $options[$option];
                }
            }
        }
    }
    
    
    protected function _loadDefaultOptions()
    {
        if (!$this->_footer)
        {
            $this->_footer = '_footerCategories.mobile';
        }
        
        if (!$this->_buttonRight)
        {
            if ($this->_category->name !=='all')
            {
                $this->_name = 'category_'.$this->_category->name;
                $hrefButtonAdd = $this->view->url(array('action' => 'create','containerId' => $this->_category->id),'event');
            }
            else
            {
                $this->_name = 'menu_events';
                $hrefButtonAdd = '#list_singleCategories';
            }
            
            $this->_buttonRight = '<a data-theme="b" href="'
            . $hrefButtonAdd 
            .'" data-iconpos="notext" data-icon="plus" data-ajax="false" class="ui-btn-right"></a>';
        }
        
        if (!$this->_buttonLeft)
        {
            $this->_buttonLeft = '<a data-theme="b" href="#list_allCategories" data-ajax="false" class="ui-btn-left">'
                .$this->view->translate('menu_short_categories').'</a>';
        }
        
        if (!$this->_title)
        {
            $this->_title = ucfirst($this->view->translate($this->_name));
        }
        
        
    }
    
    public function setButtonLeft($option)
    {
        $this->_buttonLeft = $this->view->buttonHeader($option);
    }
}
