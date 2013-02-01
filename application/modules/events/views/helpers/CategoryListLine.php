<?php

class Events_View_Helper_CategoryListLine extends Pepit_View_Helper_Abstract
{
    
    protected $_href = NULL;
    protected $_imgAttribs = NULL;
    protected $_subTitle = '';
    protected $_categoryName = '';
    protected $_nbEvents = '';
    protected $_bubbleCount = true;
    protected $_lineSplit = false;
    protected $_categoryId = NULL;
    protected $_iconEndOfLine = 'arrow-r';
    
    protected $_possibleOptions = ['href','imgAttribs','subTitle','categoryName',
            'nbEvents','bubbleCount','lineSplit','categoryId','iconEndOfLine'];
    
    public function categoryListLine($options)
    {
        $this->_resetOptions();
        $this->_loadOptions($options);
        $this->_loadDefaultOptions();
        
        $optionsRender=[
            'href'              => $this->_href,
            'imgAttribs'        => $this->_imgAttribs,
            'subTitle'          => $this->_subTitle,
            'categoryName'      => $this->_categoryName,
            'nbEvents'          => $this->_nbEvents,
            'bubbleCount'       => $this->_bubbleCount,
            'lineSplit'         => $this->_lineSplit,
            'categoryId'        => $this->_categoryId,
            'iconEndOfLine'     => $this->_iconEndOfLine        
        ];
        
        
        return array(
            'render' => $this->_render($optionsRender),
            'countEvent' => $this->_nbEvents,
        );
    }
    
    
    protected function _loadDefaultOptions()
    {
        
    }
    
    
    
    protected function _resetOptions()
    {
        $this->_href = NULL;
        $this->_imgAttribs = NULL;
        $this->_subTitle = '';
        $this->_categoryName = '';
        $this->_nbEvents = '';
        $this->_bubbleCount = true;
        $this->_lineSplit = false;
        $this->_categoryId = NULL;
        $this->_iconEndOfLine = 'arrow-r';
    }
    
    protected function _render($options)
    {
        extract($options); //options: href, imgAttribs, categoryName, nbEvents, bubbleCount, lineSplit, categoryId, iconEndOfLine
        if (($nbEvents == 0) && $lineSplit)
        {
            $href = $hrefCreate;
        }
        
        $content = '';
        if ($imgAttribs)
        {
            $content.= '<img '. $this->_htmlAttribs($imgAttribs) .'/>';
        }
        
        $content = '<a href="'.$href.'" data-ajax="false">'
                    .$content
                    .'<h3>'.$this->view->translate($categoryName). '</h3>'."\n"
                    .'<p>'. $subTitle .'</p>'
                .($bubbleCount ? '<span class="ui-li-count">'.$nbEvents.'</span>'."\n":null)
                ."\t". '</a>';
        
        if ($lineSplit)
        {
            $hrefCreate = $this->view->url(array('action' => 'create','containerId' => $categoryId),'event');
            $content.= '<a data-ajax = "false" href="'. $hrefCreate .'"></a>'."\n";
        }
        
        return "\t".'<li data-icon="'.$iconEndOfLine.'">'.$content."\t".'</li>'."\n";
    }
}
