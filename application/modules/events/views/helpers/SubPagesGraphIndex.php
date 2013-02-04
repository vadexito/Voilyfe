<?php

class Events_View_Helper_SubPagesGraphIndex extends Pepit_View_Helper_Abstract
{
    
    protected $_name = NULL;
    protected $_category;
    
    protected $_id = NULL;
    protected $_buttonRight = NULL;
    protected $_buttonLeft = NULL;
    protected $_active;
    protected $_footer = NULL;
    protected $_title = NULL;
    protected $_graph = NULL;
    protected $_buttons = NULL;
    
    protected $_possibleOptions = ['id','graph','buttons','footer','title',
            'buttonRight','buttonLeft','active'];
    
    
    public function subPagesGraphIndex($category,array $options)
    {
        if (!is_object($category))
        {
            throw new Pepit_View_Exception('A entity category has to be provided for subpageindex viewer');
        }
        $this->_category = $category;
        
        $loopOptions = [];
        foreach ($options as $optionArray)
        {
            
            if (!key_exists('id',$optionArray))
            {
                throw new Pepit_View_Exception('You should specify array of array with id for subPageIndex');
            }
            $this->_loadOptions($optionArray);
            $this->_loadDefaultOptions();
            
            $loopOptions[] = [
                   'id'         => $this->_id,
                   'active'     => $this->_active,
                   'buttons'    => $this->_buttons,
                   'graph'      => $this->_graph,
                   'title'      => $this->_title,
                   'buttonLeft' => $this->_buttonLeft,
                   'buttonRight'=> $this->_buttonRight,
                   'footer'     => $this->_footer
            ];
        }
        
        return $this->view->partialLoop('partial/_pageVisualRep-mobile.phtml',$loopOptions);
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
            
            $this->_buttonRight = $this->view->htmlMobileButtonNavBar(['position' => 'right','type' => 2],[
                    'href' => $hrefButtonAdd]
            );
           
        }
        
        if (!$this->_buttonLeft)
        {
            $this->_buttonLeft = $this->view->htmlMobileButtonNavBar(['position'=>'left','type' => 
            Pepit_View_Helper_HtmlMobileButtonNavBar::TYPE_BUTTON_BACK],[],
                ucfirst($this->view->translate('menu_back_to_previous_page')));
        }
        
        if ($this->_title === NULL)
        {
            $this->_title = ucfirst($this->view->translate($this->_name));
        }
        
        
    }
}
