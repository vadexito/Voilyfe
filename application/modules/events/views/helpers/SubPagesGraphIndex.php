<?php

class Events_View_Helper_SubPagesGraphIndex extends Zend_View_Helper_Abstract
{
    
    protected $_name = NULL;
    protected $_buttonRight = NULL;
    protected $_buttonLeft = NULL;
    protected $_defaultFooter;
    protected $_page = 'graphs';
    
    
    
    
    public function subPagesGraphIndex($category,array $options)
    {
        $this->_setButtons($category);
        $this->_setDefaultFooter();
        
        $loopOptions = [];
        foreach ($options as $option)
        {
            
            if (!key_exists('id',$option))
            {
                throw new Pepit_View_Exception('You should specify array of array for subPageIndex');
            }
            
            $loopOptions[] = [
                   'id'         => $option['id'],
                   'page'       => key_exists('page',$option) ? $option['page'] : '',
                   'buttons'    => key_exists('buttons',$option) ? $option['buttons'] : '',
                   'graph'      => key_exists('graph',$option) ? $option['graph'] : '',
                   'title'      => key_exists('title',$option) ? $option['title'] : ucfirst($this->view->translate($this->_name)),
                   'buttonLeft' => key_exists('buttonLeft',$option)? $option['buttonLeft'] :$this->_buttonLeft.$this->view->translate('menu_short_categories').'</a>',
                   'buttonRight'=> key_exists('buttonRight',$option)? $option['buttonRight'] :$this->_buttonRight,
                   'footer'     => key_exists('footer',$option) ? $option['footer'] : $this->_defaultFooter
            ];
        }
        
        return $this->view->partialLoop('partial/_pageGraph-mobile.phtml',$loopOptions);
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
    
    protected function _setDefaultFooter()
    {
        $pages = array(
            array('name' => 'last-events','translation' => $this->view->translate('menu_last_events')),
            array('name' => 'calendar','translation' => $this->view->translate('menu_calendar')),
            array('name' => 'graphs','translation' => $this->view->translate('menu_graphs')),
            array('name' => 'options','translation' => $this->view->translate('menu_options')),
        );
        $items = '';
        foreach ($pages as $page)
        {
            $name = $page['name'];
            $active = ($name === $this->_page)?'ui-btn-active ui-state-persist' : '';
            
            $items .= '
                    <li>
                        <a id="'.$name.'" href="#'
                    .$name.'-page" data-icon="mylife-'
                    .$name.'" data-transition="none" class="event-menu '
                    .$active.'">'
                    .ucfirst($page['translation'])
                    .'</a>'."\n"
                    .'</li>'."\n";   
        }
        
        
        $this->_defaultFooter = 
        '<div data-role="footer" data-position="fixed" data-id="footer" class="nav-glyphish" data-tap-toggle="false">
        <div data-role="navbar">
                <ul>'.$items.'</ul>
        </div><!-- /navbar -->
        </div><!-- /footer -->';
    }
}
