<?php

/**
 * 
 * event view helper with several sub function
 * 
 */


class Events_View_Helper_Event extends Zend_View_Helper_Abstract
{
    protected $_pathIconCategory = NULL;
    protected $_pathIconItem = NULL;
    
    protected $_event;
    
    /**
     * initialize helper
     * 
     * @param Entity $event
     * @return \Events_View_Helper_Event
     */
    public function event($event)
    {
        $this->_event = $event;
        return $this;
    }
    
    /**
     * show single line of event (<li> tag)
     * 
     * @return string
     * @throws Pepit_View_Exception
     */
    public function eventLine()
    {
        $event = $this->_event;
        if (!is_object($event))
        {
            throw new Pepit_View_Exception('Parameter of helper eventline should be an entity object');
        }
        $this->_event = $event;
        
        $this->setPathsIcon();
        $categoryName = 'category_'.$event->category->name;
        
        return $this->renderLine(
            ucfirst($this->view->translate($categoryName)),
            $this->renderCommonProperties(),
            $this->renderSpecificProperties(),
            $this->_getHref(),
            $this->_getThumbnailSrc()
        );
    }
    
    protected function _getHref()
    {
        return $this->view->url(
            ['action'=>'show','containerId'=>$this->_event->category->id,'containerRowId' => $this->_event->id],
            'event'
        );
    }
    protected function _getThumbnailSrc()
    {
        $event = $this->_event;
        if ($event->image)
        {
            return "/bin/imagesUser.php?image=". APPLICATION_PATH.$event->image ."&width=80&height=80";
        }
        return sprintf(
            $this->_pathIconCategory,
            ucfirst($event->category->name)
        );
    }
    
    public function setPathsIcon()
    {
        if (!$this->_pathIconCategory)
        {
            $this->_pathIconCategory = Zend_Registry::get('config')->public->images
                                        ->icon->category->path;
        }
        if (!$this->_pathIconItem)
        {
            $this->_pathIconItem = Zend_Registry::get('config')->public->images
                                        ->icon->item->inline->path;
        }
    }
    
    /**
     * pure view helper form rendering line
     * 
     * @param string $title
     * @param string $subTitle
     * @param string $content
     * @param string $href
     * @param string $imgSrc
     * @return string
     */
    public function renderLine($title,$subTitle,$content,$href,$imgSrc)
    {
        return '<li class="event-line">
        <a data-ajax="false" class="event-line-link" href="'.$href.'">
            <img src="'.$imgSrc.'" />
            <h3>'. $title .'</h3>
            <p><strong>'.$subTitle.'</strong></p>
            <p>'.$content.'</p>
            <p class="ui-li-aside"><strong>3 star</strong></p>
        </a>
    </li>'."\n";
    }
    
    
    public function renderSpecificProperties()
    {
        $event = $this->_event;
        $properties = array();
        foreach ($event->category->items as $item)
        {
            $property = Events_Model_Events::getPropertyName($event->category->name,$item->name);
            $properties[]= $this->_renderPropertyWithIcon($event, $property);
        }
        
        return implode(', ',array_filter($properties));
    }
    
    /**
     * return array from common properties location, persons, date, tasg
     * 
     * @param array $array
     */
    public function getArrayCommonProperties()
    {
        $properties = [];
        
        $event = $this->_event;
        $filterDate = new Pepit_Filter_DateTimeToDateForm(array(
            'date_format' => Zend_Date::DATE_MEDIUM
        ));
        
        $properties['date'] = $filterDate->filter($this->_event->date);
        
        foreach (['location','persons','tags'] as $item)
        {
            $properties[$item] = Pepit_Doctrine_Tool::toString($this->_event,$item);
        }
        
        return $properties;
    }
    public function renderCommonProperties()
    {
        return implode(', ',array_filter(array_values($this->getArrayCommonProperties())));
    }
    
    protected function _renderPropertyWithIcon($event,$property)
    {
        $string = Pepit_Doctrine_Tool::toString($event,$property);
        if (!$string)
        {
            return '';
        }
        
        //try with the property name (specific property)
        $relativePath = sprintf($this->_pathIconItem,ucfirst($property));
        $iconFile = APPLICATION_PATH.'/../public'.$relativePath;
        if (file_exists($iconFile))
        {
            $src = $relativePath;
        }
        //if not use the non specific property name (without category namespace)
        else
        {
            $property = preg_replace('#^(.*)_(.*)$#','$2',$property);
            $src = sprintf($this->_pathIconItem,$property);
        }
        
        return '<img src="'
            . $src 
            .'" style="width:15px;height:15px;"/> '
            .$string ;
    }
}
