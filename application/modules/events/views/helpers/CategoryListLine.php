<?php

class Events_View_Helper_CategoryListLine extends Zend_View_Helper_HtmlElement
{
    
    public function categoryListLine($page,$imgPath,$bubbleCount=true,
            $lineSplit=false,$icon = 'arrow-r',$subTitle='')
    {
        $categoryName = $page->getLabel();
        $category = preg_replace('#category_#','',$categoryName);
        $imgSrc = sprintf(
            $imgPath,
            ucfirst($category)
        );
        $nbEvents = count($page->events);
        $href = $page->getHref();
        
        $options=[
            'href'              => $href,
            'imgSrc'            => $imgSrc,
            'subTitle'          => $subTitle,
            'categoryName'      => $categoryName,
            'nbEvents'          => $nbEvents,
            'bubbleCount'       => $bubbleCount,
            'lineSplit'         => $lineSplit,
            'categoryId'        => $page->categoryId,
            'icon'              => $icon        
        ];
        
        
        
        
        return array(
            'render' => $this->_render($options),
            'countEvent' => $nbEvents
        );
    }
    
    protected function _render($options)
    {
        extract($options); //options: href, imgSrc, categoryName, nbEvents, bubbleCount, lineSplit, categoryId, icon
        $hrefCreate = $this->view->url(array('action' => 'create','containerId' => $categoryId),'event');
        if (($nbEvents == 0) && $lineSplit)
        {
            $href = $hrefCreate;
        }
        
        return "\t".'<li data-icon="'.$icon.'">
            <a href="'.$href.'" data-ajax="false">
                <img src="'. $imgSrc .'"/>
                    <h3>'.$this->view->translate($categoryName). '</h3>'."\n"
                    .'<p>'. $subTitle .'</p>'
                .($bubbleCount ? '<span class="ui-li-count">'.$nbEvents.'</span>'."\n":null)
                ."\t". '</a>'
                .($lineSplit ? '<a data-ajax = "false" href="'. $hrefCreate .'"></a>'."\n":null)
                ."\t".'</li>'."\n";
    }
}
