<?php

class Events_View_Helper_CategoryListLine extends Zend_View_Helper_HtmlElement
{
    
    public function categoryListLine($page,$imgPath,$bubbleCount=true,$lineSplit=false)
    {
        $categoryName = $page->getLabel();
        $category = preg_replace('#category_#','',$categoryName);
        $imgSrc = sprintf(
            $imgPath,
            ucfirst($category)
        );
        $nbEvents = count($page->events);
        $href = $page->getHref();
        
        return array(
            'render' => $this->_render($href,$imgSrc,$categoryName,$nbEvents,$bubbleCount,$lineSplit,$page->categoryId),
            'countEvent' => $nbEvents
        );
    }
    
    protected function _render($href,$imgSrc,$categoryName,
            $nbEvents,$bubbleCount,$lineSplit,$categoryId)
    {
        $hrefCreate = $this->view->url(array('action' => 'create','containerId' => $categoryId),'event');
        if (($nbEvents == 0) && $lineSplit)
        {
            $href = $hrefCreate;
        }
        
        return "\t".'<li>
            <a href="'.$href.'" data-ajax="false">
                <img src="'. $imgSrc .'"/>
                    <h3>'.$this->view->translate($categoryName). '</h3>'."\n"
                    .'<p></p>'
                .($bubbleCount ? '<span class="ui-li-count">'.$nbEvents.'</span>'."\n":null)
                ."\t". '</a>'
                .($lineSplit ? '<a data-ajax = "false" href="'. $hrefCreate .'"></a>'."\n":null)
                ."\t".'</li>'."\n";
    }
}
