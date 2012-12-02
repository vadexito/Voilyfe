<?php

class Events_View_Helper_SubPageIndex extends Zend_View_Helper_Abstract
{
    
    protected $_name = NULL;
    protected $_buttonRight = NULL;
    protected $_buttonLeft = NULL;
    
    public function subPageIndex($category,$id,$page,$content,$footer='',$title = NULL)
    {
        $this->_setButtons($category);
        
        if ($title === NULL)
        {
            $title = ucfirst($this->view->translate($this->_name));
        }
        
        
        return $this->view->partial(
                'partial/_mobilePage.phtml', 
                [
                   'id'         => $id,
                   'page'       => $page,
                   'content'    => $content,
                   'title'      => $title,
                   'buttonLeft' => $this->_buttonLeft.$this->view->translate('menu_short_categories').'</a>',
                   'buttonRight'=> $this->_buttonRight,
                   'footer'     => $footer ? $footer : '_footerCategories.mobile'
                ]) ;
            }
            
    protected function _setButtons($category)
    {
        if (!$this->_buttonLeft)
        {
             if ($category->name !=='all')
            {
                $this->_name = 'category_'.$category->name;
                $hrefButtonAdd = $this->view->url(array('action' => 'create','containerId' => $category->id),'event');
            }
            else
            {
                $this->_name = 'menu_events';
                $hrefButtonAdd = '#list_singleCategories';
            }
            $hrefButtonAdd = '';
            $this->_buttonRight = '<a data-theme="b" href="'
            . $hrefButtonAdd 
            .'" data-iconpos="notext" data-icon="plus" data-ajax="false" class="ui-btn-right"></a>';

           $this->_buttonLeft = '<a data-theme="b" href="#list_allCategories" data-ajax="false" class="ui-btn-left">';
        }
    }
}
