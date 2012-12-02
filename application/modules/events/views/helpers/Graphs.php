<?php

class Events_View_Helper_Graphs extends Zend_View_Helper_Abstract
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
            if ((!$this->_all || in_array($formElement->getId(),$this->_commonElement)))
            {
                $options[] = [
                    'id'        => $this->_getId($formElement),
                    'buttons'   => $this->_buttonGroup(),
                    'active'    => $formElement->getId(),
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
    
    protected function _buttonGroup()
    {
        $buttons = '';
        foreach ($this->_form->getElements() as $formElement)
        {
            if ($formElement->getId() === 'date')
            {
                $href = 'graphs-page';
                $label = $this->view->translate('item_frequency');
            }
            else
            {
                $href = $this->_getId($formElement);
                $label = ucfirst($formElement->getLabel());
            }
            

            //if no category is choosen show only fot the common properties
            if (method_exists($formElement,'dataChart') &&
                (!$this->_all 
                || in_array($formElement->getId(),$this->_commonElement)))
            {
                $buttons .='<a href="#'
                            .$href
                            .'" data-role="button">'
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
