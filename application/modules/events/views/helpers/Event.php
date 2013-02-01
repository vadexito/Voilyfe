<?php

/**
 * 
 * event view helper with several sub function
 * 
 */


class Events_View_Helper_Event extends Zend_View_Helper_HtmlElement
{
    protected $_pathIconCategory = NULL;
    protected $_pathIconItem = NULL;
    
    protected $_event;
    protected $_model;
    
    protected $_filterDate;
    protected $_localizedMonths;
    protected $_localizedWeekDays;
    
    
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
    
    public function getForm()
    {
        $this->_initModel();
        
        return $this->_model->getForm('insert',array(
            'containerId' => $this->_event->category->id,
            'containerType' => 'category',
            'model'         => $this->_model
        ));
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
        $commonProperties = $this->commonProperties();
        
        
        $title = $commonProperties['date'];
        if ($this->view->all)
        {
            $title .= ' - '.ucfirst($this->view->translate($categoryName));
        }
        unset($commonProperties['date']);
        
        
        return $this->renderLine([
            'title'         => $title,
            'subTitle'      => $this->renderProperties($commonProperties),
            'content'       => $this->renderProperties($this->specificProperties()),
            'href'          =>$this->getHref($this->view->all),
            'aside'         => ''
        ]);
    }
    
    public function eventForFront()
    {
        $categoryName = 'category_'.$this->_event->category->name;
        $category = $this->view->all ? ucfirst($this->view->translate($categoryName)) : NULL;
        $date = $this->_event->date;
        
        return [
            'id'                    => $this->_event->id,
            'eventId'               => $this->_event->id,
            'categoryId'            => $this->_event->category->id,
            'href'                  => '#',
            'userImageSrc'          => $this->getThumbnailSrc(),
            'title'                 => $category,
            'category'              => ucfirst($this->view->translate($categoryName)),
            'commonProperties'      => $this->commonProperties(),
            'specificProperties'    => $this->specificProperties(),
            'aside'                 => '',
            'W3CDate'               => $date->format(DateTime::W3C),
            'year'                  => $date->format('Y'),
            'month'                 => $this->getLocalizedMonth()['format']['wide'][$date->format('n')],
            'day'                   => $date->format('d'),
            'weekDay'               => $this->getLocalizedWeekDays()['format']['wide'][strtolower($date->format('D'))],
            'latitude'              => ($this->_event->location 
                    && property_exists($this->_event->location,'latitude')) ?
                    $this->_event->location->latitude : '',
            'longitude'              => ($this->_event->location 
                    && property_exists($this->_event->location,'longitude')) ?
                    $this->_event->location->longitude : '',
            'address'              => ($this->_event->location 
                    && property_exists($this->_event->location,'address')) ?
                    $this->_event->location->address : '',
        ];
    }
              
    protected function _initModel()
    {
        $this->_model = new Events_Model_Events();
    }
    
    
    public function getHref($allOption)
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
    public function getThumbnailSrc()
    {
        $event = $this->_event;
        if ($event->image && $event->image->path)
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
    public function renderLine($options)
    {
        extract($options); //title,subtitle,content,href,aside
        
        return '<li class="event-line" data-icon="false">'.$this->view->partial('partial/_eventListItem.phtml',[
            'href'                  => $href,
            'eventId'               => $this->_event->id,
            'imgSrc'                => $this->getThumbnailSrc(),
            'title'                 => $title,
            'commonProperties'      => $subTitle,
            'specificProperties'    => $content,
            'aside'                 => $aside,
        ]).'</li>'."\n";
        
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
        
        
        return '<img '. $idProperty . $altProperty .' src="'.$this->getThumbnailSrc().'" />';
    }
    
    /**
     * return array from speicifc properties of the event
     * 
     * @param array $array
     */
    public function specificProperties()
    {
        $event = $this->_event;
        $properties = [];
        foreach ($event->category->items as $item)
        {
            $property = Events_Model_Events::getPropertyName($event->category->name,$item->name);
            $properties[$item->name]= [
                'value'     => $this->view->translate(Pepit_Doctrine_Tool::toString($this->_event,$property)
                ),
                'srcIcon'   => $this->_getSrcIcon($property)
            ];
        }
        
        return $properties;
    }
    
    public function localDate($format = NULL)
    {
        if ($format === NULL)
        {
            $format = Zend_Date::DATE_MEDIUM;
        }
        
        if (!$this->_filterDate)
        {
            $this->_filterDate = new Zend_Filter_NormalizedToLocalized();
        }
        $this->_filterDate->setOptions(['date_format' => $format]);
        
        $value = $this->_event->date;
        
        return $this->_filterDate->filter([
            'day'       => $value->format('d'),
            'month'     => $value->format('m'),
            'year'      => $value->format('Y')
        ]);
    }
    /**
     * return array from common properties location, persons, date, tasg
     * 
     * @param boolean $includeDate whether or not to include the date in the 
     * property set
     */
    public function commonProperties($includeDate= true)
    {
        $properties = [];
        if ($includeDate)
        {
            $properties['date'] = $this->localDate();
        }
        
        foreach (['location','persons','tags'] as $item)
        {
            $properties[$item]= [
                'value'     => $this->view->translate(Pepit_Doctrine_Tool::toString($this->_event,$item)),
                'srcIcon'   => $this->_getSrcIcon($item),
            ];
        }
        
        return $properties;
    }
    
    public function renderProperties($properties)
    {
        $html = [];
        foreach ($properties as $property)
        {
            if ($property['value'])
            {
                $html[]= '<img src="'
                . $property['srcIcon'] 
                .'" style="width:15px;height:15px;"/> '
                .$this->view->escape($property['value']);
            }
        }
        
        return implode(', ',$html);
    }
    
    protected function _getSrcIcon($property)
    {
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
        
        return $src;
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
    
    public function initLocalizedMonth()
    {
        if ($this->_localizedMonths === NULL)
        {
            $this->_localizedMonths = Zend_Registry::get('Zend_Locale')->getTranslationList('Months');
        }
    }
    
    public function getLocalizedMonth()
    {
        if ($this->_localizedMonths === NULL)
        {
            $this->initLocalizedMonth();
        }
        return $this->_localizedMonths;
    }
    
    public function initLocalizedWeekDays()
    {
        if ($this->_localizedWeekDays === NULL)
        {
            $this->_localizedWeekDays = Zend_Registry::get('Zend_Locale')->getTranslationList('Days');
        }
    }
    
    public function getLocalizedWeekDays()
    {
        if ($this->_localizedWeekDays === NULL)
        {
            $this->initLocalizedWeekDays();
        }
        return $this->_localizedWeekDays;
    }
    
    
    
    
}
