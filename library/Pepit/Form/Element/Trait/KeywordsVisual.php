<?php


trait Pepit_Form_Element_Trait_KeywordsVisual
{
    
    protected $_tagIdProperty = NULL;
    
    public function dataChart($events)
    {
                
        $tags = [];
        $property = $this->getAttrib('data-property-name');
        $propertyTag = $this->getTagIdProperty();
        
        foreach ($events as $event)
        {
            if ($event->$property)
            {
                if (!method_exists($event->$property,'count'))
                {
                    $tagValue = ucfirst($this->getTranslator()->translate($event->$property->$propertyTag));
                    $tags = $this->_addTag($event, $tags, $tagValue);
                    
                }
                else
                //case of an array collection
                {
                    foreach ($event->$property as $tag)
                    {
                        $tags = $this->_addTag(
                                $event,
                                $tags,
                                ucfirst($this->getTranslator()->translate($tag->$propertyTag))
                        );
                    }
                }
            }
        }
        
        uasort($tags,function($a,$b){
            return $b['count'] - $a['count'];
        });
      
        return [
            'type'  =>'winner_list',
            'title' => ucfirst($this->getLabel()),
            'values'=> $tags
        ];
        
    }
    
    protected function _addTag($event,$tags,$tag)
    {
        if ($tag)
        {
            if (key_exists($tag,$tags))
            {
                $tags[$tag]['count']++;
                $tags[$tag]['events'][] = $event->id;
            }
            else
            {
                $tags[$tag] = [
                    'count' => 1,
                    'events' => [$event->id],
                ];
            }
        }
        
        return $tags;
    }
    
    public function setTagIdProperty($property = NULL)
    {
        if ($property)
        {
            $this->_tagIdProperty = $property;
        }
        else if (!$this->_tagIdProperty)
        {
            if (!method_exists($this,'getContainerType'))
            {
                throw new Pepit_Form_Exception('The element '.get_class($this). 'must contain either tagid property or contaniertype');
            }
            
            if ($this->getContainerType() === 'item')
            {
                $this->_tagIdProperty = 'value';
            }
            else if ($this->getContainerType() === 'itemGroup')
            {
                $this->_tagIdProperty = $this->getItemName().'_name';
            }
        }
        
        return $this;
    }
    
    public function getTagIdProperty()
    {
        if ($this->_tagIdProperty === NULL)
        {
            $this->setTagIdProperty();
        }
        return $this->_tagIdProperty;
    }
    
}

