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
        
        $this->setPathIconCategory()->setPathIconItem();
        $categoryName = 'category_'.$event->category->name;
   
        return $this->renderLine(
            ucfirst($this->view->translate($categoryName)),
            $this->renderCommonProperties(),
            $this->renderSpecificProperties(),
            $this->_getHref($this->view->all)
        );
    }
    
    protected function _getHref($allOption)
    {
        $options = [
            'action'=>'show','containerId'=>$this->_event->category->id,
            'containerRowId' => $this->_event->id
        ];
        if ($allOption)
        {
            $options['all'] = $allOption;
        }
        
        return $this->view->url(
            $options,
            'event'
        );
    }
    protected function _getThumbnailSrc()
    {
        $event = $this->_event;
        if ($event->image)
        {
            return $this->view->url(['image'=> $this->_event->image->path,'controller' => 'image','action' => 'show'],'member');
        }
        return sprintf(
            $this->_pathIconCategory,
            ucfirst($event->category->name)
        );
    }
    
    public function setPathIconItem()
    {
        if (!$this->_pathIconItem)
        {
            $this->_pathIconItem = Zend_Registry::get('config')->public->images
                                        ->icon->item->inline->path;
        }
        return $this;
    }
    
    public function getPathIconItem()
    {
        $this->setPathIconItem();
        return $this->_pathIconItem;
    }
    
    public function setPathIconCategory()
    {
        if (!$this->_pathIconCategory)
        {
            $this->_pathIconCategory = Zend_Registry::get('config')->public->images
                                        ->icon->category->path;
        }
        return $this;
    }

    public function getPathIconCategory()
    {
        $this->setPathIconCategory();
        return $this->_pathIconCategory;
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
    public function renderLine($title,$subTitle,$content,$href)
    {
        return '<li class="event-line">
        <a data-ajax="false" class="event-line-link" href="'.$href.'">'
            .$this->renderUserImageThumbnail()
            .'<h3>'. $title .'</h3>
            <p><strong>'.$subTitle.'</strong></p>
            <p>'.$content.'</p>
            <p class="ui-li-aside"><strong>3 star</strong></p>
        </a>
    </li>'."\n";
    }
    
    /**
     * 
     * @param array $options possible option id and alt
     * @return string
     */
    public function renderUserImageThumbnail($options = NULL)
    {
        $idProperty = '';
        $altProperty = '';
        if (is_array($options))
        {
            if (array_key_exists('id', $options))
            {
                $idProperty = ' id="'.$options['id'].'" ';
            }
            if (array_key_exists('alt', $options))
            {
                $altProperty = ' alt="'.$options['alt'].'" ';
            }
        }
        
        
        return '<img '. $idProperty . $altProperty .' src="'.$this->_getThumbnailSrc().'" />';
    }
    
    /**
     * return array from speicifc properties of the event
     * 
     * @param array $array
     */
    public function specificProperties()
    {
        $event = $this->_event;
        $properties = array();
        foreach ($event->category->items as $item)
        {
            $property = Events_Model_Events::getPropertyName($event->category->name,$item->name);
            $properties[$item->name]= $this->_renderPropertyWithIcon($event, $property);
        }
        
        return $properties;
    }
    
    
    public function renderSpecificProperties()
    {
        return implode(', ',array_filter(array_values($this->specificProperties())));
    }
    
    
    public function localDate($format = NULL)
    {
        if ($format === NULL)
        {
            $format = Zend_Date::DATE_MEDIUM;
        }
        
        $filterDate = new Pepit_Filter_DateTimeToDateForm(array(
            'date_format' => $format
        ));
        
        return $filterDate->filter($this->_event->date);
    }
    /**
     * return array from common properties location, persons, date, tasg
     * 
     * @param array $array
     */
    public function commonProperties()
    {
        $properties = [];
        
        $properties['date'] = $this->localDate();
        
        foreach (['location','persons','tags'] as $item)
        {
            $properties[$item]= $this->_renderPropertyWithIcon($this->_event,$item);
        }
        
        return $properties;
    }
    
    
    public function renderCommonProperties()
    {
        return implode(', ',array_filter(array_values($this->commonProperties())));
    }
    
    protected function _renderPropertyWithIcon($event,$property)
    {
        $string = Pepit_Doctrine_Tool::toString($event,$property);
        if (!$string)
        {
            return '';
        }
        
        //try with the property name (specific property)
        $relativePath = sprintf($this->getPathIconItem(),ucfirst($property));
        $iconFile = APPLICATION_PATH.'/../public'.$relativePath;
        if (file_exists($iconFile))
        {
            $src = $relativePath;
        }
        //if not use the non specific property name (without category namespace)
        else
        {
            $property = preg_replace('#^(.*)_(.*)$#','$2',$property);
            $src = sprintf($this->getPathIconItem(),$property);
        }
        
        return '<img src="'
            . $src 
            .'" style="width:15px;height:15px;"/> '
            .$string ;
    }
    
    public function badgeCatDate()
    {
        $logoCategorySrc = sprintf(
            $this->getPathIconCategory(),
            ucfirst($this->getEvent()->category->name)
        );
        
        $badge = 
        '<a data-role="button" data-theme="b">'
        .'<p>'
        .'<small>'.$this->getEvent()->category->name.'</small>'
        .'<img src="'.$logoCategorySrc.'" width="20px"/>'
        .'</p>'
        .'<h1>'.$this->localDate(Zend_Date::DAY).'<h1/>'
        .'<h2>'.$this->localDate(Zend_Date::MONTH_NAME_SHORT).'<h2/>'
        .'</a>'."\n"; 
        
        return $badge;
    }
    
    public function subHeaderDate()
    {
        $subHeader = 
        '<ul data-role="listview" data-theme="d" data-divider-theme="d">'
        . '<li data-role="list-divider"><h3 id="year-header" class="date">'
        .$this->localDate(Zend_Date::YEAR)
        . $this->renderLogoCategory('50px')
        .'</h3></li>'
        . '<li class="lineWithCatIcon"><h1 id="day-header" class="date">'.$this->localDate(Zend_Date::DAY).'</h1>'
        . '<div id="month-day">'
        .'<h2 id="month-header" class="date">'.$this->localDate(Zend_Date::MONTH_NAME).'</h2>'
        . '<h3 id="weekday-header" class="date">'.$this->localDate(Zend_Date::WEEKDAY).'</h3>'."\n".'</div>'
        . '</li><li data-role="list-divider"></li></ul>' . "\n"; 
        
        return $subHeader;
    }
    
    
    public function renderLogoCategory($width)
    {
        $logoCategorySrc = sprintf(
            $this->getPathIconCategory(),
            ucfirst($this->getEvent()->category->name)
        );
        
        return '<img class="icon-category" src="'.$logoCategorySrc.'" width="' . $width . '"/>'."\n";
    }
    
    
    public function __get($name)
    {
        if (method_exists($this,$name))
        {
            return $this->$name();
        }
        return $this->$name;
    }
    
    public function getEvent()
    {
        return $this->_event;
    }
    
    
}
