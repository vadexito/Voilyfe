<?php

class Events_View_Helper_Graphs extends Zend_View_Helper_HtmlElement
{
    protected $_events;
    protected $_all;
    protected $_commonElement = ['date','location','image','tags','persons'];
    protected $_form;
    
    
    
    public function graphs($events,$all)
    {
        $this->_events = $events;
        $this->_all = $all;
        $this->_form = $this->view->event($this->_events[0])->getForm();
        
        foreach ($this->_form->getElements() as $formElement)
        {
            //if no category is choosen show only fot the common properties
            if ($this->_hasShow($formElement))
            {
                $options[] = [
                    'id'        => $this->_getId($formElement),
                    'buttons'   => $this->_buttonGroup($formElement),
                    'active'    => 'graphs',
                    'graph'     => $this->view->visualRep(
                            $formElement,
                            $events,
                            ['htmlTag' => 'div']
                    )
                ];
            }
        }
        $options[0]['id'] = 'graphs-page';
        return $options;
    }
    
    protected function _hasShow($formElement)
    {
        return (!$this->_all || 
                in_array($formElement->getId(),$this->_commonElement));
    }
    protected function _buttonGroup($activeElement)
    {
        $buttons = '';
        foreach ($this->_form->getElements() as $formElement)
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
        
            //if no category is choosen show only for the common properties
            if (method_exists($formElement,'dataChart') &&
                ($this->_hasShow($formElement)))
            {
                if ($formElement->getId() === $activeElement->getId())
                {
                    $attribs['class'] = 'ui-btn-active';
                }

                $buttons .='<a '.$this->_htmlAttribs($attribs).'>'
                            .$label.'</a>'."\n";
            }
        }
        
        return $buttons;
        
    }
    
    protected function _getId($formElement)
    {
        return 'graph_'.$formElement->getId();
    }
}
