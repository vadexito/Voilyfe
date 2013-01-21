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
    
    protected $_options = [];
    
    public function graphs($events,$all)
    {
        $this->_events = $events;
        $this->_all = $all;
        
        $this->_form = $this->view->event($this->_events[0])->getForm();
        
        foreach ($this->getElementsToShowAndInitOptions() as $formElement)
        {
            
            $this->_options[$this->_getId($formElement)]['buttons'] = $this->_buttonGroup($formElement);
        }
        
        $this->_options['graph_date']['id'] = 'graphs-page';
        $options = array_values($this->_options);
        return $options;
    }
    
    public function getElementsToShowAndInitOptions()
    {
        if ($this->_elementsToShow === NULL)
        {
            foreach ($this->_form->getElements() as $formElement)
            {
                if (!$this->_all || 
                    in_array($formElement->getId(),$this->_commonElement)) 
                    
                {
                    $graph = $this->view->visualRep(
                            $formElement,
                            $this->_events,
                            ['htmlTag' => 'div']
                    );
                    
                    if ($graph)
                    {
                        $this->_options[$this->_getId($formElement)] = [
                            'id'        => $this->_getId($formElement),
                            'active'    => 'graphs',
                            'graph'     => $graph
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
    protected function _buttonGroup($activeElement)
    {
        $buttons = '';
        foreach ($this->getElementsToShowAndInitOptions() as $formElement)
        {
            $attribs = ['data-role' => 'button'];
            
            if ($formElement->getId() === 'date')
            {
                $attribs['href'] = '#graphs-page';
                $label = $this->view->translate('item_frequency');
            }
            else
            {
                $attribs['href'] = '#'.$this->_getId($formElement);
                $label = ucfirst($formElement->getLabel());
            }
        
            if ($formElement->getId() === $activeElement->getId())
            {
                $attribs['class'] = 'ui-btn-active';
            }

            $buttons .='<a '.$this->_htmlAttribs($attribs).'>'
                        .$label.'</a>'."\n";
        }
        
        return $buttons;
        
    }
    
    protected function _getId($formElement)
    {
        return 'graph_'.$formElement->getId();
    }
}
