<?php

class Events_View_Helper_Graphs extends Zend_View_Helper_HtmlElement
{
    protected $_events;
    
    /**
     * @var boolean of a specific category is choosen (false) 
     * or if all category together are shown
     */
    protected $_all;
    
    protected $_commonElement = ['date','location','image','tags','persons'];
    protected $_form;
    protected $_elementsToShow = NULL;
    protected $_model = NULL;


    protected $_options = [];
    
    public function graphs($events = NULL,$all = NULL)
    {
        if ($events === NULL && $all === NULL)
        {
            return $this;
        }
        
        
        $this->_events = $events;
        $this->_all = $all;
        
        
        $this->_form = $this->getModel()->getForm('insert',[
            'containerId' => $this->_events[0]->category->id,
            'containerType' => 'category',
            'model'         => $this->getModel()
        ]);     
        $this->_form->getElement('date')->setLabel('item_frequency');
        
        foreach ($this->getElementsToShowAndInitOptions() as $formElement)
        {
            
            $this->_options[$this->_getId($formElement)]['buttons'] = $this->_buttonGroup($formElement);
        }
        
        $options = array_values($this->_options);
        
        return $options;
    }
    
    /**
     * defines the graphs which will be show in the grahs page
     * @return array of element to be shown
     */
    public function getElementsToShowAndInitOptions()
    {
        if ($this->_elementsToShow === NULL)
        {
            foreach ($this->_form->getElements() as $formElement)
            {
                
                // if no specific category only common element else
                // all the elements
                if (!$this->_all || 
                    in_array($formElement->getId(),$this->_commonElement)) 
                    
                {
                    //generate graph
                    $graph = $this->view->visualRep(
                            $formElement,
                            $this->_events,
                            ['htmlTag' => 'div']
                    );
                    
                    //if there is a graph to be shown update _option property
                    if ($graph)
                    {
                        $id = $this->_getId($formElement);
                        $this->_options[$id] = [
                            'id'        => $id,
                            'active'    => 'graphs',
                            'graph'     => $graph,
                            'title'     => $formElement->getLabel()
                        ];
                    } 
                    else
                    {
                        $this->_form->removeElement($formElement->getId());      
                    }
                }
                else
                {
                    $this->_form->removeElement($formElement->getId());             
                }
            }
            $this->_elementsToShow = $this->_form->getElements();
                        
        }
        
        return $this->_elementsToShow;
        
        
    }
    
    public function getListElementsToShow()
    {
        return $this->_buttonGroup();
    }
    
    
    protected function _buttonGroup($activeElement = NULL)
    {
        $buttons = '';
        foreach ($this->getElementsToShowAndInitOptions() as $formElement)
        {
            $attribs = [];
            $attribs['href'] = '#'.$this->_getId($formElement);
            
            $label = ucfirst($formElement->getLabel());
            
            $icon = '';
            if ($formElement->getAttrib('data-visualrep'))
            {
                $src = [
                    'tags'          => '/images/icons/nav_bar/tags.png',
                    'google_chart'  => '/images/icons/nav_bar/graphs.png',
                ];
                
                $icon = '<img src="'.$src[$formElement->getAttrib('data-visualrep')].'" alt="France" class="ui-li-icon">';
            }
            
            if ($activeElement && 
                    ($formElement->getId() === $activeElement->getId()))
            {
                $attribs['class'] = 'ui-btn-active';
            }

            $buttons .='<li><a '.$this->_htmlAttribs($attribs).'>'
                        .$icon.$label.'</a></li>'."\n";
        }
        
        return $buttons;
        
    }
    
    protected function _getId($formElement)
    {
        return 'graph_'.$formElement->getId();
    }
    
    public function getModel()
    {
        if ($this->_model === NULL)
        {
            $this->_model = new Events_Model_Events();
        }
        return $this->_model;
    }
}
